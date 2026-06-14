<?php

use App\Models\Customer;
use App\Models\User;

beforeEach(function () {
    $this->admin = User::factory()->create(['role' => 'admin']);
    $this->actingAs($this->admin);
});

test('admin can view customers list', function () {
    Customer::factory()->count(3)->create();
    $response = $this->get(route('admin.customers.index'));
    $response->assertStatus(200);
});

test('admin can create customer', function () {
    $response = $this->post(route('admin.customers.store'), [
        'full_name' => 'Test Customer',
        'phone' => '0712345678',
        'address' => '123 Test St',
        'national_id' => 'ID12345',
    ]);
    $response->assertRedirect();
    $this->assertDatabaseHas('customers', ['full_name' => 'Test Customer']);
});

test('admin can view customer details', function () {
    $customer = Customer::factory()->create();
    $response = $this->get(route('admin.customers.show', $customer));
    $response->assertStatus(200);
});

test('admin can update customer', function () {
    $customer = Customer::factory()->create();
    $response = $this->put(route('admin.customers.update', $customer), [
        'full_name' => 'Updated Name',
        'phone' => '0712345679',
    ]);
    $response->assertRedirect();
    $this->assertDatabaseHas('customers', ['full_name' => 'Updated Name']);
});

test('admin can delete customer without loans', function () {
    $customer = Customer::factory()->create();
    $response = $this->delete(route('admin.customers.destroy', $customer));
    $response->assertRedirect();
    $this->assertDatabaseMissing('customers', ['id' => $customer->id]);
});
