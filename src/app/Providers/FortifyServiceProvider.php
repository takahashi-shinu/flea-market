<?php

namespace App\Providers;

use App\Models\User;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Fortify;
use App\Actions\Fortify\CreateNewUser;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;
use App\Http\Responses\LoginResponse;

class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot()
    {
        // viewの指定
        Fortify::registerView(function () {
            return view('auth.register');
        });

        Fortify::loginView(function () {
            return view('auth.login');
        });

        Fortify::verifyEmailView(function () {
            return view('auth.authenticate');
        });

        // 会員登録処理
        Fortify::createUsersUsing(CreateNewUser::class);

        // ログイン処理
        Fortify::authenticateUsing(function (Request $request) {

            $request->validate(app(LoginRequest::class)->rules(), app(LoginRequest::class)->messages());

            if (Auth::attempt(
                $request->only('email', 'password'),
                $request->boolean('remember')
            )) {
                return Auth::user();
            }

            throw ValidationException::withMessages([
                'email' => 'ログイン情報が登録されていません',
            ]);
        });

        $this->app->singleton(LoginResponseContract::class, LoginResponse::class);

    }
}
