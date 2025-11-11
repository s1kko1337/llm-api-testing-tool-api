<?php

namespace App\Providers;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\CustomToken;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Laravel\Sanctum\Sanctum;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        JsonResource::withoutWrapping();

        Sanctum::usePersonalAccessTokenModel(CustomToken::class);

        VerifyEmail::toMailUsing(static function ($notifiable, $url) {
            return (new MailMessage)
                ->subject('Подтверждение адреса электронной почты')
                ->greeting('Здравствуйте!')
                ->line('Пожалуйста, нажмите кнопку ниже, чтобы подтвердить Ваш адрес электронной почты.')
                ->action('Подтвердить Email', $url)
                ->line('Если Вы не создавали учетную запись, никаких дальнейших действий не требуется.')
                ->salutation('С уважением, команда SupportApp');
        });

        ResetPassword::createUrlUsing(function ($user, string $token) {
            $frontend_url = 'http://localhost'; // В проде убрать
            return rtrim($frontend_url, '/') . '/reset-password/' . $token . '?email=' . urlencode($user->email);
        });

        ResetPassword::toMailUsing(function ($notifiable, string $token) {
            $url = 'http://localhost' . '/reset-password/' . $token . '?email=' . urlencode($notifiable->getEmailForPasswordReset());

            return (new MailMessage)
                ->subject('Сброс пароля')
                ->greeting('Здравствуйте!')
                ->line('Вы получили это письмо, потому что мы получили запрос на сброс пароля для вашей учетной записи.')
                ->action('Сбросить пароль', $url)
                ->line('Срок действия ссылки для сброса пароля истечет через ' . config('auth.passwords.users.expire') . ' минут.')
                ->line('Если вы не запрашивали сброс пароля, никаких дальнейших действий не требуется.')
                ->salutation('С уважением');
        });



        RateLimiter::for('api', static function (Request $request) {
            return Limit::perMinute(200)->
            by($request->user()?->id ?: $request->
            ip())->response(
                function (Request $request, array $headers = []) {
                    return response('Too Many Attempts.', 429);
                }
            );
        });
    }
}
