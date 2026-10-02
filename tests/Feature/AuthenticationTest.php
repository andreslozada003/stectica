<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_login(): void
    {
        $this->get('/dashboard')->assertRedirect(route('login'));
    }

    public function test_user_can_log_in_and_log_out(): void
    {
        $user = User::factory()->create(['password' => 'secret-password']);

        $this->post(route('login.store'), [
            'email' => $user->email,
            'password' => 'secret-password',
        ])->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($user);

        $this->post(route('logout'))->assertRedirect(route('login'));
        $this->assertGuest();
    }
}
