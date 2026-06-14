<?php

use App\Models\Customer;
use App\Models\Loan;
use App\Models\Payment;
use App\Models\User;

beforeEach(function () {
    $this->admin = User::factory()->create(['role' => 'admin']);
    $this->customer = Customer::factory()->create();
    $this->loan = Loan::factory()->create([
        'customer_id' => $this->customer->id,
        'created_by' => $this->admin->id,
        'loan_amount' => 50000,
        'paid_amount' => 0,
        'remaining_amount' => 50000,
    ]);
    $this->actingAs($this->admin);
});

test('admin can view payments list', function () {
    Payment::factory()->create(['loan_id' => $this->loan->id, 'created_by' => $this->admin->id]);
    $response = $this->get(route('admin.payments.index'));
    $response->assertStatus(200);
});

test('admin can record payment', function () {
    $response = $this->post(route('admin.payments.store'), [
        'customer_id' => $this->customer->id,
        'amount' => 10000,
        'payment_date' => now()->format('Y-m-d'),
        'payment_method' => 'cash',
    ]);
    $response->assertRedirect();
    $this->assertDatabaseHas('payments', ['amount' => 10000]);
});
