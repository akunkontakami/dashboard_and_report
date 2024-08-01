<?php

namespace App\Http\Middleware;

use App\Enum\Role;
use App\Models\Account\CompanyAccount;
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
            $account = null;
            $regid = null;
            if(in_array($user->role,[Role::BA,Role::Admin])){
                $account = CompanyAccount::where('id', $user->id)->select('device_token')->first();
                $regid = $account?->device_token;
            }else{
                $account = User::where('id', $user->id)->select('regid')->first();
                $regid = $account?->regid;
            }
            if (!$account || $regid !== session('user-device-token')) {
                $request->session()->flush();
                return to_route('auth.login.index')->with(['error' => 'You already login in another device']);
            }
        }

        return $next($request);
    }

}
