<?php

namespace App\Http\Controllers\Auth;

use App\Actions\Auth\LoginAction;
use App\Http\Controllers\Controller;
use App\Models\Util\Setting;
use App\Traits\BadRequestException;
use App\Traits\RedirectRequestException;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AuthLoginController extends Controller
{
    public function index(Request $request)
    {
        if(user()){
            return to_route('dashboard.inbound.index',"live-daily");
        }
        
        $menus = [
            [
                'label' => 'Login to Admin',
                'description' => 'Superadmin and Admin Roles',
                'url' => 'https://bapremium.haloyelow.com/'
            ],
            [
                'label' => 'Login to Leader',
                'description' => 'Account Manager and Supervisor Roles',
                'url' => 'https://sqc.haloyelow.com/'
            ],
            [
                'label' => 'Login to Agent',
                'description' => 'Agent Role',
                'url' => 'https://agent.haloyelow.com/'
            ],
            [
                'label' => 'Login to Dashboard',
                'description' => 'Dashboard and Report for All Roles',
                'url' => 'https://report.haloyelow.com/'
            ],
            [
                'label' => 'Login to Kontakami',
                'description' => 'Self Service Contact Center',
                'url' => 'https://business.kontakami.com/login'
            ]
        ];
        return Inertia::render("Auth/Login",[
            'menus' => $menus
        ]);
    }

    public function store(Request $request, LoginAction $loginAction)
    {
        try {
            $loginAction->execute($request);

            return to_route('dashboard.inbound.index',"live-daily");
        } catch (BadRequestException $e) {
            return back()->with(['error' => $e->getMessage()]);
        }
    }

    public function logout(Request $request)
    {
        $message = null;
        if ($request->force) {
            $message = 'You already login in another device';
        }
        session()->flush();
        return to_route('auth.login.index')->with(['error' => $message]);
    }
}
