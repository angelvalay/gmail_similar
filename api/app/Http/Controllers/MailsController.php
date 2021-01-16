<?php

namespace App\Http\Controllers;

use App\Models\Email;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class MailsController extends Controller
{
    public function store(Request $request)
    {
        $this->validate($request,[
            'mail_to' => 'required',
            'title'=>'required',
            'body'=>'required'
        ]);

        $email = new Email();
        $email->mail_from = null;
        $email->mail_to = $request->get('mail_to');
        $email->body = $request->get('body');
        $email->title = $request->get('title');
        $email->is_important = 0;
        $email->is_deleted = 0;
        $email->save();
        return response()->json($email);
    }

    public function show($id)
    {
        $email = Email::findOrFail($id);
        return response()->json($email);
    }

    public function markAsImportant(Request $request)
    {
        $this->validate($request,[
            'id_mails' => 'required|array|min:1',
            'is_important' => 'required',
        ]);

        Email::whereIn('id',$request->get('id_mails'))->update([
            'is_important' => $request->get('is_important')
        ]);

        return response(['status'=>'successful']);
    }

    public function sentToTrash(Request $request)
    {
        $this->validate($request,[
            'id_mails' => 'required|array|min:1',
            'is_deleted' => 'required',
        ]);

        Email::whereIn('id',$request->get('id_mails'))->update([
            'is_deleted' => $request->get('is_deleted')
        ]);
        return response(['status'=>'successful']);
    }

    public function getTrash()
    {
        $mails = Email::where('is_deleted',1)->orderByDesc('created_at')->get();
        return \response()->json($mails);
    }

    public function getInbox()
    {
        $mails = Email::where('is_deleted',0)->where('mail_to',null)->orderByDesc('created_at')->get();
        return \response()->json($mails);
    }

    public function getSentEmails()
    {
        $mails = Email::where('is_deleted',0)->where('mail_from',null)->orderByDesc('created_at')->get();
        return \response()->json($mails);
    }

    public function delete(Request $request)
    {
        $this->validate($request,[
            'id_mails' => 'required|array|min:1',
        ]);

        Email::whereIn('id',$request->get('id_mails'))->delete();

        return response(['status'=>'successful']);
    }
}
