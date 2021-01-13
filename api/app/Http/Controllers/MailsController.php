<?php

namespace App\Http\Controllers;

use App\Models\Email;
use Illuminate\Http\Request;

class MailsController extends Controller
{
    public function store(Request $request)
    {
        $this->validate($request,[
            'mail_from' => 'required',
            'mail_to' => 'required',
            'body'=>'required'
        ]);

        $email = new Email();
        $email->mail_from = $request->get('mail_from');
        $email->mail_to = $request->get('mail_to');
        $email->body = $request->get('body');
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

        return response()->json(Email::all());
    }

    public function sentToTrash($id)
    {
        $email = Email::findOrFail($id);
        $email->is_deleted = 1;
        $email->save();
        return response()->json($email);
    }

    public function getTrash()
    {
        $mails = Email::where('is_deleted',1)->get();
        return \response()->json($mails);
    }

    public function getAll()
    {
        $mails = Email::where('is_deleted',0)->get();
        return \response()->json($mails);
    }
}
