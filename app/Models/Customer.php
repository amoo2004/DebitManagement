<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;
    protected $fillable = [
        'full_name',
        'phone',
        'address',
        'national_id',
        'notes',
    ];

    public function loans()
    {
        return $this->hasMany(Loan::class);
    }

    public function activeLoans()
    {
        return $this->loans()->whereIn('status', ['pending', 'paying', 'overdue']);
    }

    public function smsLogs()
    {
        return $this->hasMany(SmsLog::class);
    }

    public function totalDebt()
    {
        return $this->loans()->sum('remaining_amount');
    }
}
