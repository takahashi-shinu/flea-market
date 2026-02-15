<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\User;
use App\Models\Favorite;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FavoriteTest extends TestCase
{
    use RefreshDatabase;

    // ログインユーザーは商品をお気に入りにできる
    /** @test */
    public function authenticated_user_can_add_an_item_to_favorites()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post(route('favorites.toggle', $item));

        $response->assertRedirect();

        $this->assertDatabaseHas('favorites', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);
    }

    // 既にお気に入りの場合は解除される
    /** @test */
    public function if_already_favorited_it_will_be_removed()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        Favorite::create([
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        $response = $this
            ->actingAs($user)
            ->post(route('favorites.toggle', $item));

        $response->assertRedirect();

        $this->assertDatabaseMissing('favorites', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);
    }

    // 未ログインユーザーはお気に入りできない
    /** @test */
    public function guest_user_cannot_add_an_item_to_favorites()
    {
        $item = Item::factory()->create();

        $response = $this
            ->post(route('favorites.toggle', $item));

        $response->assertRedirect(route('login'));
    }
}