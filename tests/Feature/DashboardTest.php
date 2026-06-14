<?php

use App\Models\User;

test('dashboard loads for authenticated user', function () {
    $user = User::factory()->create(['role' => 'admin']);
    $this->actingAs($user);
    $response = $this->get(route('admin.dashboard'));
    $response->assertStatus(200);
});

test('unauthenticated user is redirected to login', function () {
    $response = $this->get(route('admin.dashboard'));
    $response->assertRedirect(route('login'));
});
