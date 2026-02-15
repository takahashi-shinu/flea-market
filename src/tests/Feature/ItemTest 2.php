<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Favorite;
use App\Models\Comment;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ItemTest extends TestCase
{
    use RefreshDatabase;

    // 未ログインで商品一覧が表示される
    /** @test */
    public function index_displays_items_for_guest()
    {
        $item = Item::factory()->create();

        $this->get(route('items.index'))
            ->assertStatus(200)
            ->assertViewIs('items.index')
            ->assertViewHas('items', function ($items) use ($item) {
                return $items->contains($item);
            });
    }

    // ログイン時は自分の商品が表示されない
    /** @test */
    public function index_excludes_own_items_when_logged_in()
    {
        $user = User::factory()->create();
        $ownItem = Item::factory()->create(['user_id' => $user->id]);
        $otherItem = Item::factory()->create();

        $this->actingAs($user)
            ->get(route('items.index'))
            ->assertViewHas('items', function ($items) use ($ownItem, $otherItem) {
                return ! $items->contains($ownItem)
                    && $items->contains($otherItem);
            });
    }

    // keyword 検索が機能する
    /** @test */
    public function index_filters_by_keyword()
    {
        $apple = Item::factory()->create(['name' => 'りんご']);
        $banana = Item::factory()->create(['name' => 'バナナ']);

        $this->get(route('items.index', ['keyword' => 'りんご']))
            ->assertViewHas('items', function ($items) use ($apple, $banana) {
                return $items->contains($apple)
                    && ! $items->contains($banana);
            });
    }

    // mylist タブでお気に入り商品が表示される
    /** @test */
    public function mylist_tab_displays_favorite_items()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        Favorite::create([
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        $this->actingAs($user)
            ->get(route('items.index', ['tab' => 'mylist']))
            ->assertViewHas('favorites', function ($favorites) use ($item) {
                return $favorites->contains($item);
            });
    }

    // 商品詳細が表示される
    /** @test */
    public function show_displays_item_detail()
    {
        $item = Item::factory()->create();

        $this->get(route('items.show', $item))
            ->assertStatus(200)
            ->assertViewIs('items.item')
            ->assertViewHas('item', $item);
    }

    // isLiked(いいね) が true になる
    /** @test */
    public function show_sets_is_liked_true_when_favorited()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        Favorite::create([
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        $this->actingAs($user)
            ->get(route('items.show', $item))
            ->assertViewHas('isLiked', true);
    }
}