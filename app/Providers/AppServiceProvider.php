<?php

namespace App\Providers;

use App\Models\Event;
use App\Policies\EventPolicy;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use App\Models\ProblemReport;
use Carbon\Carbon;
use App\Services\ProfanityFilter;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(ProfanityFilter::class, function ($app) {
            return new ProfanityFilter();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(Event::class, EventPolicy::class);

        View::composer('components.superAdminNavigation', function ($view) {
            $newReportsCount = ProblemReport::where('created_at', '>=', Carbon::now()->subDay())
                ->where('viewed', false)
                ->count();

            $view->with('newReportsCount', $newReportsCount);
        });
    }
}