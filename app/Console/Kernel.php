<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Enregistrement des commandes artisan personnalisées
     */
    protected $commands = [
        // Ajoute ici ta commande si elle n’est pas auto-discoverée
        \App\Console\Commands\SendLateExpensesEmail::class,
    ];

    /**
     * Planification des tâches
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('expenses:send-late-report')->dailyAt('08:00');
    }

    /**
     * Chargement des fichiers de commandes
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
