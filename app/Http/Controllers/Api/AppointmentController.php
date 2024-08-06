<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\Appointment;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class AppointmentController extends Controller
{
public function makeAppointment(Request $request)
{
$appointment = Appointment::create([
    'first_name_of_user'=>$request->first_name_of_user,
    'second_name_of_user'=>$request->second_name_of_user,
    'email_of_user'=>$request->email_of_user,
    'phone_number_of_user'=>$request->phone_number_of_user,
    'message_of_user'=>$request->message_of_user,

]);

return response()->json($appointment, 201);
}

public function getAllAppointments()
{
    $appointments = Appointment::all();
    return response()->json([
        'message' => 'Registration successful.',
        'data' => $appointments,
        'status' => 200,

    ], 200);
}


}
