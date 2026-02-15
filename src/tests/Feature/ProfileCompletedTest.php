<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProfileCompletedTest extends TestCase
{
    use RefreshDatabase;

    // プロフィール未完了の場合はプロフィール編集画面へリダイレクトされる
    /** @test */
    public function user_with_incomplete_profile_is_redirected_to_profile_edit_page()
    {
        $user = User::factory()->create([
            'postal_code' => null,
            'address' => null,
            'email_verified_at' => now(),
        ]);

        $this->actingAs($user)
            ->get('/mypage')
            ->assertRedirect(route('profile.edit'));
    }

    // プロフィール完了の場合は保護されたページにアクセスできる
    /** @test */
    public function user_with_completed_profile_can_access_protected_pages()
    {
        $user = User::factory()->create([
            'postal_code' => '100-0001',
            'address' => '東京都千代田区',
            'email_verified_at' => now(),
        ]);

        $this->actingAs($user)
            ->get('/mypage')
            ->assertStatus(200);
    }

    // 未ログインの場合はログイン画面へリダイレクトされる
    /** @test */
    public function guest_user_is_redirected_to_login_page()
    {
        $this->get('/mypage')
            ->assertRedirect('/login');
    }
}
