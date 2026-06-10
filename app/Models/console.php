<?php

use App\Jobs\SyncOrderStatuses;
use Illuminate\Support\Facades\Schedule;

// Synchronisation des statuts de commandes toutes les 5 minutes
Schedule::job(new SyncOrderStatuses)->everyFiveMinutes();
