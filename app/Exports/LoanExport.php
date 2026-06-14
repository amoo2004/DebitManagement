<?php
namespace App\Exports;

use App\Models\Customer;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class LoanExport implements FromCollection, WithHeadings, WithMapping
{
    protected $request;

    public function __construct($request)
    {
        $this->request = $request;
    }

    public function collection(): Collection
    {
        $customerIds = Customer::when($this->request->filled('search'), function ($q) {
            $s = $this->request->search;
            $q->where('full_name', 'like', "%{$s}%")->orWhere('phone', 'like', "%{$s}%");
        })->pluck('id');

        $query = \App\Models\Loan::with('customer')
            ->selectRaw('customer_id, COUNT(*) as total_loans, SUM(loan_amount) as total_amount, SUM(paid_amount) as total_paid, SUM(remaining_amount) as total_remaining')
            ->whereIn('customer_id', $customerIds)
            ->groupBy('customer_id');

        if ($this->request->filled('status')) {
            if ($this->request->status === 'unpaid') {
                $query->whereIn('status', ['pending', 'paying', 'overdue']);
            } else {
                $query->where('status', $this->request->status);
            }
        }

        if ($this->request->filled('date_from')) {
            $query->whereDate('loan_date', '>=', $this->request->date_from);
        }

        if ($this->request->filled('date_to')) {
            $query->whereDate('loan_date', '<=', $this->request->date_to);
        }

        return $query->oldest('customer_id')->get();
    }

    public function headings(): array
    {
        return [
            'Customer',
            'Phone',
            'Total Loans',
            'Total Amount',
            'Total Paid',
            'Total Remaining',
        ];
    }

    public function map($loan): array
    {
        return [
            $loan->customer->full_name,
            $loan->customer->phone,
            $loan->total_loans,
            $loan->total_amount,
            $loan->total_paid,
            $loan->total_remaining,
        ];
    }
}
