<?php

namespace App\Http\Controllers\Backend\Purchase;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseCertificate;
use App\Models\CourseFinalExam;
use App\Models\CoursePurchase;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class CoursePurchaseController extends Controller
{
    private function processCoursePurchases($course_purchases)
    {
        foreach($course_purchases as $course_purchase) {
            $course_purchase->action = '
            <a href="'. route('backend.purchases.course-purchases.show', $course_purchase->id) .'" class="review-button" title="Details"><i class="bi bi-calendar-fill"></i></a>
            <a href="'. route('backend.purchases.course-purchases.certificates.index', $course_purchase->id) .'" class="edit-button" title="Provide Certificate"><i class="bi bi-patch-check-fill"></i></a>
            <a id="'.$course_purchase->id.'" class="delete-button" title="Delete"><i class="bi bi-trash3"></i></a>';

            $course_purchase->user = User::find($course_purchase->user_id)->first_name . ' ' . User::find($course_purchase->user_id)->last_name;
            $course_purchase->course = Course::find($course_purchase->course_id)->title;

            $course_purchase->date_time = $course_purchase->date . ' | ' . $course_purchase->time;

            $course_purchase->payment_status = 
                ($course_purchase->payment_status == 'Completed') 
                ? '<span class="active-status">Completed</span>' 
                : (($course_purchase->payment_status == 'Pending') 
                    ? '<span class="pending-status">Pending</span>' 
                    : (($course_purchase->payment_status == 'Failed') 
                    ? '<span class="pending-status">Failed</span>' 
                    : '<span class="active-status">Directly Added</span>'));

            $course_purchase->material_logistic = 
                ($course_purchase->material_logistic == 'Yes') 
                ? '<span class="active-status">Yes</span>' 
                : '<span class="pending-status">No</span>';

            $course = Course::find($course_purchase->course_id);
            if($course->final_exam == 'Yes') {
                if(hasStudentCompletedFinalExam($course_purchase->user_id, $course_purchase->course_id)) {
                    $final_exam = CourseFinalExam::where('user_id', $course_purchase->user_id)->where('course_id', $course_purchase->course_id)->where('status', '1')->orderBy('id', 'desc')->first();
                    
                    if($final_exam->result == 'Pass') {
                        $course_purchase->final_exam = '<span class="active-status">Pass</span>';
                    }
                    else {
                        $course_purchase->final_exam = '<span class="inactive-status">Fail</span>';
                    }
                }
                else {
                    $course_purchase->final_exam = '<span class="pending-status">Pending</span>';
                }
            }
            else {
                $course_purchase->final_exam = '-';
            }
            

            $course_purchase->course_access_status = ($course_purchase->course_access_status == 'Active') ? '<span class="active-status">Active</span>' : '<span class="inactive-status">Revoked</span>';
        }

        return $course_purchases;
    }

    public function index(Request $request)
    {
        $items = $request->items ?? 10;

        if(auth()->user()->admin_language) {
            $user_ids = User::where('role', 'student')->where('language', auth()->user()->admin_language)->where('status', '1')->pluck('id')->toArray();

            $course_purchases = CoursePurchase::whereIn('user_id', $user_ids)->where('date', '!=', null)->where('status', '1')->orderBy('id', 'desc')->paginate($items);
        }
        else {
            $course_purchases = CoursePurchase::where('date', '!=', null)->where('status', '1')->orderBy('id', 'desc')->paginate($items);
        }

        $course_purchases = $this->processCoursePurchases($course_purchases);

        return view('backend.purchases.course-purchases.index', [
            'course_purchases' => $course_purchases,
            'items' => $items
        ]);
    }

    public function show(CoursePurchase $course_purchase)
    {
        $student = User::find($course_purchase->user_id)->first_name . ' ' . User::find($course_purchase->user_id)->last_name;

        $course = Course::where('status', '1')->find($course_purchase->course_id)->title;

        return view('backend.purchases.course-purchases.show', [
            'course_purchase' => $course_purchase,
            'student' => $student,
            'course' => $course
        ]);
    }

    public function destroy(CoursePurchase $course_purchase)
    {
        $course_purchase->status = '0';
        $course_purchase->save();

        return redirect()->back()->with('success', 'Successfully deleted!');
    }

    public function filter(Request $request)
    {
        if($request->action == 'reset') {
            return redirect()->route('backend.purchases.course-purchases.index');
        }

        $student_email = $request->student_email;
        $student_name = $request->student_name;
        $course_name = $request->course_name;
        $date = $request->date;

        if(auth()->user()->admin_language) {
            $user_ids = User::where('role', 'student')->where('language', auth()->user()->admin_language)->where('status', '1')->pluck('id')->toArray();

            $course_purchases = CoursePurchase::whereIn('user_id', $user_ids)->where('date', '!=', null)->where('status', '1')->orderBy('id', 'desc');
        }
        else {
            $course_purchases = CoursePurchase::where('date', '!=', null)->where('status', '1')->orderBy('id', 'desc');
        }

        if($student_email) {
            $email_user_ids = User::where('role', 'student')
                ->where('status', '1')
                ->where('email', 'like', '%' . $student_email . '%')
                ->pluck('id')
                ->toArray();

            $course_purchases->whereIn('user_id', $email_user_ids);
        }

        if($student_name) {
            $user_ids = User::where('role', 'student')
            ->where('status', '1')
            ->where(function ($query) use ($student_name) {
                $query->where('first_name', 'like', '%' . $student_name . '%')
                      ->orWhere('last_name', 'like', '%' . $student_name . '%')
                      ->orWhere(DB::raw("CONCAT(first_name, ' ', last_name)"), 'like', '%' . $student_name . '%');
            })
            ->pluck('id')->toArray();

            $course_purchases->whereIn('user_id', $user_ids);
        }

        if($course_name) {
            $course_ids = Course::where('status', '1')->where('title', 'like', '%' . $course_name . '%')->pluck('id')->toArray();

            $course_purchases->whereIn('course_id', $course_ids);
        }

        if($date) {
            $course_purchases->where('date', $date);
        }

        $items = $request->items ?? 10;
        $course_purchases = $course_purchases->paginate($items);
        $course_purchases = $this->processCoursePurchases($course_purchases);

        return view('backend.purchases.course-purchases.index', [
            'course_purchases' => $course_purchases,
            'items' => $items,
            'student_email' => $student_email,
            'student_name' => $student_name,
            'course_name' => $course_name,
            'date' => $date,
        ]);
    }

    public function certificate(CoursePurchase $course_purchase)
    {
        $certificate = CourseCertificate::where('course_purchase_id', $course_purchase->id)->first();

        if(!$certificate) {
            $certificate = new CourseCertificate();
            $certificate->course_purchase_id = $course_purchase->id;
            $certificate->status = '1';
            $certificate->save();
        }

        return view('backend.purchases.course-purchases.certificate', [
            'course_purchase' => $course_purchase,
            'certificate' => $certificate
        ]);
    }

    private function deleteOldRemovedFiles($course_certificate, $field, $name, $path)
    {
        $existing_files = collect(json_decode($course_certificate->$field, true));
        $existing_files = collect($existing_files->pluck('file')->all());
        $old_files = collect($name);
        $missing_files = $existing_files->diff($old_files);

        if($missing_files->isNotEmpty()) {
            foreach($missing_files as $key => $missing_file) {
                Storage::delete('public/backend/courses/' . $path . $missing_file);
            }
        }
    }

    public function certificateUpdate(Request $request, CourseCertificate $course_certificate)
    {
        $validator = Validator::make($request->all(), [
            'certificate_files.*' => 'max:30720',
            'certificate_times.*' => ['nullable', 'regex:/^(?:2[0-3]|[01][0-9]):[0-5][0-9]$/']
        ], [
            'certificate_files.*.max' => 'Each certificate must not be greater than 30 MB',
            'certificate_times.*.regex' => 'The time format must be HH:MM'
        ]);
        
        if($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput()->with('error', 'There was a problem with your certificate upload!');
        }

        $certificates = [];

        $this->deleteOldRemovedFiles($course_certificate, 'certificates', $request->old_certificate_files, 'course-certificates/');

        if($request->old_certificate_dates) {
            foreach($request->old_certificate_dates as $key => $old_certificate_date) {
                array_push($certificates, [
                    'date' => $old_certificate_date,
                    'time' => $request->old_certificate_times[$key],
                    'file' => $request->old_certificate_files[$key]
                ]);
            }
        }

        if($request->certificate_dates) {
            foreach($request->certificate_dates as $key => $certificate_date) {
                if($request->certificate_files && $request->certificate_files[$key]) {
                    $certificate = $request->certificate_files[$key];
                    $certificate_name = Str::random(40) . '.' . $certificate->getClientOriginalExtension();
                    $certificate->storeAs('public/backend/courses/course-certificates', $certificate_name);
                }

                array_push($certificates, [
                    'date' => $certificate_date,
                    'time' => $request->certificate_times[$key] ?? Carbon::now()->toTimeString(),
                    'file' => $certificate_name
                ]);
            }
        }
        $final_certificates = $certificates ? json_encode($certificates) : null;

        $data = $request->except('old_certificate_dates', 'old_certificate_times', 'old_certificate_files', 'certificate_dates', 'certificate_times', 'certificate_files');
        $data['certificates'] = $final_certificates;

        $data['status'] = $final_certificates ? '1' : '0';

        $course_certificate->fill($data)->save();
        
        return redirect()->route('backend.purchases.course-purchases.certificates.index', $course_certificate->course_purchase_id)->with('success', "Successfully updated!");
    }
}