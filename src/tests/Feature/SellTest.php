<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Category;
use App\Models\Item;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SellTest extends TestCase
{
    use RefreshDatabase;

    // 出品画面が表示される
    /** @test */
    public function sell_create_page_is_displayed()
    {
        $user = User::factory()->verified()->completedProfile()->create();

        $this->actingAs($user)
            ->get(route('sell.create'))
            ->assertStatus(200)
            ->assertViewIs('items.sell')
            ->assertViewHas('categories');
    }

    // 商品を出品できる
    /** @test */
    public function user_can_sell_item()
    {
        Storage::fake('public');

        $user = User::factory()->verified()->completedProfile()->create();
        $categories = Category::factory()->count(2)->create();

        $response = $this->actingAs($user)->post(route('sell.store'), [
            'name' => 'テスト商品',
            'price' => 1000,
            'condition' => '新品',
            'description' => 'テスト説明',
            'image' => UploadedFile::fake()->create(
                'test.png',
                100,        // KB
                'image/png'
            ),
            'category_ids' => $categories->pluck('id')->toArray(),
        ]);

        // リダイレクト確認
        $response->assertRedirect(route('items.index'));

        // items に保存されている
        $this->assertDatabaseHas('items', [
            'name' => 'テスト商品',
            'price' => 1000,
            'user_id' => $user->id,
            'status' => Item::STATUS_SELLING,
        ]);

        $item = Item::first();

        // pivot テーブル確認
        foreach ($categories as $category) {
            $this->assertDatabaseHas('category_item', [
                'item_id' => $item->id,
                'category_id' => $category->id,
            ]);
        }

        // 画像保存確認
        Storage::disk('public')->assertExists($item->image);
    }

    // 未ログインは出品画面にアクセスできない
    /** @test */
    public function guest_cannot_access_sell_page()
    {
        $this->get(route('sell.create'))
            ->assertRedirect(route('login'));
    }

    // 出品商品のバリデーションテスト
    /** @test */
    public function sell_validation_fails_without_required_fields()
    {
        Storage::fake('public');

        $user = User::factory()->verified()->completedProfile()->create();
        $category = Category::factory()->create();

        $response = $this->actingAs($user)->post(route('sell.store'), [
            // name をわざと送らない
            'description' => '説明文',
            'price' => 3000,
            'condition' => 'good',
            'category_ids' => [$category->id],
            'image' => UploadedFile::fake()->create(
                'test.jpg',
                100,
                'image/jpeg'
            ),
        ]);

        // バリデーション失敗 → リダイレクト
        $response->assertStatus(302);

        // セッションにエラーが入る
        $response->assertSessionHasErrors([
            'name',
        ]);

        // DB に保存されていない
        $this->assertDatabaseCount('items', 0);
    }

}