<?php

namespace App\Observers;

use App\User;
use Exception;
use App\LichSuHoatDong;

class UserObserver
{
    /**
     * Handle the user "created" event.
     *
     * @param  \App\User  $user
     * @return void
     */
    public function created(User $user)
    {
        if (isset($user)) {
            try {
                $userLogin = auth()->user();
                LichSuHoatDong::create([
                    'reference_id' => $user->id,
                    'type' => 'user',
                    'hanh_dong' => 'created',
                    'user_id' => $userLogin->id,
                    'noi_dung' => 'Tạo tài khoản người dùng'
                ]);
            } catch (Exception $e) {
                dd($e);
            }
        }
    }

    /**
     * Handle the user "updated" event.
     *
     * @param  \App\User  $user
     * @return void
     */
    public function updated(User $user)
    {
        if (isset($user)) {
            try {
                $userLogin = auth()->user();
                LichSuHoatDong::create([
                    'reference_id' => $user->id,
                    'type' => 'user',
                    'hanh_dong' => 'updated',
                    'user_id' => $userLogin->id,
                    'noi_dung' => 'Cập nhật thông tin người dùng'
                ]);
            } catch (Exception $e) {
                dd($e);
            }
        }
    }

    /**
     * Handle the user "deleted" event.
     *
     * @param  \App\User  $user
     * @return void
     */
    public function deleted(User $user)
    {
        if (isset($user)) {
            try {
                $userLogin = auth()->user();
                LichSuHoatDong::create([
                    'reference_id' => $user->id,
                    'type' => 'user',
                    'hanh_dong' => 'deleted',
                    'user_id' => $userLogin->id,
                    'noi_dung' => 'Xóa người dùng tên '.$user->name.', email: '.$user->email.', tên đăng nhập: '.$user->username
                ]);
            } catch (Exception $e) {
                dd($e);
            }
        }
    }

    /**
     * Handle the user "restored" event.
     *
     * @param  \App\User  $user
     * @return void
     */
    public function restored(User $user)
    {
        //
    }

    /**
     * Handle the user "force deleted" event.
     *
     * @param  \App\User  $user
     * @return void
     */
    public function forceDeleted(User $user)
    {
        //
    }
}
