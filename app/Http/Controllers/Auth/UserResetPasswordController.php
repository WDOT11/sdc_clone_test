<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\UserPasswordResetMail;
use App\Models\User;
use App\Models\UserMeta;
use App\Services\Smtp2GoMail;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;

class UserResetPasswordController extends Controller
{
    use ResetsPasswords;

    public function redirectPath()
    {
        // return route('sdcsmuser.login.index');
        return '/sdcsmuser/login';
    }

    protected function broker()
    {
        return Password::broker('users');
    }

    public function __construct()
    {
        $this->middleware('guest');
    }

    public function showResetForm(Request $request, $token = null)
    {
        return view('auth.userdash.passwords.reset')->with([
            'token' => $token,
            'email' => $request->email,
        ]);
    }

    public function generateResetForm($userId)
    {
        try {
            $user = User::findOrFail($userId);
            if (empty($user)) {
                return response()->json(["msg" => "User not found.", "success" => false], 200);
            }else {
                $token = Password::broker('users')->createToken($user);
                $userMetaData = UserMeta::where('user_id', $user->id)->where('service_provider_id', $user->service_provider_id)->where('active', 1)->first();
                if (!empty($userMetaData) && isset($userMetaData->meta_key) && $userMetaData->meta_key == 'admin') {
                    $resetUrl = route('smarttiusadmin.password.reset', ['token' => $token]) . '?email=' . urlencode($user->email);
                    /** Send Mail using the Smtp2GoMail **/
                    Smtp2GoMail::to($user->email)->subject('Reset Your Password')->view('emails.user_password_reset', ['resetUrl' => $resetUrl])->send();
                } else {
                    $resetUrl = route('sdcsmuser.password.reset', ['token' => $token]) . '?email=' . urlencode($user->email);
                    /** Send Mail using the Smtp2GoMail **/
                    Smtp2GoMail::to($user->email)->subject('Reset Your Password')->view('emails.user_password_reset', ['resetUrl' => $resetUrl])->send();
                }
                return response()->json(["msg" => "Reset Password link sent to {$user->full_name}", "success" => true], 200);

            }
        } catch (\Exception $e) {
            return response()->json(["msg" => "Something went wrong, Please try again later.", "success" => false], 200);
        }
    }

    /* ADD THIS METHOD */
    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email|exists:users,email,active,1',
            'password' => 'required|confirmed|min:8',
        ],[
            'email.required' => 'Please enter your email address.',
            'email.email' => 'Please enter a valid email address.',
            'email.exists' => 'We couldn\'t find an active account with this email.',
            'password.required' => 'Please enter your new password.',
            'password.confirmed' => 'The password confirmation does not match.',
            'password.min' => 'The password must be at least 8 characters long.',
        ]);

        $status = $this->broker()->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->password = Hash::make($password);
                $user->save();
            }
        );
        if ($status == Password::PASSWORD_RESET) {
            return redirect($this->redirectPath())->with('status', __('Your password has been updated.'));
        }

        // Custom error messages
        $customMessages = [
            Password::INVALID_TOKEN => 'The password reset link has expired.',
            Password::INVALID_USER => 'We can’t find a user with that email address.',
        ];

        throw ValidationException::withMessages([
            'email' => [$customMessages[$status] ?? trans($status)],
        ]);
    }
}
