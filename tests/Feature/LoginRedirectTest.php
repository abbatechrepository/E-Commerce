<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginRedirectTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_is_redirected_to_admin_dashboard_after_login(): void
    {
        $admin = User::factory()->create([
            'email' => 'admin@example.test',
            'password' => 'password',
            'role' => UserRole::ADMIN,
        ]);

        $this->post('/login', [
            'email' => $admin->email,
            'password' => 'password',
        ])->assertRedirect(route('admin.dashboard'));
    }

    public function test_customer_is_redirected_to_customer_dashboard_after_login(): void
    {
        $user = User::factory()->create([
            'email' => 'customer@example.test',
            'password' => 'password',
            'role' => UserRole::CUSTOMER,
        ]);

        Customer::query()->create([
            'user_id' => $user->id,
        ]);

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ])->assertRedirect(route('customer.dashboard'));
    }
}
