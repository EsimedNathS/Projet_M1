<?php
namespace App\Models;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Collection;
use Carbon\Carbon;
use App\Models\Quotes;
use App\Models\Expenses;

class User extends Authenticatable
{
    use Notifiable;

    protected $table = 'users';
    public $timestamps = true;

    protected $casts = [
        'admin' => 'boolean',
        'ca_max' => 'decimal:2',
        'created_at' => 'datetime',
        'charges' => 'decimal:2'
    ];

    protected $hidden = [
        'password'
    ];

    protected $fillable = [
        'first_name',
        'last_name',
        'phone',
        'email',
        'adresse',
        'ca_max',
        'charges',
        'password',
        'admin'
    ];

    /**
     * Relations
     */
    public function customers()
    {
        return $this->hasMany(Customer::class);
    }

    public function projects()
    {
        return $this->hasManyThrough(Project::class, Customer::class);
    }

    public function quotes()
    {
        return Quotes::query()
            ->join('projects', 'quotes.project_id', '=', 'projects.id')
            ->join('customers', 'projects.customer_id', '=', 'customers.id')
            ->where('customers.user_id', $this->id)
            ->select('quotes.*');
    }

    public function expenses()
    {
        return Expenses::query()
            ->join('quotes', 'expenses.quote_id', '=', 'quotes.id')
            ->join('projects', 'quotes.project_id', '=', 'projects.id')
            ->join('customers', 'projects.customer_id', '=', 'customers.id')
            ->where('customers.user_id', $this->id)
            ->select('expenses.*');
    }

    /**
     * Accesseurs
     */
    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function getFormattedCaMaxAttribute()
    {
        return number_format($this->ca_max ?? 0, 2) . ' €';
    }

    public function getFormattedChargesAttribute()
    {
        return number_format($this->charges ?? 0, 2) . ' €';
    }

    /**
     * Méthodes utilitaires
     */
    public function getTotalCA()
    {
        // Calcul du CA basé sur les lignes de devis acceptés
        $totalCA = 0;
        
        // $quotes = $this->quotes()->where('status', 'accepted')->with('lines')->get();
        
        // foreach ($quotes as $quote) {
        //     $totalCA += $quote->lines->sum(function($line) {
        //         return $line->unit_price * $line->quantity;
        //     });
        // }
        
        // return $totalCA;
        return $totalCA;

    }

    public function getTotalExpenses()
    {
        // Calcul des dépenses basé sur les lignes de dépenses
        $totalExpenses = 0;
        
        $expenses = $this->expenses()->with('lines')->get();
        
        foreach ($expenses as $expense) {
            $totalExpenses += $expense->lines->sum(function($line) {
                return $line->unit_price * $line->quantity;
            });
        }
        
        return $totalExpenses;
    }

    public function getNetResult()
    {
        return $this->getTotalCA() - $this->getTotalExpenses();
    }

    public function isAdmin()
    {
        return $this->admin === true;
    }

    public function getRemainingCA()
    {
        if (!$this->ca_max) return null;
        $currentCA = $this->getTotalCA();
        return max(0, $this->ca_max - $currentCA);
    }

    public function getCARatio()
    {
        if (!$this->ca_max) return 0;
        $currentCA = $this->getTotalCA();
        return min(100, ($currentCA / $this->ca_max) * 100);
    }

    /**
     * Scopes
     */
    public function scopeAdmins($query)
    {
        return $query->where('admin', true);
    }

    public function scopeRegularUsers($query)
    {
        return $query->where('admin', false)->orWhereNull('admin');
    }

    public function scopeWithCA($query)
    {
        return $query->whereNotNull('ca_max')->where('ca_max', '>', 0);
    }
}