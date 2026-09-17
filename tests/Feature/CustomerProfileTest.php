<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

function profileCustomer(): User
{
    return User::create([
        'first_name' => 'John',
        'last_name' => 'Doe',
        'email' => 'john.profile@example.com',
        'phone' => '+1555123456',
        'gender' => 'male',
        'address' => '123 Main St, New York',
        'role' => 2,
        'is_active' => true,
        'email_verified_at' => now(),
        'referral_code' => 'JOHN123',
        'password' => Hash::make('password123'),
    ]);
}

it('loads the customer profile page', function () {
    $user = profileCustomer();

    $this->actingAs($user)
        ->get(route('customer.profile.index'))
        ->assertOk()
        ->assertViewIs('pages.customer.profile')
        ->assertSee('John')
        ->assertSee('Doe')
        ->assertSee('JOHN123');
});

it('updates customer profile information', function () {
    $user = profileCustomer();

    $this->actingAs($user)
        ->post(route('customer.profile.update'), [
            'first_name' => 'Johnny',
            'last_name' => 'Smith',
            'email' => 'johnny.updated@example.com',
            'phone' => '+1999888777',
            'gender' => 'male',
            'address' => '456 Updated Ave',
        ])
        ->assertRedirect(route('customer.profile.index'))
        ->assertSessionHas('success');

    $this->assertDatabaseHas('users', [
        'id' => $user->id,
        'first_name' => 'Johnny',
        'last_name' => 'Smith',
        'email' => 'johnny.updated@example.com',
        'phone' => '+1999888777',
        'address' => '456 Updated Ave',
    ]);
});

it('updates customer password when provided', function () {
    $user = profileCustomer();

    $this->actingAs($user)
        ->post(route('customer.profile.update'), [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john.profile@example.com',
            'password' => 'newsecret123',
            'password_confirmation' => 'newsecret123',
        ])
        ->assertRedirect(route('customer.profile.index'));

    expect(Hash::check('newsecret123', $user->fresh()->password))->toBeTrue();
});

it('uploads and resizes profile photo using intervention image', function () {
    $user = profileCustomer();
    Storage::fake('public');

    $photo = UploadedFile::fake()->image('avatar.jpg', 600, 600);

    $this->actingAs($user)
        ->post(route('customer.profile.update'), [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john.profile@example.com',
            'photo' => $photo,
        ])
        ->assertRedirect(route('customer.profile.index'));

    $updatedUser = $user->fresh();
    expect($updatedUser->photo)->not->toBeNull();
    expect(file_exists(public_path(Illuminate\Support\Str::after($updatedUser->photo, 'public/'))))->toBeTrue();

    // Clean up created file
    if (file_exists(public_path(Illuminate\Support\Str::after($updatedUser->photo, 'public/')))) {
        unlink(public_path(Illuminate\Support\Str::after($updatedUser->photo, 'public/')));
    }
});
