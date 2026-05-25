<?php

namespace App\Http\Controllers\Backend\Communication;

use App\Http\Controllers\Controller;
use App\Models\TechnicalSupport;
use App\Models\TechnicalSupportReply;
use App\Models\User;
use App\Mail\TechnicalSupportReplyMail;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TechnicalSupportController extends Controller
{
    private function processTechnicalSupports($technical_supports)
    {
        foreach($technical_supports as $technical_support) {
            $technical_support->action = '
            <a href="'. route('backend.communications.technical-supports.edit', $technical_support->id) .'" class="edit-button" title="Reply"><i class="bi bi-pencil-square"></i></a>
            <a id="'.$technical_support->id.'" class="delete-button" title="Delete"><i class="bi bi-trash3"></i></a>';

            $user = User::find($technical_support->user_id);

            $technical_support->user = $user->first_name . ' ' . $user->last_name;
            $technical_support->email = $user->email;

            $technical_support->date = Carbon::parse($technical_support->created_at)->toDateString();
            $technical_support->time = Carbon::parse($technical_support->created_at)->toTimeString();
        }

        return $technical_supports;
    }

    public function index(Request $request)
    {
        $items = $request->items ?? 10;

        $technical_supports = TechnicalSupport::where('status', '1')->orderBy('id', 'desc')->paginate($items);
        $technical_supports = $this->processTechnicalSupports($technical_supports);

        TechnicalSupport::where('status', '1')->where('is_new', '1')->update(['is_new' => '0']);

        return view('backend.communications.technical-supports.index', [
            'technical_supports' => $technical_supports,
            'items' => $items
        ]);
    }

    public function edit(TechnicalSupport $technical_support)
    {
        if($technical_support->is_new != '0') {
            $technical_support->is_new = '0';
            $technical_support->save();
        }

        $user = User::find($technical_support->user_id);
        $technical_support_replies = TechnicalSupportReply::where('technical_support_id', $technical_support->id)->where('status', '1')->get();

        $technical_support->time_difference = Carbon::parse($technical_support->created_at)->diffForHumans();

        foreach($technical_support_replies as $technical_support_reply) {
            $technical_support_reply->admin_viewed = '1';
            $technical_support_reply->save();

            $date_time_string = $technical_support_reply->date . ' ' . $technical_support_reply->time;
            $technical_support_reply->time_difference = Carbon::parse($date_time_string)->diffForHumans();
        }

        return view('backend.communications.technical-supports.edit', [
            'technical_support' => $technical_support,
            'technical_support_replies' => $technical_support_replies,
            'user' => $user
        ]);
    }

    public function update(Request $request, TechnicalSupport $technical_support)
    {
        $technical_support_reply = new TechnicalSupportReply();
        $technical_support_reply->technical_support_id = $technical_support->id;
        $technical_support_reply->replied_by = auth()->user()->id;
        $technical_support_reply->message = $request->message;
        $technical_support_reply->date = Carbon::now()->toDateString();
        $technical_support_reply->time = Carbon::now()->toTimeString();
        $technical_support_reply->admin_viewed = '1';
        $technical_support_reply->user_viewed = '0';
        $technical_support_reply->status = '1';
        $technical_support_reply->save();

        $user = User::find($technical_support->user_id);

        $mail_data = [
            'user_name' => $user->first_name . ' ' . $user->last_name,
            'user_email' => $user->email,
            'subject' => $technical_support->subject,
            'initial_message' => $technical_support->message,
            'reply_message' => $request->message,
        ];

        send_email(new TechnicalSupportReplyMail($mail_data, 'user'), $user->email);

        return redirect()->route('backend.communications.technical-supports.edit', $technical_support)->with('success', "Successfully sent!");
    }

    public function destroy(TechnicalSupport $technical_support)
    {
        $technical_support_replies = TechnicalSupportReply::where('technical_support_id', $technical_support->id)->where('status', '!=', '0')->get();

        if($technical_support_replies) {
            foreach($technical_support_replies as $technical_support_reply) {
                $technical_support_reply->status = '0';
                $technical_support_reply->save();
            }
        }

        $technical_support->status = '0';
        $technical_support->save();

        return redirect()->back()->with('success', 'Successfully deleted!');
    }

    public function filter(Request $request)
    {
        if($request->action == 'reset') {
            return redirect()->route('backend.communications.technical-supports.index');
        }

        $name = $request->name;

        $users = User::where('status', '1');
        $technical_supports = TechnicalSupport::where('status', '1')->orderBy('id', 'desc');

        if($name != null) {
            $user_ids = $users->where(function ($query) use ($name) {
                $query->whereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ['%' . $name . '%'])
                      ->orWhereRaw("CONCAT(last_name, ' ', first_name) LIKE ?", ['%' . $name . '%']);
            })->get()->pluck('id')->toArray();

            $technical_supports->whereIn('user_id', $user_ids);
        }

        $items = $request->items ?? 10;
        $technical_supports = $technical_supports->paginate($items);
        $technical_supports = $this->processTechnicalSupports($technical_supports);

        return view('backend.communications.technical-supports.index', [
            'technical_supports' => $technical_supports,
            'items' => $items,
            'name' => $name
        ]);
    }
}
