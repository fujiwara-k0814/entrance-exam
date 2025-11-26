<?php

namespace App\Http\Responses;

use Laravel\Fortify\Contracts\LoginResponse;
use Illuminate\Support\Facades\Auth;

class CustomLoginResponse implements LoginResponse
{
    public function toResponse($request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        //認証メールの送信
        if (!$user->hasVerifiedEmail()) {
            $user->sendEmailVerificationNotification();
        }

        if ($user->delivery_address) {
            return redirect()->intended('/?tab=mylist');
        }else{
            return redirect('/mypage/profile');
        }
    }
}
