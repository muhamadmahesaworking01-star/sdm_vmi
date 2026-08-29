<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileReligionTest extends TestCase
{
    use RefreshDatabase;

    public function test_direksi_can_save_religion_in_biodata(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_DIREKSI]);

        $this->actingAs($user)->put(route('direksi.profile.update'), [
            'nama' => 'Direksi Test',
            'email' => 'direksi-test@example.com',
            'telepon' => '081234567890',
            'agama' => 'Islam',
            'alamat' => 'Alamat test',
        ])->assertRedirect(route('direksi.profile.edit'));

        $this->assertSame('Islam', $user->fresh()->biodata['agama']);
    }

    public function test_super_admin_can_save_religion_in_biodata(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);

        $this->actingAs($user)->put(route('admin.profile.update'), [
            'nama' => 'Admin Test',
            'email' => 'admin-test@example.com',
            'agama' => 'Kristen',
        ])->assertRedirect(route('admin.profile.edit'));

        $this->assertSame('Kristen', $user->fresh()->biodata['agama']);
    }
}
