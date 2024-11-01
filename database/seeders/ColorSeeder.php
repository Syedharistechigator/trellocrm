<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ColorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $colors = [
            ['subtle green', '#baf3db', '1_1'],
            ['subtle yellow', '#f8e6a0', '2_1'],
            ['subtle orange', '#fedec8', '3_1'],
            ['subtle red', '#ffd5d2', '4_1'],
            ['subtle purple', '#dfd8fd', '5_1'],

            ['green', '#4bce97', '1'],
            ['yellow', '#f5cd47', '2'],
            ['orange', '#fea362', '3'],
            ['red', '#f87168', '4'],
            ['purple', '#9f8fef', '5'],

            ['bold green', '#1f845a', '1_2'],
            ['bold yellow', '#946f00', '2_2'],
            ['bold orange', '#c25100', '3_2'],
            ['bold red', '#c9372c', '4_2'],
            ['bold purple', '#6e5dc6', '5_2'],

            ['subtle blue', '#cce0ff', '6_1'],
            ['subtle sky', '#c6edfb', '7_1'],
            ['subtle lime', '#d3f1a7', '8_1'],
            ['subtle pink', '#fdd0ec', '9_1'],
            ['subtle black', '#dcdfe4', '10_1'],
            ['none', '#091e420f', '10_3'],

            ['blue', '#579dff', '6'],
            ['sky', '#6cc3e0', '7'],
            ['lime', '#94c748', '8'],
            ['pink', '#e774bb', '9'],
            ['black', '#8590a2', '10'],

            ['bold blue', '#0c66e4', '6_2'],
            ['bold sky', '#227d9b', '7_2'],
            ['bold lime', '#5b7f24', '8_2'],
            ['bold pink', '#ae4787', '9_2'],
            ['bold black', '#626f86', '10_2'],
        ];


        foreach ($colors as $color) {
            DB::table('colors')->insert([
                'color_name' => $color[0],
                'color_value' => $color[1],
                'color_position' => $color[2],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
