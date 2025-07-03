<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Expenses;
use App\Models\ExpensesStatus;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $currentYear = Carbon::now()->year;
        
        // Gestion du trimestre (navigation)
        $quarterOffset = (int) $request->get('quarter_offset', 0);
        $currentQuarter = $this->getCurrentQuarter($quarterOffset);
        
        // === DONNÉES ANNUELLES ===
        $annualData = $this->getAnnualData($currentYear);
        
        // === DONNÉES TRIMESTRIELLES ===
        $quarterlyData = $this->getQuarterlyData($currentQuarter);
        
        // === DONNÉES GRAPHIQUES ===
        $monthlyChartData = $this->getMonthlyChartData($currentYear);
        $annualChartData = $this->getAnnualChartData($currentYear);
        
        return view('dashboard', compact(
            'annualData',
            'quarterlyData', 
            'currentQuarter',
            'quarterOffset',
            'monthlyChartData',
            'annualChartData'
        ));
    }
    
    private function getCurrentQuarter($offset = 0)
    {
        $now = Carbon::now();
        $currentQuarterStart = $now->copy()->firstOfQuarter();
        
        // Appliquer l'offset (navigation trimestre précédent/suivant)
        if ($offset !== 0) {
            $currentQuarterStart->addQuarters($offset);
        }
        
        return [
            'start' => $currentQuarterStart->copy()->startOfDay(),
            'end' => $currentQuarterStart->copy()->endOfQuarter()->endOfDay(),
            'label' => 'Q' . $currentQuarterStart->quarter . ' ' . $currentQuarterStart->year
        ];
    }
    
    private function getAnnualData($year)
    {
        $userId = auth()->id();
        $user = auth()->user();
        $yearStart = Carbon::createFromDate($year, 1, 1)->startOfDay();
        $yearEnd = Carbon::createFromDate($year, 12, 31)->endOfDay();
        
        // Récupérer les statuts
        $statusPaye = ExpensesStatus::where('name', 'Payée')->first();
        $statusEnvoye = ExpensesStatus::where('name', 'Envoyée')->first();
        $statusEditee = ExpensesStatus::where('name', 'Éditée')->first();
        
        // CA annuel payé (factures payées cette année)
        $expensesPaye = Expenses::with('lines')
            ->where('status_id', $statusPaye?->id)
            ->whereBetween('date_payment_effect', [$yearStart, $yearEnd])
            ->whereHas('quote.project.customer', function ($q) use ($userId) {
            $q->where('user_id', $userId);
            })
            ->get();
        $caAnnuelPaye = $expensesPaye->sum(function($expense) {
            return $expense->calculateAmount();
        });
            
        // Paiements en attente (factures envoyées mais non payées)
        $expensesEnvoye = Expenses::with('lines')
            ->where('status_id', $statusEnvoye?->id)
            ->whereHas('quote.project.customer', function ($q) use ($userId) {
            $q->where('user_id', $userId);
            })
            ->get();
        $paiementsEnAttente = $expensesEnvoye->sum(function($expense) {
            return $expense->calculateAmount();
        });
            
        // Factures éditées non envoyées
        $expensesEditee = Expenses::with('lines')
            ->where('status_id', $statusEditee?->id)
            ->whereHas('quote.project.customer', function ($q) use ($userId) {
            $q->where('user_id', $userId);
            })
            ->get();
        $facturesEditees = $expensesEditee->sum(function($expense) {
            return $expense->calculateAmount();
        });
            
        // CA annuel max (toutes les factures de l'année)
        $caAnnuelMax = $user->ca_max;
            
        // CA restant à faire
        $caRestant = $caAnnuelMax - $caAnnuelPaye;
        
        return [
            'ca_annuel_paye' => $caAnnuelPaye,
            'paiements_en_attente' => $paiementsEnAttente,
            'factures_editees' => $facturesEditees,
            'ca_annuel_max' => $caAnnuelMax,
            'ca_restant' => $caRestant
        ];
    }
    
    private function getQuarterlyData($quarter)
    {
        $userId = auth()->id();
        $user = auth()->user();
        // Récupérer les statuts
        $statusPaye = ExpensesStatus::where('name', 'Payée')->first();
        $statusEnvoye = ExpensesStatus::where('name', 'Envoyée')->first();
        $statusEditee = ExpensesStatus::where('name', 'Éditée')->first();
        
        // CA payé du trimestre
        $expensesPayeTrimestre = Expenses::with('lines')
            ->where('status_id', $statusPaye?->id)
            ->whereBetween('date_payment_effect', [$quarter['start'], $quarter['end']])
            ->whereHas('quote.project.customer', function ($q) use ($userId) {
            $q->where('user_id', $userId);
            })
            ->get();
        $caPayeTrimestre = $expensesPayeTrimestre->sum(function($expense) {
            return $expense->calculateAmount();
        });
            
        // CA estimé (factures envoyées avec date de paiement dans le trimestre)
        $expensesEstimeTrimestre = Expenses::with('lines')
            ->where('status_id', $statusEnvoye?->id)
            ->whereBetween('date_payment_limit', [$quarter['start'], $quarter['end']])
            ->whereHas('quote.project.customer', function ($q) use ($userId) {
            $q->where('user_id', $userId);
            })
            ->get();
        $caEstimeTrimestre = $expensesEstimeTrimestre->sum(function($expense) {
            return $expense->calculateAmount();
        });

        $caEstimeTrimestre += $caPayeTrimestre;
            
        // Charges 
        $chargesPourcentage = $user->charges ?? 0;
        $chargesAPayer = $caPayeTrimestre * ($chargesPourcentage / 100) ;
        $chargesEstimees = $caEstimeTrimestre * ($chargesPourcentage / 100);
        
        return [
            'periode' => $quarter['label'],
            'ca_paye' => $caPayeTrimestre,
            'ca_estime' => $caEstimeTrimestre,
            'charges_a_payer' => $chargesAPayer,
            'charges_estimees' => $chargesEstimees
        ];
    }
    
    private function getMonthlyChartData($year)
    {
        $userId = auth()->id();
        $monthlyData = [];
        $statusPaye = ExpensesStatus::where('name', 'Payée')->first();
        
        for ($month = 1; $month <= 12; $month++) {
            $monthStart = Carbon::createFromDate($year, $month, 1)->startOfMonth();
            $monthEnd = Carbon::createFromDate($year, $month, 1)->endOfMonth();
            
            $monthlyExpenses = Expenses::with('lines')
                ->where('status_id', $statusPaye?->id)
                ->whereBetween('date_payment_effect', [$monthStart, $monthEnd])
                 ->whereHas('quote.project.customer', function ($q) use ($userId) {
                $q->where('user_id', $userId);
                })
                ->get();
                
            $monthlyCA = $monthlyExpenses->sum(function($expense) {
                return $expense->calculateAmount();
            });
                
            $monthlyData[] = [
                'month' => $monthStart->format('M'),
                'amount' => $monthlyCA
            ];
        }
        
        return $monthlyData;
    }
    
    private function getAnnualChartData($year)
    {
        $userId = auth()->id();
        $annualData = [];
        $cumulative = 0;
        $statusPaye = ExpensesStatus::where('name', 'Payée')->first();
        
        for ($month = 1; $month <= 12; $month++) {
            $monthStart = Carbon::createFromDate($year, $month, 1)->startOfMonth();
            $monthEnd = Carbon::createFromDate($year, $month, 1)->endOfMonth();
            
            $monthlyExpenses = Expenses::with('lines')
                ->where('status_id', $statusPaye?->id)
                ->whereBetween('date_payment_effect', [$monthStart, $monthEnd])
                ->whereHas('quote.project.customer', function ($q) use ($userId) {
                $q->where('user_id', $userId);
                })
                ->get();
                
            $monthlyCA = $monthlyExpenses->sum(function($expense) {
                return $expense->calculateAmount();
            });
                
            $cumulative += $monthlyCA;
                
            $annualData[] = [
                'month' => $monthStart->format('M'),
                'cumulative' => $cumulative
            ];
        }
        
        return $annualData;
    }
}