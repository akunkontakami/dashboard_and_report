<?php
namespace App\Actions\Auth;

use App\Actions\Auth\RegisterAction;
use App\Helpers\SocketBroadcast;
use App\Helpers\Yellow;
use App\Models\Account\User;
use App\Traits\RedirectRequestException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class LoginAction
{
     public function execute(Request $request)
     {

          $request->validate([
               'email' => 'required|email',
               'password' => 'required|min:8|max:50|regex:/^.*(?=.{3,})(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9]).*$/',
          ]);

          $user = User::query()
               ->whereRole('customer')
               ->whereEmail($request->email)
               ->where('is_kontakkami', true)
               ->whereNull('deleted_at')
               ->first();
          if (!$user) {
               throw ValidationException::withMessages([
                    'email' => 'The email you entered is incorrect',
               ]);
          }

          if (!$user->email_verified_at) {
               /**
                * Send email confirmation OTP again and redirect to input otp form
                */
               $this->sendNewEmailVerification($user->email);
          }


          if (!Hash::check($request->password, $user->password)) {
               throw ValidationException::withMessages([
                    'password' => 'The password you entered does not match',
               ]);
          }

          $this->forceLogoutUser($user);


          $randomDeviceToken = base64_encode(str()->uuid() . date('YmdHis'));
          $user->update([
               'platform' => 'web',
               'regid' => $randomDeviceToken
          ]);

          $sessionObject = [
               'id' => $user->id,
               'role' => $user->role,
               'name' => $user->name,
               'email' => $user->email,
               'username' => $user->username,
               'lang' => $user->lang,
               'phone_code' => $user->phone_code,
               'phone_number' => Yellow::normalPhoneNumber($user->phone, $user->phone_code),
               'avatar' => asset($user->profile),
          ];

          session()->put(config('services.session-user-prefix'), (object) $sessionObject);
          session()->put('user-device-token', $randomDeviceToken);
          return $user;
     }

     private function forceLogoutUser(User $user)
     {
          if ($user->regid) {
               SocketBroadcast::channel('force_logout')
                    ->destination([$user->id])
                    ->send(['status' => 'approved']);
          }
     }


     private function sendNewEmailVerification($email)
     {
          $token = (new RegisterAction)->sendOtp($email);
          throw new RedirectRequestException(route('auth.register.otp-verification', $token));
     }
}