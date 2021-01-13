<?php


namespace App\Models;
use Illuminate\Database\Eloquent\Model;


class Email extends Model
{
    protected $table = 'mails';

    protected $fillable = ['mail_from','mail_to','body','is_important','is_deleted'];

}
