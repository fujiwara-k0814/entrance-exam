<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class VerifyEmailController extends Controller
{
    public function notice()
    {
        return view('verify-email');
    }

    public function send(Request $request)
    {
        $request->user()->sendEmailVerificationNotification();
        
        return redirect('/email/verify');
    }
}
