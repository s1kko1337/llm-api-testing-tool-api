<?php

namespace App\Providers;

use App\Models\CustomToken;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
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
