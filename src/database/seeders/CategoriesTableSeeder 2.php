<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Category;

class CategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $names = ["ファッション","家電","インテリア","レディース","メンズ","コスメ","本","ゲーム","スポーツ","キッチン","ハンドメイド","アクセサリー","おもちゃ","ベビー・キッズ","洋服","生活用品",];
        foreach ($names as $name) {
            Category::create([
                'name' => $name,
            ]);
        }
    }
}
