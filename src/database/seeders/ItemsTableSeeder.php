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
        $user1 = User::where('email', 'sample1@example.com')->first();
        $user2 = User::where('email', 'sample2@example.com')->first();
        $user3 = User::where('email', 'sample3@example.com')->first();
        $user4 = User::where('email', 'sample4@example.com')->first();

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
            'status' => Item::STATUS_SELLING,
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
            'status' => Item::STATUS_SELLING,
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
            'status' => Item::STATUS_SELLING,
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
            'status' => Item::STATUS_SELLING,
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
            'status' => Item::STATUS_SELLING,
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
            'status' => Item::STATUS_SELLING,
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
            'status' => Item::STATUS_SELLING,
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
            'status' => Item::STATUS_SELLING,
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
            'status' => Item::STATUS_SELLING,
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
            'status' => Item::STATUS_SELLING,
        ]);
        $MakeupSet->categories()->sync([$cosmetic]);

        $TV = Item::create([
            'user_id' => $user3->id,
            'name' => 'テレビ',
            'image' => 'items/4kTV.png',
            'brand_name' => 'Hise',
            'condition' => '目立った傷や汚れなし',
            'price' => 50000,
            'description' => '55inch 大画面4kテレビ',
            'status' => Item::STATUS_SELLING,
        ]);
        $TV->categories()->sync([$consumer_electronic]);

        $SofaBed = Item::create([
            'user_id' => $user3->id,
            'name' => 'ソファーベッド',
            'image' => 'items/sofabed.png',
            'brand_name' => 'CAG',
            'condition' => '目立った傷や汚れなし',
            'price' => 60000,
            'description' => 'レトロモダンソファベッド くつろぎと実用性を一体化した心地よい設計',
            'status' => Item::STATUS_SELLING,
        ]);
        $SofaBed->categories()->sync([$interior]);

        $SportsWare = Item::create([
            'user_id' => $user3->id,
            'name' => 'メンズスポーツウェア',
            'image' => 'items/sportsware.png',
            'brand_name' => 'MJ',
            'condition' => '目立った傷や汚れなし',
            'price' => 50000,
            'description' => 'メンズスポーツウェア上下セット サイズ：L',
            'status' => Item::STATUS_SELLING,
        ]);
        $SportsWare->categories()->sync([$fashion,$mens,$sport]);

        $BabyClothes = Item::create([
            'user_id' => $user4->id,
            'name' => 'ベビー洋服',
            'image' => 'items/baby_clothes.png',
            'brand_name' => 'beb',
            'condition' => '良好',
            'price' => 1000,
            'description' => 'ベビー洋服(サイズ100~120cm)',
            'status' => Item::STATUS_SELLING,
        ]);
        $BabyClothes->categories()->sync([$fashion,$baby_kids,$clothes]);

        $BabyToy = Item::create([
            'user_id' => $user4->id,
            'name' => '子供おもちゃ',
            'image' => 'items/baby_toy.png',
            'brand_name' => '',
            'condition' => '目立った傷や汚れなし',
            'price' => 500,
            'description' => '0歳向け知育玩具',
            'status' => Item::STATUS_SELLING,
        ]);
        $BabyToy->categories()->sync([$baby_kids,$toy]);

        $LadyCoat = Item::create([
            'user_id' => $user4->id,
            'name' => 'レディースコート',
            'image' => 'items/lady_coat.png',
            'brand_name' => '',
            'condition' => '目立った傷や汚れなし',
            'price' => 5000,
            'description' => 'レディースのチェスターコート',
            'status' => Item::STATUS_SELLING,
        ]);
        $LadyCoat->categories()->sync([$fashion,$ladies,$clothes]);
    }
}
