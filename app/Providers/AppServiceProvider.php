<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use Cloudinary\Configuration\Configuration;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;


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
     *
     * This method is called after all the other service providers have been registered,
     * but before any bootstrapping has taken place. Here we can configure the Cloudinary
     * library and set up rate limiting for API requests.
     *
     * @return void
     */
    public function boot(): void
    {
        // Configure the Cloudinary library
      
       
     Configuration::instance([
            'cloud' => [
                'cloud_name' => config('cloudinary.cloud_name'),
                'api_key'    => config('cloudinary.api_key'),
                'api_secret' => config('cloudinary.api_secret'),
            ],
            'url' => [
                'secure' => true
            ]
        ]);

        // Set up rate limiting for API requests
        // This will limit API requests to 60 per minute per user or IP address
        RateLimiter::for('api', function(Request $request) {
            return Limit::perMinute(40)->by($request->user()?->id ?: $request->ip());
        });

        RateLimiter::for('guest', function(Request $request) {
            return Limit::perMinute(15)->by($request->ip());
        });

        RateLimiter::for('public-properties', function (Request $request) {
            return [
                Limit::perMinute(80)->by($request->ip()),     
                Limit::perMinute(15)->by('search:' . $request->ip()), 
            ];
        });


    }

}
