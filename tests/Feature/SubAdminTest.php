<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

function subAdminUser(): User
{
    return User::create([
        'first_name' => 'Admin',
        'last_name' => 'User',
        'email' => 'admin-sub-' . Str::random(5) . '@example.com',
        'role' => 1,
        'is_active' => true,
        'password' => Hash::make('password'),
    ]);
}

function validSubAdminPayload(array $overrides = []): array
{
    return array_merge([
        'first_name' => 'John',
        'last_name' => 'Doe',
        'email' => 'john.doe.' . Str::random(5) . '@example.com',
        'phone' => '1234567890',
        'is_active' => true,
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ], $overrides);
}

it('loads the sub admin index page', function () {
    $this->actingAs(subAdminUser())
        ->get(route('sub-admin.index'))
        ->assertOk()
        ->assertViewIs('pages.admin.sub-admin.index')
        ->assertSee('Sub Admin')
        ->assertSee('Add Sub Admin');
});

it('returns sub admins for yajra datatables ajax requests', function () {
    $admin = subAdminUser();
    User::create(validSubAdminPayload([
        'email' => 'sub1@example.com',
        'role' => 1,
    ]));

    $this->actingAs($admin)
        ->getJson(route('sub-admin.index'), ['X-Requested-With' => 'XMLHttpRequest'])
        ->assertOk()
        ->assertJsonStructure([
            'draw',
            'recordsTotal',
            'recordsFiltered',
            'data',
        ]);
});

it('stores a sub admin', function () {
    $admin = subAdminUser();
    $email = 'new.subadmin.' . Str::random(5) . '@example.com';

    $this->actingAs($admin)
        ->postJson(route('sub-admin.store'), validSubAdminPayload([
            'email' => $email,
        ]))
        ->assertOk()
        ->assertJson([
            'message' => 'Sub Admin created successfully!',
        ]);

    $this->assertDatabaseHas('users', [
        'first_name' => 'John',
        'last_name' => 'Doe',
        'email' => $email,
        'role' => 1,
    ]);
});

it('updates a sub admin while keeping email uniqueness scoped', function () {
    $admin = subAdminUser();
    $email = 'john.doe.' . Str::random(5) . '@example.com';
    $subAdmin = User::create(validSubAdminPayload([
        'email' => $email,
        'role' => 1,
    ]));

    $this->actingAs($admin)
        ->putJson(route('sub-admin.update', $subAdmin), validSubAdminPayload([
            'first_name' => 'Jane',
            'last_name' => 'Smith',
            'email' => $email,
            'password' => '',
            'password_confirmation' => '',
        ]))
        ->assertOk()
        ->assertJson([
            'message' => 'Sub Admin updated successfully!',
        ]);

    $this->assertDatabaseHas('users', [
        'id' => $subAdmin->id,
        'first_name' => 'Jane',
        'last_name' => 'Smith',
        'role' => 1,
    ]);
});

it('deletes a sub admin', function () {
    $admin = subAdminUser();
    $subAdmin = User::create(validSubAdminPayload([
        'role' => 1,
    ]));

    $this->actingAs($admin)
        ->deleteJson(route('sub-admin.destroy', $subAdmin))
        ->assertOk()
        ->assertJson([
            'message' => 'Sub Admin deleted successfully!',
        ]);

    $this->assertDatabaseMissing('users', [
        'id' => $subAdmin->id,
    ]);
});

it('validates sub admin fields', function () {
    $admin = subAdminUser();
    $existingEmail = 'exists.' . Str::random(5) . '@example.com';
    User::create(validSubAdminPayload([
        'email' => $existingEmail,
        'role' => 1,
    ]));

    $this->actingAs($admin)
        ->postJson(route('sub-admin.store'), [
            'first_name' => '',
            'email' => $existingEmail,
            'password' => '123',
            'password_confirmation' => '456',
        ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors([
            'first_name',
            'email',
            'password',
        ]);
});

it('stores sub admin with photo upload', function () {
    Storage::fake('public');
    $admin = subAdminUser();
    $file = UploadedFile::fake()->image('avatar.jpg');
    $email = 'photo.sub.' . Str::random(5) . '@example.com';

    $this->actingAs($admin)
        ->postJson(route('sub-admin.store'), validSubAdminPayload([
            'email' => $email,
            'photo' => $file,
        ]))
        ->assertOk()
        ->assertJson([
            'message' => 'Sub Admin created successfully!',
        ]);

    $this->assertDatabaseHas('users', [
        'email' => $email,
        'role' => 1,
    ]);
});
