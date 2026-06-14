<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'loan_date' => 'date',
            'due_date' => 'date',
            'loan_amount' => 'decimal:2',
            'paid_amount' => 'decimal:2',
            'remaining_amount' => 'decimal:2',
        ];
    }

    protected $fillable = [
        'customer_id',
        'product_name',
        'loan_amount',
        'paid_amount',
        'remaining_amount',
        'loan_date',
        'due_date',
        'due_time',
        'status',
        'created_by',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function updateStatus(): void
    {
        if ((float) $this->remaining_amount <= 0) {
            $this->status = 'completed';
        } elseif ($this->due_date <= now()->subDays(5)->toDateString()) {
            $this->status = 'overdue';
        } else {
            $this->status = 'paying';
        }
        $this->save();

        if ($this->status === 'completed') {
            $customer = $this->customer;
            if (!$customer->loans()->whereIn('status', ['pending', 'paying', 'overdue'])->exists()) {
                $loanIds = $customer->loans()->pluck('id');
                Payment::whereIn('loan_id', $loanIds)->delete();
                $customer->loans()->delete();
            }
        }
    }
}
