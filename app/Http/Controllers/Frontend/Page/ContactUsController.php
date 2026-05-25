<?php

namespace App\Http\Controllers\Frontend\Page;

use App\Http\Controllers\Controller;
use App\Mail\ContactMail;
use App\Models\ContactUsContent;
use App\Models\Setting;
use App\Models\Connection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ContactUsController extends Controller
{
    public function index()
    {
        $contents = ContactUsContent::find(1);
        $settings = Setting::find(1);

        return view('frontend.pages.contact-us', [
            'contents' => $contents,
            'settings' => $settings
        ]);
    }

    public function store(Request $request)
    {
        $response = Http::asForm()->post('https://hcaptcha.com/siteverify', [
            'secret' => config('services.hcaptcha.secret'),
            'response' => $request->input('h-captcha-response'),
            'remoteip' => $request->ip(),
        ]);

        if(!optional($response->json())['success']) {
            Log::warning('hCaptcha verification failed', [
                'ip'       => $request->ip(),
                'activity' => 'login',
                'response' => $response->json(),
                'user_agent' => $request->userAgent(),
            ]);
            
            return redirect()->back()->withInput()->with('error', 'Captcha verification failed!');;
        }
        
        $validator = Validator::make($request->all(), [
            'phone' => 'required|regex:/^\+?[0-9]+$/',
            'question' => 'required|min:10|max:65535',
            'comments' => 'required|min:10|max:65535'
        ], [
            'phone.required' => 'The phone number field is required',
            'phone.regex' => 'The phone number is invalid',
            'question.required' => 'This question field is required.',
            'question.min' => 'The question must be at least 10 characters.',
            'question.max' => 'The question must not be greater than 65535 characters.',
            'comments.required' => 'The comments field is required.',
            'comments.min' => 'The comments must be at least 10 characters.',
            'comments.max' => 'The comments must not be greater than 65535 characters.'
        ]);

        if($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput()->with('error', 'Message sending failed!');
        }
        
        $connection = new Connection();
        $data = $request->except('middleware_language', 'middleware_language_name', 'g-recaptcha-response', 'h-captcha-response');
        $data['status'] = '1';
        $connection->create($data);

        $mail_data = [
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'question' => $request->question,
            'comments' => $request->comments
        ];

        send_email(new ContactMail($mail_data, 'user'), $request->email);
        send_email(new ContactMail($mail_data, 'admin'), config('app.admin_emails'));

        return redirect()->back()->with('success', 'Message sent successfully. We usually reply within 5 working days. Thank you for your patience.');
    }
}
