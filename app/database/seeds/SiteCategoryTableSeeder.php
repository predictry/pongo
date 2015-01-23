<?php

use App\Models\SiteCategory;

class SiteCategoryTableSeeder extends Seeder
{

    public function run()
    {
        $site_categories = [
            'Standalone eCommerce',
            'Marketplace',
            'Daily Deals',
            'Aggregator',
            'Others'
        ];

        foreach ($site_categories as $site_category) {
            SiteCategory::create([
                'name' => $site_category
            ]);
        }
    }

}
