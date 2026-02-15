<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    // ログインユーザーはプロフィール編集画面を表示できる
    /** @test */
    public function authenticated_user_can_view_the_profile_edit_page()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get(route('profile.edit'));
        $response->assertStatus(200);
        $response->assertViewIs('users.profile');
    }

    // 未ログインユーザーはプロフィール編集画面を表示できない
    /** @test */
    public function guest_user_cannot_view_the_profile_edit_page()
    {
        $response = $this->get(route('profile.edit'));
        $response->assertRedirect(route('login'));
    }

    // プロフィール情報を更新できる
    /** @test */
    public function user_can_update_profile_information()
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post(route('profile.update'), [
                'name' => '更新後ユーザー',
                'postal_code' => '150-0001',
                'address' => '東京都渋谷区',
                'building' => '更新ビル',
            ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => '更新後ユーザー',
            'postal_code' => '150-0001',
            'address' => '東京都渋谷区',
            'building' => '更新ビル',
        ]);
    }

    // 画像付きでプロフィールを更新できる
    /** @test */
    public function user_can_update_profile_with_image_upload()
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $response = $this
            ->actingAs($user)
            ->post(route('profile.update'), [
                'name' => '画像ユーザー',
                'image' => UploadedFile::fake()->create(
                    'avatar.png',
                    100,
                    'image/png'
                ),
            ]);

        $response->assertRedirect();
        $user->refresh();
        Storage::disk('public')->assertExists($user->image);
    }

    // バリデーションエラー時はプロフィールが更新されない
    /** @test */
    public function profile_is_not_updated_when_validation_fails()
    {
        $user = User::factory()->create([
            'name' => '元の名前',
        ]);

        $response = $this
            ->actingAs($user)
            ->from(route('profile.edit'))
            ->post(route('profile.update'), [
                'name' => '',
            ]);

        $response->assertRedirect(route('profile.edit'));
        $response->assertSessionHasErrors(['name']);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => '元の名前',
        ]);
    }
}
