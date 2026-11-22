<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertSee('Masuk ke Akun Anda');
        $response->assertSee('admin@rentcar.com');
    }

    public function test_register_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
        $response->assertSee('Buat Akun Customer Baru');
        $response->assertSee('Nama Lengkap');
        $response->assertSee('No. Telepon / WhatsApp');
    }

    public function test_admin_can_authenticate_and_redirect_to_admin_dashboard(): void
    {
        $user = User::factory()->create([
            'email' => 'admin@rentcar.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
        ]);

        $response = $this->post('/login', [
            'email' => 'admin@rentcar.com',
            'password' => 'admin123',
        ]);

        $this->assertAuthenticatedAs($user);
        $response->assertRedirect(route('admin.dashboard'));
    }

    public function test_customer_can_authenticate_and_redirect_to_customer_dashboard(): void
    {
        $user = User::factory()->create([
            'email' => 'customer@test.com',
            'password' => Hash::make('password123'),
            'role' => 'customer',
        ]);

        $response = $this->post('/login', [
            'email' => 'customer@test.com',
            'password' => 'password123',
        ]);

        $this->assertAuthenticatedAs($user);
        $response->assertRedirect(route('customer.dashboard'));
    }

    public function test_users_can_not_authenticate_with_invalid_password(): void
    {
        User::factory()->create([
            'email' => 'admin@rentcar.com',
            'password' => Hash::make('admin123'),
        ]);

        $response = $this->post('/login', [
            'email' => 'admin@rentcar.com',
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors('email');
    }

    public function test_new_users_can_register_as_customer(): void
    {
        $response = $this->post('/register', [
            'name' => 'Budi Baru',
            'email' => 'budi.baru@example.com',
            'phone' => '081299998888',
            'address' => 'Jl. Pengujian No. 1, Sleman',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('customer.dashboard'));

        $this->assertDatabaseHas('users', [
            'email' => 'budi.baru@example.com',
            'role' => 'customer',
        ]);
    }

    public function test_users_can_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect(route('homepage'));
    }

    public function test_admin_dashboard_requires_admin_role(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);

        $this->actingAs($customer)->get('/admin/dashboard')->assertStatus(403);
    }

    public function test_customer_dashboard_requires_customer_role(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->get('/customer/dashboard')->assertStatus(403);
    }

    public function test_admin_can_access_admin_dashboard(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->get('/admin/dashboard')->assertStatus(200);
    }

    public function test_customer_can_access_customer_dashboard(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);

        $this->actingAs($customer)->get('/customer/dashboard')->assertStatus(200);
    }

    public function test_dashboard_redirects_based_on_role(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $customer = User::factory()->create(['role' => 'customer']);

        $this->actingAs($admin)->get('/dashboard')->assertRedirect(route('admin.dashboard'));
        $this->actingAs($customer)->get('/dashboard')->assertRedirect(route('customer.dashboard'));
    }
}
