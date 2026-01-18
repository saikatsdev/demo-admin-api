<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    public const HOME = '/home';

    public function boot(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(500)->by($request->user()?->id ?: $request->ip());
        });

        $this->routes(function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            Route::middleware('api')
                ->prefix('api/admin')
                ->group(base_path('routes/user.php'));

            Route::middleware('api')
                ->prefix('api/admin')
                ->group(base_path('routes/blog_post.php'));

            Route::middleware('api')
                ->prefix('api/admin')
                ->group(base_path('routes/cms.php'));

            Route::middleware('api')
                ->prefix('api/admin')
                ->group(base_path('routes/product.php'));

            Route::middleware('api')
                ->prefix('api/admin')
                ->group(base_path('routes/order.php'));

            Route::middleware('api')
                ->prefix('api/admin')
                ->group(base_path('routes/report.php'));

            Route::middleware('api')
                ->prefix('api/admin')
                ->group(base_path('routes/setting.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });
    }
}
