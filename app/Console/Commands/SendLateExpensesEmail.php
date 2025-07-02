<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Mail\LateExpensesReport;
use App\Models\User;

class SendLateExpensesEmail extends Command
{
    protected $signature = 'expenses:send-late-report';
    protected $description = 'Envoie un email aux utilisateurs avec la liste de leurs factures en retard';

    public function handle()
    {
        $today = now();

        // On récupère tous les users avec leurs relations nécessaires
        $users = User::with('customers.projects.quotes.expenses')->get();

        foreach ($users as $user) {
            $lateExpenses = collect();

            foreach ($user->customers as $customer) {
                foreach ($customer->projects as $project) {
                    foreach ($project->quotes as $quote) {
                        foreach ($quote->expenses as $expense) {
                            if (is_null($expense->date_payment_effect) && $expense->date_payment_limit < $today) {
                                $lateExpenses->push($expense);
                            }
                        }
                    }
                }
            }

            if ($lateExpenses->isNotEmpty()) {
                Mail::to($user->email)->send(new LateExpensesReport($user, $lateExpenses));
                $this->info("Mail envoyé à {$user->email}");
            }
        }

        return Command::SUCCESS;
    }
}
