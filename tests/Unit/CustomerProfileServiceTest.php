<?php

namespace Tests\Unit;

use App\Models\User;
use App\Services\CustomerProfileService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

class CustomerProfileServiceTest extends TestCase
{
    use RefreshDatabase;

    protected CustomerProfileService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new CustomerProfileService();
    }

    public function test_it_updates_customer_profile_attributes()
    {
        $user = User::create([
            'first_name' => 'Alice',
            'last_name' => 'Smith',
            'email' => 'alice@example.com',
            'phone' => '+1555000111',
            'gender' => 'female',
            'address' => '100 Broadway St',
            'role' => 2,
            'password' => Hash::make('password123'),
        ]);

        $updatedUser = $this->service->updateProfile($user, [
            'first_name' => 'Alice Updated',
            'last_name' => 'Johnson',
            'email' => 'alice.new@example.com',
            'phone' => '+1555999888',
            'gender' => 'female',
            'address' => '200 Park Ave',
        ]);

        $this->assertEquals('Alice Updated', $updatedUser->first_name);
        $this->assertEquals('Johnson', $updatedUser->last_name);
        $this->assertEquals('alice.new@example.com', $updatedUser->email);
        $this->assertEquals('+1555999888', $updatedUser->phone);
        $this->assertEquals('200 Park Ave', $updatedUser->address);
    }

    public function test_it_hashes_new_password_when_provided()
    {
        $user = User::create([
            'first_name' => 'Bob',
            'email' => 'bob@example.com',
            'role' => 2,
            'password' => Hash::make('oldpassword'),
        ]);

        $updatedUser = $this->service->updateProfile($user, [
            'first_name' => 'Bob',
            'email' => 'bob@example.com',
            'password' => 'brandnewpassword123',
        ]);

        $this->assertTrue(Hash::check('brandnewpassword123', $updatedUser->password));
    }

    public function test_it_processes_photo_file_with_intervention_image()
    {
        Storage::fake('public');

        $user = User::create([
            'first_name' => 'Charlie',
            'email' => 'charlie@example.com',
            'role' => 2,
            'password' => Hash::make('password'),
        ]);

        $file = UploadedFile::fake()->image('profile.jpg', 500, 500);

        $updatedUser = $this->service->updateProfile($user, [
            'first_name' => 'Charlie',
            'email' => 'charlie@example.com',
        ], $file);

        $this->assertNotNull($updatedUser->photo);
        $this->assertStringContainsString('public/assets/images/user_photos/user-' . $user->id, $updatedUser->photo);

        $localPath = public_path(Str::after($updatedUser->photo, 'public/'));
        $this->assertFileExists($localPath);

        // Clean up created file
        if (File::exists($localPath)) {
            File::delete($localPath);
        }
    }
}
