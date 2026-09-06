<?php

use App\Models\AuditLog;
use App\Models\Holiday;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

function auditAdmin(): User
{
    return User::create([
        'first_name' => 'Audit',
        'last_name' => 'Admin',
        'email' => 'audit-admin@example.com',
        'role' => 1,
        'is_active' => true,
        'password' => Hash::make('password'),
    ]);
}

it('logs audit entry on holiday creation with new values and user/IP details', function () {
    $admin = auditAdmin();

    $this->actingAs($admin, 'web')
        ->withServerVariables(['REMOTE_ADDR' => '192.168.1.50']);

    $holiday = Holiday::create([
        'title' => 'New Year Holiday',
        'description' => 'Office closed for New Year',
        'start_date' => '2026-01-01',
        'end_date' => '2026-01-01',
        'is_active' => true,
    ]);

    $auditLog = AuditLog::where('auditable_type', Holiday::class)
        ->where('auditable_id', $holiday->id)
        ->where('action', 'created')
        ->first();

    expect($auditLog)->not->toBeNull()
        ->and($auditLog->user_id)->toBe($admin->id)
        ->and($auditLog->ip_address)->toBe('192.168.1.50')
        ->and($auditLog->old_values)->toBeNull()
        ->and($auditLog->new_values['title'])->toBe('New Year Holiday')
        ->and($auditLog->new_values['description'])->toBe('Office closed for New Year')
        ->and($auditLog->new_values['is_active'])->toBeTrue()
        ->and(array_key_exists('updated_at', $auditLog->new_values ?? []))->toBeFalse();
});

it('logs only the changed field when updating one field and formats dates cleanly without 00:00:00', function () {
    $admin = auditAdmin();
    $holiday = Holiday::create([
        'title' => 'Original Title',
        'description' => 'Original Description',
        'start_date' => '2026-05-01',
        'end_date' => '2026-05-29',
        'is_active' => true,
    ]);

    AuditLog::query()->delete();

    $this->actingAs($admin);
    $holiday->update([
        'end_date' => '2026-05-31',
    ]);

    $auditLog = AuditLog::where('auditable_type', Holiday::class)
        ->where('auditable_id', $holiday->id)
        ->where('action', 'updated')
        ->first();

    expect($auditLog)->not->toBeNull()
        ->and($auditLog->old_values)->toBe(['end_date' => '2026-05-29'])
        ->and($auditLog->new_values)->toBe(['end_date' => '2026-05-31']);
});

it('logs only changed fields when updating multiple fields', function () {
    $admin = auditAdmin();
    $holiday = Holiday::create([
        'title' => 'Labor Day',
        'description' => 'May Day',
        'start_date' => '2026-05-01',
        'end_date' => '2026-05-01',
        'is_active' => true,
    ]);

    AuditLog::query()->delete();

    $this->actingAs($admin);
    $holiday->update([
        'title' => 'International Workers Day',
        'description' => 'Updated description',
    ]);

    $auditLog = AuditLog::where('action', 'updated')->first();

    expect($auditLog)->not->toBeNull()
        ->and($auditLog->old_values)->toBe([
            'title' => 'Labor Day',
            'description' => 'May Day',
        ])
        ->and($auditLog->new_values)->toBe([
            'title' => 'International Workers Day',
            'description' => 'Updated description',
        ])
        ->and(array_key_exists('start_date', $auditLog->old_values))->toBeFalse();
});

it('does not create an audit log when updating with no actual changes', function () {
    $admin = auditAdmin();
    $holiday = Holiday::create([
        'title' => 'No Change Holiday',
        'description' => 'Same description',
        'start_date' => '2026-07-04',
        'end_date' => '2026-07-04',
        'is_active' => true,
    ]);

    AuditLog::query()->delete();

    $this->actingAs($admin);

    // Save with identical values
    $holiday->update([
        'title' => 'No Change Holiday',
        'description' => 'Same description',
        'start_date' => '2026-07-04',
        'end_date' => '2026-07-04',
        'is_active' => true,
    ]);

    $count = AuditLog::where('action', 'updated')->count();
    expect($count)->toBe(0);
});

it('logs previous values on holiday deletion', function () {
    $admin = auditAdmin();
    $holiday = Holiday::create([
        'title' => 'To Be Deleted',
        'description' => 'Temporary holiday',
        'start_date' => '2026-12-25',
        'end_date' => '2026-12-25',
        'is_active' => true,
    ]);

    AuditLog::query()->delete();

    $this->actingAs($admin);
    $holiday->delete();

    $auditLog = AuditLog::where('action', 'deleted')->first();

    expect($auditLog)->not->toBeNull()
        ->and($auditLog->old_values['title'])->toBe('To Be Deleted')
        ->and($auditLog->old_values['description'])->toBe('Temporary holiday')
        ->and($auditLog->new_values)->toBeNull();
});

it('loads audit logs index page and detail json', function () {
    $admin = auditAdmin();

    $holiday = Holiday::create([
        'title' => 'Audit Test Holiday',
        'start_date' => '2026-08-01',
        'end_date' => '2026-08-01',
        'is_active' => true,
    ]);

    $auditLog = AuditLog::where('auditable_id', $holiday->id)->first();

    $this->actingAs($admin)
        ->get(route('audit-logs.index'))
        ->assertOk()
        ->assertViewIs('pages.admin.audit_logs.index')
        ->assertSee('Audit Logs');

    $this->actingAs($admin)
        ->getJson(route('audit-logs.show', $auditLog))
        ->assertOk()
        ->assertJson([
            'id' => $auditLog->id,
            'event' => 'Created',
            'module' => 'Holiday',
            'record_id' => $holiday->id,
        ]);
});

it('resolves module name as Subadmin for role 1 and Customer for role 2 users', function () {
    $admin = auditAdmin();

    $subAdmin = User::create([
        'first_name' => 'Jane',
        'last_name' => 'Sub',
        'email' => 'subadmin@example.com',
        'role' => 1,
        'password' => Hash::make('password'),
    ]);

    $customer = User::create([
        'first_name' => 'John',
        'last_name' => 'Doe',
        'email' => 'customer@example.com',
        'role' => 2,
        'password' => Hash::make('password'),
    ]);

    $subAdminAudit = AuditLog::where('auditable_id', $subAdmin->id)->first();
    $customerAudit = AuditLog::where('auditable_id', $customer->id)->first();

    $this->actingAs($admin)
        ->getJson(route('audit-logs.show', $subAdminAudit))
        ->assertOk()
        ->assertJson([
            'module' => 'Subadmin',
        ]);

    $this->actingAs($admin)
        ->getJson(route('audit-logs.show', $customerAudit))
        ->assertOk()
        ->assertJson([
            'module' => 'Customer',
        ]);
});
