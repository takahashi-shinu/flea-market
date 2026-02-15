<?php

namespace App\Http\Responses;

use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

class LoginResponse implements LoginResponseContract
{
    public function toResponse($request)
    {
        $user = $request->user();

        // モデルに定義した判定メソッドを使用
        if ($user->isProfileCompleted()) {
            return redirect()->route('mypage');
        }

        return redirect()->route('profile.edit');
    }
}