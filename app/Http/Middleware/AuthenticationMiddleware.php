<?php

namespace App\Http\Middleware;

use App\Models\Account\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthenticationMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if(!$user = user()){
            return to_route('auth.login.index');
        }

        if (!$request->inertia() && !$request->expectsJson() && !$request->header('x-requested-with')) {
            $user = User::where('id', $user->id)->select('regid')->first();
            if (!$user || $user->regid !== session('user-device-token')) {
                $request->session()->flush();
                return to_route('auth.login.index')->with(['error' => 'You already login in another device']);
            }
        }

        return $next($request);
    }

}
