<?php

use App\Models\Industry;

class IndustryTableSeeder extends Seeder
{

    public function run()
    {
        $industries = [
            'Fashions',
            'Electronics',
            'Groceries',
            'Foods & Beverages',
            'Others'
        ];

        foreach ($industries as $industry) {
            Industry::create([
                'name' => $industry
            ]);
        }
    }

}
