<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory;
    protected $fillable=[
        'first_name_of_user',
        'second_name_of_user',
        'email_of_user',
        'phone_number_of_user',
        'message_of_user',
    ];
}