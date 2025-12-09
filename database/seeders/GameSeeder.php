<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GameSeeder extends Seeder
{
    public function run(): void
    {
        // Clear existing data in correct order
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('topup_options')->truncate();
        DB::table('games')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $games = [
            [
                'name' => 'Mobile Legends',
                'description' => 'Popular MOBA game',
                'logo' => 'images/mobile_legends.png',
            ],
            [
                'name' => 'Free Fire',
                'description' => 'Battle Royale',
                'logo' => 'images/free_fire.png',
            ],
            [
                'name' => 'Valorant',
                'description' => 'Tactical FPS',
                'logo' => 'images/valorant.png',
            ],
            [
                'name' => 'Genshin Impact',
                'description' => 'Open World RPG',
                'logo' => 'images/genshin.png',
            ],
            [
                'name' => 'Clash Royale',
                'description' => 'Strategy Card Game',
                'logo' => 'images/clash_royale.png',
            ],
        ];

        foreach ($games as $game) {
            DB::table('games')->insert([
                'name' => $game['name'],
                'description' => $game['description'],
                'logo' => $game['logo'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Mobile Legends - Diamonds
        $mlOptions = [
            ['game_id' => 1, 'coins' => 86, 'amount' => '86 Diamonds', 'price' => 20000],
            ['game_id' => 1, 'coins' => 172, 'amount' => '172 Diamonds', 'price' => 39000],
            ['game_id' => 1, 'coins' => 257, 'amount' => '257 Diamonds', 'price' => 58000],
            ['game_id' => 1, 'coins' => 344, 'amount' => '344 Diamonds', 'price' => 77000],
            ['game_id' => 1, 'coins' => 429, 'amount' => '429 Diamonds', 'price' => 96000],
            ['game_id' => 1, 'coins' => 514, 'amount' => '514 Diamonds', 'price' => 115000],
            ['game_id' => 1, 'coins' => 706, 'amount' => '706 Diamonds', 'price' => 154000],
            ['game_id' => 1, 'coins' => 878, 'amount' => '878 Diamonds', 'price' => 192000],
        ];

        // Free Fire - Diamonds
        $ffOptions = [
            ['game_id' => 2, 'coins' => 50, 'amount' => '50 Diamonds', 'price' => 7000],
            ['game_id' => 2, 'coins' => 70, 'amount' => '70 Diamonds', 'price' => 10000],
            ['game_id' => 2, 'coins' => 140, 'amount' => '140 Diamonds', 'price' => 19000],
            ['game_id' => 2, 'coins' => 210, 'amount' => '210 Diamonds', 'price' => 28000],
            ['game_id' => 2, 'coins' => 355, 'amount' => '355 Diamonds', 'price' => 47000],
            ['game_id' => 2, 'coins' => 720, 'amount' => '720 Diamonds', 'price' => 95000],
            ['game_id' => 2, 'coins' => 1450, 'amount' => '1450 Diamonds', 'price' => 190000],
            ['game_id' => 2, 'coins' => 2180, 'amount' => '2180 Diamonds', 'price' => 285000],
        ];

        // Valorant - VP (Valorant Points)
        $valorantOptions = [
            ['game_id' => 3, 'coins' => 475, 'amount' => '475 VP', 'price' => 50000],
            ['game_id' => 3, 'coins' => 1000, 'amount' => '1000 VP', 'price' => 100000],
            ['game_id' => 3, 'coins' => 2050, 'amount' => '2050 VP', 'price' => 200000],
            ['game_id' => 3, 'coins' => 3650, 'amount' => '3650 VP', 'price' => 350000],
            ['game_id' => 3, 'coins' => 5350, 'amount' => '5350 VP', 'price' => 500000],
            ['game_id' => 3, 'coins' => 11000, 'amount' => '11000 VP', 'price' => 1000000],
        ];

        // Genshin Impact - Genesis Crystals
        $genshinOptions = [
            ['game_id' => 4, 'coins' => 60, 'amount' => '60 Genesis Crystals', 'price' => 16000],
            ['game_id' => 4, 'coins' => 300, 'amount' => '300 Genesis Crystals', 'price' => 79000],
            ['game_id' => 4, 'coins' => 980, 'amount' => '980 Genesis Crystals', 'price' => 249000],
            ['game_id' => 4, 'coins' => 1980, 'amount' => '1980 Genesis Crystals', 'price' => 479000],
            ['game_id' => 4, 'coins' => 3280, 'amount' => '3280 Genesis Crystals', 'price' => 799000],
            ['game_id' => 4, 'coins' => 6480, 'amount' => '6480 Genesis Crystals', 'price' => 1599000],
        ];

        // Clash Royale - Gems
        $clashOptions = [
            ['game_id' => 5, 'coins' => 80, 'amount' => '80 Gems', 'price' => 15000],
            ['game_id' => 5, 'coins' => 500, 'amount' => '500 Gems', 'price' => 79000],
            ['game_id' => 5, 'coins' => 1200, 'amount' => '1200 Gems', 'price' => 159000],
            ['game_id' => 5, 'coins' => 2500, 'amount' => '2500 Gems', 'price' => 319000],
            ['game_id' => 5, 'coins' => 6500, 'amount' => '6500 Gems', 'price' => 799000],
            ['game_id' => 5, 'coins' => 14000, 'amount' => '14000 Gems', 'price' => 1599000],
        ];

        $allOptions = array_merge(
            $mlOptions, 
            $ffOptions, 
            $valorantOptions, 
            $genshinOptions, 
            $clashOptions
        );
        
        foreach ($allOptions as $option) {
            DB::table('topup_options')->insert([
                'game_id' => $option['game_id'],
                'coins' => $option['coins'],
                'amount' => $option['amount'],
                'price' => $option['price'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}