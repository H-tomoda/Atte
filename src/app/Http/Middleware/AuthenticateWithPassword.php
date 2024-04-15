<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AuthenticateWithPassword
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $password = env('PASSWORD'); //.envでのパスワード設定

        if ($request->input('password') !== $password) {
            return redirect()->route('password.form'); //パスワード不一致で入力フォームにリダイレクト
        }
        return $next($request);
    }
}
