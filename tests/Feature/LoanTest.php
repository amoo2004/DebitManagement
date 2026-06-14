<?php

use App\Models\Customer;
use App\Models\Loan;
use App\Models\User;

beforeEach(function () {
    $this->admin = User::factory()->create(['role' => 'admin']);
    $this->customer = Customer::factory()->create();
    $this->actingAs($this->admin);
});

test('admin can view loans list', function () {
    Loan::factory()->create(['customer_id' => $this->customer->id, 'created_by' => $this->admin->id]);
    $response = $this->get(route('admin.loans.index'));
    $response->assertStatus(200);
});

test('admin can create loan', function () {
    $response = $this->post(route('admin.loans.store'), [
        'customer_id' => $this->customer->id,
        'product_name' => 'Test Product',
        'loan_amount' => 50000,
        'loan_date' => now()->format('Y-m-d'),
        'due_date' => now()->addMonth()->format('Y-m-d'),
    ]);
    $response->assertRedirect();
    $this->assertDatabaseHas('loans', ['product_name' => 'Test Product']);
});

test('admin can view loan details', function () {
    $loan = Loan::factory()->create(['customer_id' => $this->customer->id, 'created_by' => $this->admin->id]);
    $response = $this->get(route('admin.loans.show', $loan));
    $response->assertStatus(200);
});
