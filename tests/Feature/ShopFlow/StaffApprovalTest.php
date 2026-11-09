<?php

namespace Tests\Feature\ShopFlow;

use App\Enums\AccountStatus;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StaffApprovalTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_creates_pending_account_and_does_not_log_in(): void
    {
        $response = $this->post('/register', [
            'name' => 'New Staff', 'email' => 'staff@example.com',
            'password' => 'password', 'password_confirmation' => 'password',
        ]);

        $this->assertGuest();
        $response->assertRedirect(route('login'));
        $this->assertSame(AccountStatus::Pending, User::where('email', 'staff@example.com')->first()->status);
    }

    public function test_pending_user_cannot_log_in(): void
    {
        User::factory()->pending()->create(['email' => 'pending@example.com']);

        $this->post('/login', ['email' => 'pending@example.com', 'password' => 'password']);

        $this->assertGuest();
    }

    public function test_admin_can_approve_pending_user_who_can_then_log_in(): void
    {
        $admin = User::factory()->admin()->create();
        $pending = User::factory()->pending()->create(['email' => 'pending@example.com']);

        $this->actingAs($admin)
            ->post(route('staff.approve', $pending))
            ->assertRedirect();

        $pending->refresh();
        $this->assertSame(AccountStatus::Approved, $pending->status);
        $this->assertSame($admin->id, $pending->approved_by);

        auth()->logout();
        $this->post('/login', ['email' => 'pending@example.com', 'password' => 'password']);
        $this->assertAuthenticated();
    }

    public function test_non_admin_cannot_access_staff_page(): void
    {
        $cashier = User::factory()->create();

        $this->actingAs($cashier)->get(route('staff.index'))->assertForbidden();
    }
}
