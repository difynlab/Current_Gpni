<?php

namespace App\Http\Controllers\Frontend\Student;
use Illuminate\Support\Facades\Auth;
use App\Models\CoursePurchase;
use App\Models\Course;
use App\Models\CourseModule;
use Carbon\Carbon;
use App\Models\CourseChapter;
use App\Http\Controllers\Controller;
use App\Mail\FinalExamPurchaseMail;
use App\Models\CourseFinalExam;
use App\Models\CourseModuleExam;
use App\Models\FinalExamPurchase;
use App\Models\Setting;
use App\Models\Wallet;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function index(Request $request)
    {
        $student = Auth::user();

        $course_ids = CoursePurchase::
        where('user_id', $student->id)
        ->where(function ($query) {
            $query->where('payment_status', 'Completed')
                  ->orWhereNull('payment_status');
        })
        ->where('course_access_status', 'Active')
        ->where(function ($query) {
            $query->where('refund_status', 'Not Refunded')
                  ->orWhereNull('refund_status');
        })
        ->where('status', '1')->whereNot('course_id', 19)->whereNot('course_id', 47)->pluck('course_id')->toArray();

        // $courses = Course::whereIn('id', $course_ids)->where('status', '1')->orderBy('id', 'desc')->get();

        $query = Course::whereIn('id', $course_ids)->where('status', '!=', '0');

        if($request->route()->getName() === 'frontend.courses.gpni-tv') {
            $query->where('type', 'Small Course');
        }
        // else {
        //     $query->whereNot('type', 'Small Course');
        // }

        $courses = $query->orderBy('id', 'desc')->get();

        foreach($courses as $course) {
            $course_purchase = CoursePurchase::
                where('user_id', $student->id)
                ->where('course_id', $course->id)
                ->where(function ($query) {
                    $query->where('payment_status', 'Completed')
                        ->orWhereNull('payment_status');
                })
                ->where('course_access_status', 'Active')
                ->where(function ($query) {
                    $query->where('refund_status', 'Not Refunded')
                        ->orWhereNull('refund_status');
                })
                ->where('status', '1')->whereNot('course_id', 19)->whereNot('course_id', 47)
                ->orderBy('id', 'desc')
                ->first();

            $course->date = $course_purchase && $course_purchase->date ? Carbon::parse($course_purchase->date)->format('d M Y') : null;

            $course->completion_date = $course_purchase && $course_purchase->completion_date ? Carbon::parse($course_purchase->completion_date)->format('d M Y') : null;

            $course->course_final_exam = CourseFinalExam::where('course_id', $course->id)->where('status', '1')->orderBy('id', 'desc')->first();

            $course->course_purchase = $course_purchase;
        }

        return view('frontend.student.courses.index', [
            'courses' => $courses,
            'student' => $student
        ]);
    }
    
    public function show(Course $course)
    {
        $student = Auth::user();

        $course_modules = CourseModule::where('course_id', $course->id)->where('status', '1')->orderBy('id', 'asc')->get();

        foreach($course_modules as $course_module) {
            $course_module->chapters = CourseChapter::where('course_id', $course->id)->where('module_id', $course_module->id)->where('status', '1')->get();
            $course_module->course_module_exam = CourseModuleExam::where('user_id', $student->id)->where('course_id', $course->id)->where('module_id', $course_module->id)->where('status', '1')->orderBy('id', 'desc')->first();
        }

        $course->course_final_exam = CourseFinalExam::where('user_id', $student->id)->where('course_id', $course->id)->where('status', '1')->orderBy('id', 'desc')->first();

        $course->course_purchase = CoursePurchase::
                where('user_id', $student->id)
                ->where('course_id', $course->id)
                ->where(function ($query) {
                    $query->where('payment_status', 'Completed')
                        ->orWhereNull('payment_status');
                })
                ->where('course_access_status', 'Active')
                ->where(function ($query) {
                    $query->where('refund_status', 'Not Refunded')
                        ->orWhereNull('refund_status');
                })
                ->where('status', '1')->whereNot('course_id', 19)->whereNot('course_id', 47)
                ->orderBy('id', 'desc')
                ->first();

        return view('frontend.student.courses.show', [
            'course' => $course,
            'course_modules' => $course_modules,
            'student' => $student
        ]);
    }

    public function showMore(Course $course, CourseModule $course_module, CourseChapter $course_chapter)
    {
        $student = Auth::user();

        $books = json_decode($course_chapter->books) ?? null;
        $videos = json_decode($course_chapter->videos) ?? null;
        $video_links = json_decode($course_chapter->video_links) ?? null;
        $additional_videos = json_decode($course_chapter->additional_videos) ?? null;
        $additional_video_links = json_decode($course_chapter->additional_video_links) ?? null;
        $presentation_medias = json_decode($course_chapter->presentation_medias) ?? null;
        $downloadable_resources = json_decode($course_chapter->downloadable_resources) ?? null;

        return view('frontend.student.courses.more', [
            'course' => $course,
            'course_module' => $course_module,
            'course_chapter' => $course_chapter,
            'student' => $student,
            'books' => $books,
            'videos' => $videos,
            'video_links' => $video_links,
            'additional_videos' => $additional_videos,
            'additional_video_links' => $additional_video_links,
            'presentation_medias' => $presentation_medias,
            'downloadable_resources' => $downloadable_resources
        ]);
    }

    function getFile(Request $request)
    {
        if($request->ajax()) {
            $file_view = view("frontend.student.courses.file-view", [
                'book' => $request->book,
                'type' => $request->type,
            ])->render();

            $file_title = $request->title;
        }

        return response()->json(
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="' . basename($file_view) . '"',
                'Cache-Control' => 'no-cache, no-store, must-revalidate',
                'Pragma' => 'no-cache',
                'Expires' => '0',
                'file_view' => $file_view,
                'file_title' => $file_title
            ]
        );
    }

    public function checkout(Request $request)
    {
        $course = Course::find($request->course_id);
        $user = Auth::user();
        
        $wallet = Wallet::where('user_id', $user->id)->where('status', '1')->first();
        $wallet_balance = $wallet ? $wallet->balance : 0;

        $setting = Setting::find(1);

        if($course->language == 'English') {
            $makeup_price = $setting->makeup_exam_price_en;
            $currency = 'usd';
        }
        elseif($course->language == 'Chinese') {
            $makeup_price = $setting->makeup_exam_price_zh;
            $currency = 'cny';
        }
        else {
            $makeup_price = $setting->makeup_exam_price_ja;
            $currency = 'jpy';
        }

        if($makeup_price >= $wallet_balance) {
            $amount = $makeup_price - $wallet_balance;
        }
        else {
            $amount = '0.00';
        }

        $final_exam_purchase = new FinalExamPurchase();
        $final_exam_purchase->user_id = Auth::user()->id;
        $final_exam_purchase->course_id = $request->course_id;
        $final_exam_purchase->currency = $currency;
        $final_exam_purchase->status = '1';
        $final_exam_purchase->save();

        $total_order_amount_in_cents = $currency === 'jpy' ? (int)$amount : (int)($amount * 100);

        \Stripe\Stripe::setApiKey(config('stripe.sk'));
        $session = \Stripe\Checkout\Session::create([
            'line_items' => [
                [
                    'price_data' => [
                        'currency' => $currency,
                        'product_data' => [
                            'name' => $course->title . ": Final Exam Purchase"
                        ],
                        'unit_amount' => $total_order_amount_in_cents
                    ],
                    'quantity' => 1,
                ]
            ],
            'mode' => 'payment',
            'success_url' => route('frontend.final-exam.success', ['final_exam_purchase_id' => $final_exam_purchase->id]) . '&session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('frontend.courses.show', $course->id) . '?' . http_build_query([
                    'error' => 'Final exam purchase has been failed because of the payment cancellation'
                ]),
        ])
        return redirect()->away($session->url);
    }

    public function success(Request $request)
    {
        \Stripe\Stripe::setApiKey(config('stripe.sk'));

        $session_id = $request->query('session_id');
        $final_exam_purchase_id = $request->query('final_exam_purchase_id');

        $session = \Stripe\Checkout\Session::retrieve($session_id);

        $final_exam_purchase = FinalExamPurchase::find($final_exam_purchase_id);

        if($final_exam_purchase) {
            $final_exam_purchase->date = now()->toDateString();
            $final_exam_purchase->time = now()->toTimeString();
            $final_exam_purchase->mode = $session->mode;
            $final_exam_purchase->transaction_id = $session->id;
            $final_exam_purchase->amount_paid = $session->currency == 'jpy' ? $session->amount_total : $session->amount_total / 100;
            $final_exam_purchase->payment_status = 'Completed';
            $final_exam_purchase->save();
        }

        $wallet = Wallet::where('user_id', $final_exam_purchase->user_id)->where('status', '1')->first();
        $course = Course::find($final_exam_purchase->course_id);

        $setting = Setting::find(1);

        if($course->language == 'English') {
            $makeup_price = $setting->makeup_exam_price_en;
        }
        elseif($course->language == 'Chinese') {
            $makeup_price = $setting->makeup_exam_price_zh;
        }
        else {
            $makeup_price = $setting->makeup_exam_price_ja;
        }

        if($wallet) {
            if($wallet->balance >= $makeup_price) {
                $final_exam_purchase->wallet_amount = $makeup_price;
                $final_exam_purchase->save();

                $wallet->balance = $wallet->balance - $makeup_price;
                $wallet->save();
            }
            else {
                $final_exam_purchase->wallet_amount = $wallet->balance;
                $final_exam_purchase->save();

                $wallet->balance = '0.00';
                $wallet->save();
            }
        }

        $user = Auth::user();

        $mail_data = [
            'name' => $user->first_name . ' ' . $user->last_name,
            'course' => $course->title
        ];

        send_email(new FinalExamPurchaseMail($mail_data, 'user'), $user->email);
        send_email(new FinalExamPurchaseMail($mail_data, 'admin'), config('app.admin_emails'));

        return redirect()->route('frontend.courses.show', $course->id);
    }
}