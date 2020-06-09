<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Support\Str;
use App\User;
use Illuminate\Http\Request;
use App\Models\PasswordReset;
use App\Notifications\ResetPasswordRequest;
use Illuminate\Support\Facades\Hash;


class ResetPasswordController extends Controller
{
    /**
     * Create token password reset.
     *
     * @param  ResetPasswordRequest $request
     * @return JsonResponse
     */
    public function sendMail(Request $request)
    {
        $data = $request->all();
        $user = User::where('email', $data['email'])->first();
        if (!$user)
        return response()->json([
            'message' => 'Email bạn vừa nhập không tồn tại trên hệ thống.'
        ], 404);
        $passwordReset = PasswordReset::updateOrCreate(
            ['email' => $user->email,], 
            ['token' => Str::random(60),]);
        if ($passwordReset) {
            $user->notify(new ResetPasswordRequest($passwordReset->token));
        }
        return response()->json([
            'message' => 'Chúng tôi đã gửi một thư đến địa chỉ email của bạn. Vui lòng check mail để thay đổi mật khẩu!'
        ]);
    }

    public function reset(Request $request)
    {
        $data = $request->all();
        $token = $data['token'];
        $newPasword = Hash::make($data['password']);
        $passwordReset = PasswordReset::where('token', $token)->first();
        if (!$passwordReset)
        return response()->json([
            'message' => 'Không thể đổi mật khẩu, token không hợp lệ.'
        ], 404);
        if (Carbon::parse($passwordReset->updated_at)->addMinutes(120)->isPast()) {
            $passwordReset->delete();

            return response()->json([
                'message' => 'Email đã hết hạn! Bạn không thể thay đổi mật khẩu.',
            ], 422);
        }
        $user = User::where('email', $passwordReset->email)->firstOrFail();

        $user->password = $newPasword;
        $user->save();
        $passwordReset->delete();

        return response()->json([
            'success' => $user,
        ]);
    }
}
