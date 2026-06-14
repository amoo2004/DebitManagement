<?php
namespace Database\Seeders;

use App\Models\User;
use App\Models\Customer;
use App\Models\Loan;
use App\Models\Payment;
use App\Models\SmsLog;
use App\Models\Notification;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'phone' => '0712345678',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'status' => true,
        ]);

        User::create([
            'name' => 'Staff',
            'email' => 'staff@example.com',
            'phone' => '0712345679',
            'password' => Hash::make('password'),
            'role' => 'staff',
            'status' => true,
        ]);

        User::create([
            'name' => 'Staff2',
            'email' => 'staff2@example.com',
            'phone' => '0712345673',
            'password' => Hash::make('password'),
            'role' => 'staff',
            'status' => true,
        ]);

        $customers = [
            ['full_name' => 'John Doe', 'phone' => '0722000001', 'address' => '123 Main St', 'national_id' => 'ID001'],
            ['full_name' => 'Jane Smith', 'phone' => '0722000002', 'address' => '456 Oak Ave', 'national_id' => 'ID002'],
            ['full_name' => 'Bob Johnson', 'phone' => '0722000003', 'address' => '789 Pine Rd', 'national_id' => 'ID003'],
            ['full_name' => 'Alice Williams', 'phone' => '0722000004', 'address' => '321 Elm St', 'national_id' => 'ID004'],
            ['full_name' => 'Charlie Brown', 'phone' => '0722000005', 'address' => '654 Maple Dr', 'national_id' => 'ID005'],
        ];

        foreach ($customers as $data) {
            Customer::create($data);
        }

        $loans = [
            ['customer_id' => 1, 'product_name' => 'TV Set', 'loan_amount' => 50000, 'loan_date' => '2026-01-15', 'due_date' => '2026-04-15', 'created_by' => 1],
            ['customer_id' => 2, 'product_name' => 'Refrigerator', 'loan_amount' => 75000, 'loan_date' => '2026-02-01', 'due_date' => '2026-05-01', 'created_by' => 1],
            ['customer_id' => 3, 'product_name' => 'Laptop', 'loan_amount' => 120000, 'loan_date' => '2026-03-10', 'due_date' => '2026-06-10', 'created_by' => 2],
            ['customer_id' => 4, 'product_name' => 'Washing Machine', 'loan_amount' => 45000, 'loan_date' => '2026-04-05', 'due_date' => '2026-07-05', 'created_by' => 2],
            ['customer_id' => 5, 'product_name' => 'Smartphone', 'loan_amount' => 35000, 'loan_date' => '2026-05-20', 'due_date' => '2026-08-20', 'created_by' => 1],
        ];

        foreach ($loans as $data) {
            $data['paid_amount'] = 0;
            $data['remaining_amount'] = $data['loan_amount'];
            $data['status'] = 'paying';
            Loan::create($data);
        }

        Payment::create([
            'loan_id' => 1,
            'amount' => 10000,
            'payment_date' => '2026-02-15',
            'payment_method' => 'cash',
            'notes' => 'First installment',
            'created_by' => 1,
        ]);

        Payment::create([
            'loan_id' => 1,
            'amount' => 10000,
            'payment_date' => '2026-03-15',
            'payment_method' => 'mobile_money',
            'notes' => 'Second installment',
            'created_by' => 1,
        ]);

        Payment::create([
            'loan_id' => 2,
            'amount' => 15000,
            'payment_date' => '2026-03-01',
            'payment_method' => 'bank_transfer',
            'notes' => 'Partial payment',
            'created_by' => 2,
        ]);

        Payment::create([
            'loan_id' => 3,
            'amount' => 20000,
            'payment_date' => '2026-04-10',
            'payment_method' => 'cash',
            'created_by' => 1,
        ]);

        $loan1 = Loan::find(1);
        $loan1->paid_amount = 20000;
        $loan1->remaining_amount = 30000;
        $loan1->save();

        $loan2 = Loan::find(2);
        $loan2->paid_amount = 15000;
        $loan2->remaining_amount = 60000;
        $loan2->save();

        $loan3 = Loan::find(3);
        $loan3->paid_amount = 20000;
        $loan3->remaining_amount = 100000;
        $loan3->save();

        Notification::create([
            'title' => 'Welcome',
            'message' => 'Welcome to the Debt Management System',
            'user_id' => 1,
            'type' => 'info',
            'read_status' => false,
        ]);
    }
}
