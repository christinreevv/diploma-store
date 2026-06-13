<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function login_page_is_available()
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertViewIs('auth.login');

        echo "\nlogin_page_is_available - OK\n";
    }

    #[Test]
    public function registration_page_is_available()
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
        $response->assertViewIs('auth.register');

        echo "\nregistration_page_is_available - OK\n";
    }

    #[Test]
    public function user_can_register()
    {
        $response = $this->post('/register', [
            'name' => 'Иван Иванов',
            'email' => 'ivan@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect('/profile');
        $this->assertDatabaseHas('users', [
            'email' => 'ivan@example.com',
        ]);
        $this->assertAuthenticated();

        echo "\nuser_can_register - OK\n";
    }

    #[Test]
    public function registration_fails_if_email_exists()
    {
        User::factory()->create([
            'email' => 'ivan@example.com',
        ]);

        $response = $this->from('/register')->post('/register', [
            'name' => 'Иван Иванов',
            'email' => 'ivan@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect('/register');
        $response->assertSessionHasErrors('email');

        echo "\nregistration_fails_if_email_exists - OK\n";
    }

    #[Test]
    public function user_can_logout()
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $response = $this->post('/logout');

        $response->assertRedirect('/catalog');
        $this->assertGuest();

        echo "\nuser_can_logout - OK\n";
    }
}
