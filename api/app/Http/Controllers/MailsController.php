<?php

namespace App\Http\Controllers;

use App\Models\Email;
use Illuminate\Http\Response;

class MailsController extends Controller
{
    public function store(Response $response)
    {
        // validate
        // create instance
        // return instance
        return 'send info mail';
    }

    public function show($id)
    {
        // find instance
        // return instance
        return 'return content mail '. $id;
    }

    public function markAsImportant(Response $response)
    {
        // validate
        // update instances
        // return exit o fail

        // id's array, true o false for mark as important
        return 'mark as important a set mails';
    }

    public function sentToTrash($id)
    {
        return 'send mail to trash '.$id;
    }

    public function getTrash()
    {
        // get instances
        // return instances
        return 'return mails trashed';
    }

    public function getAll()
    {
        // get instances
        // return instances
        $mails = Email::all();
        return \response()->json($mails);
    }
}
