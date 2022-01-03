<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use DB;
use Carbon\Carbon;
// use Branchio;
use Illuminate\Auth\Events\PasswordReset;

class AuthController extends ApiBaseController
{
    public function login(Request $request) {
        return $this->authenticateUser($request);
    }

    public function register(Request $request) {
        $this->saveUser($request);
        $auth = $this->authenticateUser($request);
        return $auth;
    }
    
    private function saveUser($request) {
        $user = auth('api')->user();
        if (!$user) {
            $user = new User();
            $user->password = Hash::make($request->password);
        }
        $user->email = $request['email'];
        $user->name = $request['name'];
        $user->save();
        $user->assignRole('reader');
        return $user;
    }

    private function authenticateUser($request) {
        $credentials = [
            'email' => $request['email'],
            'password' => $request['password'],
        ];
        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            // $user->load(['codes.book.author','codes.book.edition']);
            $token = $user->createToken('editions-akhira')->plainTextToken;
            // if (!empty($request['deviceId'])) {
            //     $deviceId = $request['deviceId'];
            //     Device::updateOrCreate(
            //         ['device_id' => $deviceId],
            //         [
            //             'device_id' => $deviceId, 
            //             'platform' => $request->platform, 
            //             'model' => $request->model, 
            //             'version' => $request->version, 
            //             'manufacturer' => $request->manufacturer, 
            //             'user_id' => $user->id 
            //         ]
            //     );
            // }
            return ['user' => $user, 'token' => $token];
        }
        return $this->sendError(__('auth.failed'), 403);
    }

    public function updateAccount(Request $request) {
        $user = auth()->user();
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|string|email|unique:users,email,' . $user->id,
        ]);
        $user->name = $request->name;
        $user->email = $request->email;
        $user->save();
        return $user;
    }

    public function changePassword(Request $request) {
        $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|confirmed',
        ]);
        $user = auth()->user();
        if (!Hash::check($request->current_password, $user->password)) {
            return $this->sendError(__('auth.password'), 401);
        }
        $user->password = Hash::make($request->password);
        $user->save();
        return $user;
    }

    private function getTokenByEmail($email) {
        $data = DB::table('password_resets')->select('token')
            ->where('email', '=', $email)
            ->first();
        return $data ? $data->token : $data;
    }

    private function createToken($email) {
        $reset_token = strtolower(Str::random(64));
        $token = $this->getTokenByEmail($email);
        if ($token) {
            DB::table('password_resets')->update([
                'token' => $reset_token,
                'created_at' => Carbon::now(),
            ]); 
        }else {
            DB::table('password_resets')->insert([
                'email' => $email,
                'token' => $reset_token,
                'created_at' => Carbon::now(),
            ]);
        }
        return $reset_token;
    }

    public function forgotPassword(Request $request) {
        $user = User::whereEmail($request->email)->first();
        if ($user) {
            $token = $this->createToken($user->email);
            $branchData = [
                '$always_deeplink' => 'true',
                '$deeplink_path' => 'reset-password',
                'token' => $token
            ];
            $branchLink = $this->createBranchLink($branchData);
            return $branchLink;
            // Mail::to($user)->send(new ForgotPasswordMail($branchLink, $user));
            return response([
                'status' => 200,
                'message' => 'Reset email sent successfully'
            ], 200);
        }
        else 
            return response(['errors' => [
                'customError' => __("We can't find a user with this email")
            ]], 404);
    }

    private function createBranchLink($data) {
        // $link = new \Iivannov\Branchio\Link();
        // $link->setChannel('reset-password')
        //     ->setData($data);
        // return Branchio::createLink($link);
    }

    private function resetUserPassword ($credentials) {
        $user = User::whereEmail($credentials['email'])->first();
        $user->password = bcrypt($credentials['password']);
        $user->setRememberToken(Str::random(60));
        $user->save();
        event(new PasswordReset($user));
        DB::table('password_resets')->where('email', '=', $user->email)->delete();
        return $user;
    }

    protected function credentials(Request $request, $email)
    {
        $credentials = $request->only(
            'password', 'password_confirmation', 'token'
        );
        $credentials['email'] = $email;
        return $credentials;
    }

    public function resetPassword(Request $request) {
        $data = DB::table('password_resets')->select('email')
            ->where('token', '=', $request->token)
            ->first();
        if (!$data) {
            return response(['errors' => [
                'customError' => __('auth.noPasswordResetRequest')
            ]], 401);
        }
        $credentials = $this->credentials($request, $data->email);
        $token = $this->getTokenByEmail($credentials['email']);
        if ($token) {
            $user = $this->resetUserPassword($credentials);
            $response = $this->authenticateUser($credentials);
            return response($response, 200);
        }else {
            return response(['errors' => [
                'customError' => __('auth.noPasswordResetRequest')
            ]], 401);
        }
    }

    public function logout(Request $request) {
        // $device = Device::where('device_id', $request->deviceId)->first();
        if ($device) {
            $device->delete();
        }
        return response([
            'message' => 'Logout successfully',
            'status' => 200
        ], 200);
    }
}
