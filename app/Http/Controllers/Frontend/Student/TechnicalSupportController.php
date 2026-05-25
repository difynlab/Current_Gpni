<?php

namespace App\Http\Controllers\Frontend\Student;

use App\Http\Controllers\Controller;
use App\Models\TechnicalSupport;
use App\Models\TechnicalSupportReply;
use App\Mail\TechnicalSupportMail;
use App\Mail\TechnicalSupportReplyMail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TechnicalSupportController extends Controller
{
    public function index()
    {
        $student = Auth::user();

        return view('frontend.student.technical-supports.index', [
            'student' => $student
        ]);
    }

    public function store(Request $request)
    {
        $auth = Auth::user();

        $technical_support = new TechnicalSupport();
        $technical_support->user_id = $auth->id;
        $technical_support->subject = $request->subject;
        $technical_support->message = $request->message;
        $technical_support->status = '1';
        $technical_support->save();

        $mail_data = [
            'user_name' => $auth->first_name . ' ' . $auth->last_name,
            'user_email' => $auth->email,
            'subject' => $request->subject,
            'message' => $request->message
        ];

        send_email(new TechnicalSupportMail($mail_data, 'user'), $auth->email);
        send_email(new TechnicalSupportMail($mail_data, 'admin'), config('app.admin_emails'));

        return redirect()->back()->with('success', 'Message sent successfully. We usually reply within 5 working days. Thank you for your patience.');
    }

    public function histories()
    {
        $student = Auth::user();
        $technical_supports = TechnicalSupport::where('user_id', $student->id)->where('status', '1')->orderBy('id', 'desc')->get();

        foreach($technical_supports as $technical_support) {
            $last_reply = TechnicalSupportReply::where('technical_support_id', $technical_support->id)
                ->where('status', '1')
                ->orderBy('id', 'desc')
                ->first();

            if($last_reply && $last_reply->replied_by != auth()->user()->id) {
                $technical_support->replied = $last_reply;
            } else {
                $technical_support->replied = null;
            }
        }

        return view('frontend.student.technical-supports.histories', [
            'technical_supports' => $technical_supports,
            'student' => $student
        ]);
    }

    public function show(TechnicalSupport $technical_support)
    {
        $student = Auth::user();

        $technical_support_replies = TechnicalSupportReply::where('technical_support_id', $technical_support->id)->where('status', '1')->get();

        $technical_support->time_difference = Carbon::parse($technical_support->created_at)->diffForHumans();

        foreach($technical_support_replies as $technical_support_reply) {
            $technical_support_reply->user_viewed = '1';
            $technical_support_reply->save();

            $date_time_string = $technical_support_reply->date . ' ' . $technical_support_reply->time;
            $technical_support_reply->time_difference = Carbon::parse($date_time_string)->diffForHumans();
        }

        return view('frontend.student.technical-supports.show', [
            'technical_support' => $technical_support,
            'technical_support_replies' => $technical_support_replies,
            'student' => $student
        ]);
    }

    public function update(Request $request, TechnicalSupport $technical_support)
    {
        $auth = Auth::user();

        $technical_support_reply = new TechnicalSupportReply();
        $technical_support_reply->technical_support_id = $technical_support->id;
        $technical_support_reply->replied_by = $auth->id;
        $technical_support_reply->message = $request->message;
        $technical_support_reply->date = Carbon::now()->toDateString();
        $technical_support_reply->time = Carbon::now()->toTimeString();
        $technical_support_reply->admin_viewed = '0';
        $technical_support_reply->user_viewed = '1';
        $technical_support_reply->status = '1';
        $technical_support_reply->save();

        $mail_data = [
            'user_name' => $auth->first_name . ' ' . $auth->last_name,
            'user_email' => $auth->email,
            'subject' => $technical_support->subject,
            'initial_message' => $technical_support->message,
            'reply_message' => $request->message,
        ];

        send_email(new TechnicalSupportReplyMail($mail_data, 'admin'), config('app.admin_emails'));

        return redirect()->back();
    }
}
