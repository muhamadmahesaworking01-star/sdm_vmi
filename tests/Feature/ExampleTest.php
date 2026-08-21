<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic test example.
     */
    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get('/');

        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_role_is_redirected_to_its_own_home_page(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_DIREKSI]);

        $response = $this->actingAs($user)->get('/');

        $response->assertRedirect(route('direksi.dashboard'));
    }
}
