<?php
namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use App\Events\DocumentSubmitted;
use App\Listeners\submitDocumentListener;
use App\Events\NewAnnouncement;
use App\Listeners\NewAnnouncementListener;

class EventServiceProvider extends ServiceProvider
{
protected $listen = [
    DocumentSubmitted::class => [
        submitDocumentListener::class,
    ],
    
    \App\Events\DocumentStatusUpdated::class => [
        \App\Listeners\SendDocumentStatusNotification::class,
        \Illuminate\Log\Events\MessageLogged::class,
    ],
     \App\Events\NewChatMessage::class => [
        \App\Listeners\SendChatNotification::class,
    ],
    NewAnnouncement::class => [
        NewAnnouncementListener::class,
    ],
    
];
    

    public function boot()
    {
        parent::boot();
        
    }
}