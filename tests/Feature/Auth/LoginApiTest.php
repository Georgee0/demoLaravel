<?php

use App\Models\User;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

beforeEach(function () {
    // Clear rate limiter for email to ensure clean state before each test
    $throttleKey = Str::transliterate(Str::lower('test@example.com') . '|' . '127.0.0.1');
    RateLimiter::clear($throttleKey);
});


// Test to ensure login credentials via API
test('Test login credentials via API', function () {
    $user = User::factory()->create([
        'email' => 'test@example.com',
        'password' => bcrypt('password123'),
    ]);

    $response = $this->postJson('/api/login', [
        'email' => 'test@example.com',
        'password' => 'password123',
    ]);

    $response->assertStatus(200)
        ->assertJsonStructure([
            'user' => [
                'id', 'name', 'email'
            ],
        ])
        ->assertJson([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
        ]
    );
});

// Test to ensure login fails with wrong email
test('User can not login with wrong email', function () {
    $response = $this->postJson('/api/login', [
        'email' => 'wrong@example.com',
        'password' => 'password123',
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['email']);

    $this->assertGuest();
});

// Test to ensure login works after rate limit is cleared
test('User can not login with wrong password', function () {
    $user = User::factory()->create([
        'email' => 'test@example.com',
        'password' => bcrypt('correctpassword'),
    ]);
    $response = $this->postJson('/api/login', [
        'email' => 'test@example.com',
        'password' => 'wrongpassword',
    ]);
    $response->assertStatus(422)
        ->assertJsonValidationErrors(['email']);
    $this->assertGuest();   
});

// Test to ensure login is rate limited after 6 attempts
test('Login is rate limited after 6 attempts', function () {
    $user = User::factory()->create([
        'email' => 'test@example.com',
        'password' => bcrypt('password123'),
    ]);
    for ($i = 0; $i < 5; $i++) {
        $response = $this->postJson('/api/login', [
            'email' => 'test@example.com',
            'password' => 'wrongpassword',
        ]);
    }
    $response = $this->postJson('/api/login', [
        'email' => 'test@example.com',
        'password' => 'wrongpassword',
    ]);
    $response->assertStatus(429)
        ->assertJsonValidationErrors(['email']);
});

// Test to ensure login works after rate limit is cleared
test('Login works after rate limit is cleared', function () {
    $user = User::factory()->create([
        'email' => 'test@example.com',
        'password' => bcrypt('password123'),
    ]);
    $throttleKey = Str::transliterate(Str::lower('test@example.com') . '|' . '127.0.0.1');
    RateLimiter::clear($throttleKey);
    $response = $this->postJson('/api/login', [
        'email' => 'test@example.com',
        'password' => 'password123',
    ]);
    $response->assertStatus(200)
        ->assertJsonStructure([
            'user' => [
                'id', 'name', 'email'
            ],
            'token',
        ]);
    $this->assertAuthenticatedAs($user);
});

// Test to ensure login endpoint requires guest middleware
test('login endpoint require guest middleware', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user, 'sanctum')->postJson('/api/login', [
        'email' => 'test@example.com',
        'password' => 'password123',
    ]);
    $response->assertStatus(302);
});

// Test to ensure sensitive data is not exposed
test('Login response contain correct user data structure', function () {
    $user = User::factory()->create([
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => bcrypt('password123'),
    ]);
    $response = $this->postJson('/api/login', [
        'email' => 'test@example.com',
        'password' => 'password123',
    ]);
    $response->assertStatus(200)
        ->assertJson([
            'user' => [
                'id' => $user->id,
                'name' => 'Test User',
                'email' => 'test@example.com',
            ],
        ]);

    $responseData = $response->json();
    expect($responseData['user'])->not->toHaveKeys(['password']);
    expect($responseData['user'])->not->toHaveKeys(['remember_token']);
});