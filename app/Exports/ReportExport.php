<?php
namespace App\Exports;

use App\Models\Payment;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ReportExport implements FromCollection, WithHeadings, WithMapping
{
    protected $request;

    public function __construct($request)
    {
        $this->request = $request;
    }

    public function collection(): Collection
    {
        $query = Payment::with('loan.customer', 'createdBy');

        if ($this->request->filled('date_from')) {
            $query->whereDate('payment_date', '>=', $this->request->date_from);
        }

        if ($this->request->filled('date_to')) {
            $query->whereDate('payment_date', '<=', $this->request->date_to);
        }

        return $query->latest()->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Customer',
            'Loan Product',
            'Amount',
            'Payment Date',
            'Method',
            'Reference',
            'Recorded By',
            'Notes',
        ];
    }

    public function map($payment): array
    {
        return [
            $payment->id,
            $payment->loan->customer->full_name ?? 'N/A',
            $payment->loan->product_name ?? 'N/A',
            $payment->amount,
            $payment->payment_date->format('Y-m-d'),
            $payment->payment_method,
            $payment->reference_number ?? 'N/A',
            $payment->createdBy->name ?? 'N/A',
            $payment->notes ?? 'N/A',
        ];
    }
}
