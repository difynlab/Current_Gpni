<?php

namespace App\Http\Controllers\Frontend\Student;

use App\Http\Controllers\Controller;
use App\Models\AskQuestion;
use App\Models\AskQuestionReply;
use App\Mail\AskQuestionMail;
use App\Mail\AskQuestionReplyMail;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class AskQuestionController extends Controller
{
    public function index()
    {
        $student = Auth::user();

        return view('frontend.student.ask-questions.index', [
            'student' => $student
        ]);
    }

    public function store(Request $request)
    {
        $auth = Auth::user();

        $ask_question = new AskQuestion();
        $ask_question->user_id = $auth->id;
        $ask_question->subject = $request->subject;
        $ask_question->initial_message = $request->initial_message;
        $ask_question->date = Carbon::now()->toDateString();
        $ask_question->time = Carbon::now()->toTimeString();
        $ask_question->admin_viewed = '0';
        $ask_question->status = '1';
        $ask_question->save();

        $mail_data = [
            'user_name' => $auth->first_name . ' ' . $auth->last_name,
            'user_email' => $auth->email,
            'subject' => $request->subject,
            'initial_message' => $request->initial_message,
        ];

        send_email(new AskQuestionMail($mail_data, 'user'), $auth->email);
        send_email(new AskQuestionMail($mail_data, 'admin'), config('app.admin_emails'));

        return redirect()->back()->with('success', 'Your question has been submitted successfully. Depending on the difficulty of the question, we usually reply within 5 working days. Thank you for your patience.');
    }

    public function histories()
    {
        $student = Auth::user();
        $ask_questions = AskQuestion::where('user_id', $student->id)->where('status', '1')->orderBy('id', 'desc')->get();

        foreach($ask_questions as $ask_question) {
            $last_reply = AskQuestionReply::where('ask_question_id', $ask_question->id)
                ->where('status', '1')
                ->orderBy('id', 'desc')
                ->first();

            if ($last_reply && $last_reply->replied_by != auth()->user()->id) {
                $ask_question->replied = $last_reply;
            }
            else {
                $ask_question->replied = null;
            }
        }

        return view('frontend.student.ask-questions.histories', [
            'ask_questions' => $ask_questions,
            'student' => $student
        ]);
    }

    public function show(AskQuestion $ask_question)
    {
        $student = Auth::user();

        $ask_question_replies = AskQuestionReply::where('ask_question_id', $ask_question->id)->where('status', '1')->get();

        $date_time_string = $ask_question->date . ' ' . $ask_question->time;
        $parsed_date_time = Carbon::parse($date_time_string);
        $time_ago = $parsed_date_time->diffForHumans();
        $ask_question->time_difference = $time_ago;

        foreach($ask_question_replies as $ask_question_reply) {
            $ask_question_reply->user_viewed = '1';
            $ask_question_reply->save();

            $date_time_string = $ask_question_reply->date . ' ' . $ask_question_reply->time;
            $parsed_date_time = Carbon::parse($date_time_string);
            $time_ago = $parsed_date_time->diffForHumans();
            $ask_question_reply->time_difference = $time_ago;
        }

        return view('frontend.student.ask-questions.show', [
            'ask_question' => $ask_question,
            'ask_question_replies' => $ask_question_replies,
            'student' => $student
        ]);
    }

    public function update(Request $request, AskQuestion $ask_question)
    {
        $auth = Auth::user();

        $ask_question_reply = new AskQuestionReply();
        $ask_question_reply->ask_question_id = $ask_question->id;
        $ask_question_reply->replied_by = $auth->id;
        $ask_question_reply->message = $request->message;
        $ask_question_reply->date = Carbon::now()->toDateString();
        $ask_question_reply->time = Carbon::now()->toTimeString();
        $ask_question_reply->admin_viewed = '0';
        $ask_question_reply->user_viewed = '1';
        $ask_question_reply->status = '1';
        $ask_question_reply->save();

        $mail_data = [
            'user_name' => $auth->first_name . ' ' . $auth->last_name,
            'user_email' => $auth->email,
            'subject' => $ask_question->subject,
            'initial_message' => $ask_question->initial_message,
            'reply_message' => $request->message,
        ];

        send_email(new AskQuestionReplyMail($mail_data, 'admin'), config('app.admin_emails'));

        return redirect()->back();
    }
}