<?php

use App\Models\User;

test('api login returns token', function () {
    $user = User::factory()->create(['password' => bcrypt('password'), 'role' => 'admin']);
    $response = $this->postJson('/api/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);
    $response->assertStatus(200);
    $response->assertJsonStructure(['token', 'user']);
});

test('api authentication required for protected routes', function () {
    $response = $this->getJson('/api/customers');
    $response->assertStatus(401);
});
