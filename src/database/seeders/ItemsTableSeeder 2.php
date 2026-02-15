<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Item;
use App\Models\Category;
use App\Models\User;

class ItemsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user1 = User::find(1);
        $user2 = User::find(2);
        if (!$user1 || !$user2) {
        $this->command->error("ID 1 または 2 のユーザーが見つかりません。シーダーを中断します。");
        return;
    }

        $fashion = Category::where('name', 'ファッション')->first()->id;
        $consumer_electronic = Category::where('name', '家電')->first()->id;
        $interior = Category::where('name', 'インテリア')->first()->id;
        $ladies = Category::where('name', 'レディース')->first()->id;
        $mens = Category::where('name', 'メンズ')->first()->id;
        $cosmetic = Category::where('name', 'コスメ')->first()->id;
        $book = Category::where('name', '本')->first()->id;
        $game = Category::where('name', 'ゲーム')->first()->id;
        $sport = Category::where('name', 'スポーツ')->first()->id;
        $kitchen = Category::where('name', 'キッチン')->first()->id;
        $handmade = Category::where('name', 'ハンドメイド')->first()->id;
        $accessory = Category::where('name', 'アクセサリー')->first()->id;
        $toy = Category::where('name', 'おもちゃ')->first()->id;
        $baby_kids = Category::where('name', 'ベビー・キッズ')->first()->id;
        $clothes = Category::where('name', '洋服')->first()->id;
        $daily_necessity = Category::where('name', '生活用品')->first()->id;

        $ArmaniMensClock = Item::create([
            'user_id' => $user1->id,
            'name' => '腕時計',
            'image' => 'images/ArmaniMensClock.jpg',
            'brand_name' => 'Rolax',
            'condition' => '良好',
            'price' => 15000,
            'description' => 'スタイリッシュなデザインのメンズ腕時計',
        ]);
        $ArmaniMensClock->categories()->sync([$fashion, $mens]);

        $HDDHardDisk = Item::create([
            'user_id' => $user1->id,
            'name' => 'HDD',
            'image' => 'images/HDDHardDisk.jpg',
            'brand_name' => '西芝',
            'condition' => '目立った傷や汚れなし',
            'price' => 5000,
            'description' => '高速で信頼性の高いハードディスク',
        ]);
        $HDDHardDisk->categories()->sync([$consumer_electronic]);

        $Onion = Item::create([
            'user_id' => $user1->id,
            'name' => '玉ねぎ３束',
            'image' => 'images/iLoveIMGd.jpg',
            'brand_name' => 'なし',
            'condition' => 'やや傷や汚れあり',
            'price' => 300,
            'description' => '新鮮な玉ねぎ３束のセット',
        ]);
        $Onion->categories()->sync([$daily_necessity]);

        $Shoes = Item::create([
            'user_id' => $user1->id,
            'name' => '革靴',
            'image' => 'images/LeatherShoes.jpg',
            'brand_name' => '',
            'condition' => '状態が悪い',
            'price' => 4000,
            'description' => 'クラシックなデザインの革靴',
        ]);
        $Shoes->categories()->sync([$fashion, $mens]);

        $NotePC = Item::create([
            'user_id' => $user1->id,
            'name' => 'ノートPC',
            'image' => 'images/LivingRoomLaptop.jpg',
            'brand_name' => '',
            'condition' => '良好',
            'price' => 45000,
            'description' => '高性能なノートパソコン',
        ]);
        $NotePC->categories()->sync([$consumer_electronic]);

        $MusicMic = Item::create([
            'user_id' => $user2->id,
            'name' => 'マイク',
            'image' => 'images/MusicMic4632231.jpg',
            'brand_name' => 'なし',
            'condition' => '目立った傷や汚れなし',
            'price' => 8000,
            'description' => '高音質のレコーディング用マイク',
        ]);
        $MusicMic->categories()->sync([$consumer_electronic]);

        $fashionPocket = Item::create([
            'user_id' => $user2->id,
            'name' => 'ショルダーバッグ',
            'image' => 'images/PursefashionPocket.jpg',
            'brand_name' => '',
            'condition' => 'やや傷や汚れあり',
            'price' => 3500,
            'description' => 'おしゃれなショルダーバッグ',
        ]);
        $fashionPocket->categories()->sync([$fashion, $ladies]);

        $Tumbler = Item::create([
            'user_id' => $user2->id,
            'name' => 'タンブラー',
            'image' => 'images/TumblerSouvenir.jpg',
            'brand_name' => 'なし',
            'condition' => '状態が悪い',
            'price' => 500,
            'description' => '使いやすいタンブラー',
        ]);
        $Tumbler->categories()->sync([$daily_necessity, $kitchen]);

        $CoffeeGrinder = Item::create([
            'user_id' => $user2->id,
            'name' => 'コーヒーミル',
            'image' => 'images/WaitressWithCoffeeGrinder.jpg',
            'brand_name' => 'Starbacks',
            'condition' => '良好',
            'price' => 4000,
            'description' => '手動のコーヒーミル',
        ]);
        $CoffeeGrinder->categories()->sync([$daily_necessity, $kitchen]);

        $MakeupSet = Item::create([
            'user_id' => $user2->id,
            'name' => 'メイクセット',
            'image' => 'images/外出メイクアップセット.jpg',
            'brand_name' => '',
            'condition' => '目立った傷や汚れなし',
            'price' => 2500,
            'description' => '便利なメイクアップセット',
        ]);
        $MakeupSet->categories()->sync([$cosmetic]);
    }
}
