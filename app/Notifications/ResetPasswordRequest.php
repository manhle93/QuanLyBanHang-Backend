<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPasswordRequest extends Notification implements ShouldQueue
{
    use Queueable;
    protected $token;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($token)
    {
        $this->token = $token;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $url = url('http://pccc.howizbiz.com/#/matkhaumoi/' . $this->token);
        return (new MailMessage)
                    ->subject('Quên mật khẩu đăng nhập!')
                    ->line('Bạn quên mật khẩu đăng nhập! Nhấp vào nút bên dưới để đặt lại mật khẩu')
                    ->action('ĐẶT LẠI MẬT KHẨU', url($url))
                    ->line('Lưu ý ! Email có hiệu lực trong 120 phút')
                    ->line('Nếu bạn không quên mật khẩu, hãy bỏ qua email này')
                    ->line('Cảm ơn đã sử dụng dịch vụ của chúng tôi!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
