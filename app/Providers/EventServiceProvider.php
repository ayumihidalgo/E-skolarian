<?php
namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use App\Events\DocumentSubmitted;
use App\Listeners\submitDocumentListener;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        DocumentSubmitted::class => [
            submitDocumentListener::class,
        ],
    ];

    public function boot()
    {
        parent::boot();
        // ...existing code...
    }
}