<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Property;
use App\Models\Inquiry;
use App\Models\Agent;
use App\Policies\PropertyPolicy;
use App\Policies\InquiryPolicy;
use App\Policies\AgentPolicy;


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
        $policies = [
        Property::class => PropertyPolicy::class,
        Inquiry::class  => InquiryPolicy::class,
        Agent::class    => AgentPolicy::class,
    ];

    }
}
