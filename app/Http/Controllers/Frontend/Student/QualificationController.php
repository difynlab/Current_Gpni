<?php

namespace App\Http\Controllers\Frontend\Student;

use App\Http\Controllers\Controller;
use App\Models\CECPointActivity;
use App\Models\Course;
use Illuminate\Support\Facades\Auth;
use App\Models\CourseCertificate;
use App\Models\CoursePurchase;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Mail\CECPointRequestMail;
use App\Mail\CECPointUserMail;

class QualificationController extends Controller
{
    public function index(Request $request)
    {
        $language = $request->middleware_language_name;

        if($language == 'English') {
            $addition = 'Addition';
            $deduction = 'Deduction';
        }
        elseif($language == 'Chinese') {
            $addition = '增加';
            $deduction = '扣除';
        }
        else {
            $addition = '追加';
            $deduction = '控除';
        }

        $student = Auth::user();
        $courses = $request->qualification ? Course::where('title', 'like', '%' . $request->qualification . '%')->where('status', '1')->get() : Course::where('status', '1')->get();
        $course_ids = $courses->pluck('id')->toArray();
        
        $purchases = CoursePurchase::
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
            ->whereIn('course_id', $course_ids)
            ->where('status', '1')
            ->get();

        $purchase_ids = $purchases->pluck('id')->toArray();
        $certificates = CourseCertificate::whereIn('course_purchase_id', $purchase_ids)->where('status', '1')->whereNotNull('certificates')->get();

        $obtained_certificates = $certificates->map(function ($certificate) use ($purchases) {
            $purchase = $purchases->find($certificate->course_purchase_id);
            $course = Course::find($purchase->course_id);

            return [
                'course_title' => $course->title,
                'certificates' => json_decode($certificate->certificates)
            ];
        })->filter();

        $cec_point_activities = CECPointActivity::where('user_id', $student->id)->where('status', '!=', '0')->orderBy('id', 'desc')->get();

        foreach($cec_point_activities as $cec_point_activity) {
            if($cec_point_activity->type == 'Addition') {
                $cec_point_activity->type = $addition;
            }
            else {
                $cec_point_activity->type = $deduction;
            }
        }
        
        $course_ids = $purchases->pluck('course_id')->toArray();

        $cec_courses = Course::whereIn('id', $course_ids)->where('status', '1')->get();

        return view('frontend.student.qualifications', [
            'student' => $student,
            'obtained_certificates' => $obtained_certificates,
            'qualification' => $request->qualification,
            'cec_point_activities' => $cec_point_activities,
            'cec_courses' => $cec_courses
        ]);
    }

    public function cecStore(Request $request)
    {
        $student = Auth::user();
        $course = Course::find($request->course_id);

        CECPointActivity::create([
            'user_id' => $student->id,
            'course_id' => $request->course_id,
            'activity_name' => $request->activity_name,
            'type' => 'Addition',
            'date' => Carbon::now()->toDateString(),
            'time' => Carbon::now()->toTimeString(),
            'points' => $request->points,
            'user_comment' => $request->user_comment,
            'status' => '2'
        ]);

        $mail_data = [
            'name' => $student->first_name . ' ' . $student->last_name,
            'email' => $student->email,
            'points' => $request->points,
            'course' => $course->title ?? null,
            'activity_name' => $request->activity_name ?? null,
            'user_comment' => $request->user_comment
        ];

        send_email(new CECPointUserMail($mail_data), $student->email);
        send_email(new CECPointRequestMail($mail_data), config('app.admin_emails'));

        return redirect()->route('frontend.qualifications')->with('success', 'Request successfully completed');
    }
}