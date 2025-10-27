<?php

use App\Models\ContactMessage;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('model:prune', [
    '--model' => [ContactMessage::class],
])->daily()
  ->name('prune-contact-messages')
  ->withoutOverlapping()
  ->onOneServer();
