<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminUserManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_another_admin_user(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->post(route('admin.users.store'), [
            'name' => 'Novo Admin',
            'email' => 'novo.admin@ecommerce.test',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('users', [
            'email' => 'novo.admin@ecommerce.test',
            'role' => UserRole::ADMIN->value,
            'is_active' => true,
        ]);
    }

    public function test_admin_can_promote_existing_user_to_admin(): void
    {
        $admin = User::factory()->admin()->create();
        $customer = User::factory()->create([
            'email' => 'cliente.promocao@ecommerce.test',
            'role' => UserRole::CUSTOMER,
        ]);

        $response = $this->actingAs($admin)->post(route('admin.users.promote'), [
            'email' => $customer->email,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('users', [
            'id' => $customer->id,
            'role' => UserRole::ADMIN->value,
        ]);
    }

    public function test_manager_cannot_create_or_promote_admin_users(): void
    {
        $manager = User::factory()->create([
            'role' => UserRole::MANAGER,
            'email' => 'manager@ecommerce.test',
        ]);

        $target = User::factory()->create([
            'email' => 'target.user@ecommerce.test',
            'role' => UserRole::CUSTOMER,
        ]);

        $this->actingAs($manager)
            ->post(route('admin.users.promote'), ['email' => $target->email])
            ->assertForbidden();

        $this->actingAs($manager)
            ->post(route('admin.users.store'), [
                'name' => 'Outro Admin',
                'email' => 'bloqueado@ecommerce.test',
                'password' => 'password123',
                'password_confirmation' => 'password123',
            ])
            ->assertForbidden();
    }

    public function test_admin_can_access_admin_users_page_and_filter_results(): void
    {
        $admin = User::factory()->admin()->create();
        User::factory()->create([
            'name' => 'Cliente Um',
            'email' => 'cliente.um@ecommerce.test',
            'role' => UserRole::CUSTOMER,
        ]);
        User::factory()->create([
            'name' => 'Manager Um',
            'email' => 'manager.um@ecommerce.test',
            'role' => UserRole::MANAGER,
        ]);

        $response = $this->actingAs($admin)->get(route('admin.users.index', [
            'q' => 'cliente',
            'role' => 'customer',
        ]));

        $response->assertOk();
        $response->assertSee('Cliente Um');
        $response->assertDontSee('Manager Um');
    }

    public function test_admin_can_activate_and_deactivate_user(): void
    {
        $admin = User::factory()->admin()->create();
        $target = User::factory()->create([
            'email' => 'toggle.user@ecommerce.test',
            'is_active' => true,
        ]);

        $this->actingAs($admin)
            ->patch(route('admin.users.status', $target), ['is_active' => 0])
            ->assertRedirect();

        $this->assertDatabaseHas('users', [
            'id' => $target->id,
            'is_active' => false,
        ]);

        $this->actingAs($admin)
            ->patch(route('admin.users.status', $target), ['is_active' => 1])
            ->assertRedirect();

        $this->assertDatabaseHas('users', [
            'id' => $target->id,
            'is_active' => true,
        ]);
    }

    public function test_admin_cannot_deactivate_own_account(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)
            ->from(route('admin.users.index'))
            ->patch(route('admin.users.status', $admin), ['is_active' => 0]);

        $response->assertRedirect(route('admin.users.index'));
        $response->assertSessionHasErrors('is_active');

        $this->assertDatabaseHas('users', [
            'id' => $admin->id,
            'is_active' => true,
        ]);
    }

    public function test_manager_cannot_access_admin_users_page_or_status_toggle(): void
    {
        $manager = User::factory()->create([
            'role' => UserRole::MANAGER,
            'email' => 'manager.access@ecommerce.test',
        ]);

        $target = User::factory()->create([
            'email' => 'target.access@ecommerce.test',
            'role' => UserRole::CUSTOMER,
        ]);

        $this->actingAs($manager)
            ->get(route('admin.users.index'))
            ->assertForbidden();

        $this->actingAs($manager)
            ->patch(route('admin.users.status', $target), ['is_active' => 0])
            ->assertForbidden();
    }
}
