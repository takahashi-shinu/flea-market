<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CommentTest extends TestCase
{
    use RefreshDatabase;

    // ログインユーザーは商品にコメントできる
    /** @test */
    public function authenticated_user_can_comment_on_item()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        $response = $this->actingAs($user)
            ->post(route('comments.store', $item), [
                'comment' => 'テストコメントです',
            ]);

        $response->assertStatus(302);

        $this->assertDatabaseHas('comments', [
            'user_id' => $user->id,
            'item_id' => $item->id,
            'comment'    => 'テストコメントです',
        ]);
    }

    // 未ログインユーザーはコメント送信できない
    /** @test */
    public function guest_cannot_post_comment()
    {
        $item = Item::factory()->create();

        $response = $this->post(route('comments.store', $item), [
            'comment' => 'テストコメント',
        ]);

        $response->assertRedirect(); // back()

        $response->assertSessionHas('comment_error');

        $this->assertDatabaseCount('comments', 0);
    }

    // コメント内容が空だと保存されない
    /** @test */
    public function comment_is_not_saved_when_body_is_empty()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        $response = $this->actingAs($user)
            ->post(route('comments.store', $item), [
                'comment' => '',
            ]);

        $response->assertSessionHasErrors('comment');

        $this->assertDatabaseMissing('comments', [
            'item_id' => $item->id,
        ]);
    }
}