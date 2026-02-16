<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UsersTableSeeder extends Seeder
{
    public function run()
    {
        $now = Carbon::now();

        $users = [
            [
                'name' => 'User1',
                'email' => 'sample1@example.com',
                'password' => Hash::make('abc12345'),
                'email_verified_at' => $now, // 認証済み
                'postal_code' => '100-0001',
                'address' => '東京都千代田区',
                'building' => 'テストビル',
                'image' => 'users/user1.png',
            ],
            [
                'name' => 'User2',
                'email' => 'sample2@example.com',
                'password' => Hash::make('abc12345'),
                'email_verified_at' => $now,
                'postal_code' => '150-0001',
                'address' => '東京都渋谷区',
                'building' => 'サンプルマンション',
                'image' => 'users/user2.png',
            ],
            [
                'name' => 'User3',
                'email' => 'sample3@example.com',
                'password' => Hash::make('abc12345'),
                'email_verified_at' => $now,
                'postal_code' => '530-0001',
                'address' => '大阪府大阪市北区',
                'building' => 'デモビル',
                'image' => 'users/user3.png',
            ],
            [
                'name' => 'User4',
                'email' => 'sample4@example.com',
                'password' => Hash::make('abc12345'),
                'email_verified_at' => $now,
                'postal_code' => '060-0001',
                'address' => '北海道札幌市中央区',
                'building' => 'テストタワー',
                'image' => null,
            ],
            [
                'name' => 'User5',
                'email' => 'sample5@example.com',
                'password' => Hash::make('abc12345'),
                'email_verified_at' => $now,
                'postal_code' => '270-1444',
                'address' => '千葉県柏市',
                'building' => null,
                'image' => null,
            ],
        ];

        // 再実行しても増殖しないように email をキーに upsert
        User::upsert(
            $users,
            ['email'], // unique key
            ['name', 'password', 'email_verified_at', 'postal_code', 'address', 'building', 'image']
        );

        $this->command?->info('Sample users seeded: sample1〜sample5@example.com');
    }
}

