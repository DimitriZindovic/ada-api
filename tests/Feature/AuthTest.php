<?php

use App\Models\User;
use Laravel\Sanctum\Sanctum;

test('user can register', function () {
    $unique = uniqid();
    $userData = [
        'firstName' => 'Alice',
        'lastName' => 'Johnson',
        'username' => 'alicejohnson_' . $unique,
        'phone' => '+33555' . rand(100000, 999999),
        'email' => 'alice' . $unique . '@example.com',
        'password' => 'Password123!',
        'password_confirmation' => 'Password123!',
    ];

    $response = $this->postJson('/api/register', $userData);

    $response->assertStatus(201);
    $response->assertJsonStructure(['token']);

    $this->assertDatabaseHas('users', [
        'first_name' => $userData['firstName'],
        'last_name' => $userData['lastName'],
        'username' => $userData['username'],
        'phone' => $userData['phone'],
        'email' => $userData['email'],
    ]);

    $this->assertNotEmpty($response->json('token'));
});

test('user can login with valid phone and password', function () {
    $user = User::first();

    $response = $this->postJson('/api/login', [
        'phone' => $user->phone,
        'password' => 'Password123!',
    ]);

    $response->assertStatus(200);
    $response->assertJsonStructure(['token']);

    $this->assertNotEmpty($response->json('token'));
});

test('user can login with valid email and password', function () {
    $user = User::first();

    $response = $this->postJson('/api/login', [
        'email' => $user->email,
        'password' => 'Password123!',
    ]);

    $response->assertStatus(200);
    $response->assertJsonStructure(['token']);
});

test('authenticated user can logout successfully', function () {
    $user = User::first();

    Sanctum::actingAs($user);

    $response = $this->postJson('/api/logout');

    $response->assertStatus(200);

    $user->refresh();
});
