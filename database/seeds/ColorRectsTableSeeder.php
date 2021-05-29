<?php

use Illuminate\Database\Seeder;

class ColorRectsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('color_rects')->insert([
            'pos_top'  => 100,
            'pos_left' => 100,
            'color'    => 0xFFaaaa,
        ]);
        DB::table('color_rects')->insert([
            'pos_top'  => 300,
            'pos_left' => 100,
            'color'    => 0xaaFFaa,
        ]);
        DB::table('color_rects')->insert([
            'pos_top'  => 300,
            'pos_left' => 500,
            'color'    => 0xaaaaFF,
        ]);
    }
}
