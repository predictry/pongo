<?php

use Faker\Factory as Faker;

class DatabaseSeeder extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Eloquent::unguard();

        $this->call('PlanSeeder');
        $this->call('AccountSeeder');
        $this->call('SitesTableSeeder');
        $this->call('ActionsSeeder');
        $this->call('CombinationTableSeeder');
        $this->call('ItemTableSeeder');
//		$this->call('ActionDataSeeder');
    }

}

class PlanSeeder extends Seeder
{

    public function run()
    {
        DB::table('plans')->delete();

        App\Models\Plan::create(
                array(
                    'id'          => '1',
                    'name'        => 'Plan A',
                    'description' => 'Plan A',
                    'currency'    => 'USD',
                    'price'       => '9',
                    'limit_value' => '1',
                )
        );

        App\Models\Plan::create(
                array(
                    'id'          => '2',
                    'name'        => 'Plan B',
                    'description' => 'Plan B',
                    'currency'    => 'USD',
                    'price'       => '99',
                    'limit_value' => '12',
                )
        );

        App\Models\Plan::create(
                array(
                    'id'          => '3',
                    'name'        => 'Plan C',
                    'description' => 'Plan C',
                    'currency'    => 'USD',
                    'price'       => '999',
                    'limit_value' => '12',
                )
        );
    }

}

class ActionsSeeder extends Seeder
{

    function run()
    {
        DB::table('actions')->delete();
        foreach (range(1, 10) as $index) {
            \App\Models\Action::create(
                    array(
                        'name'    => 'view',
                        'site_id' => $index
                    )
            );
            \App\Models\Action::create(
                    array(
                        'name'    => 'rate',
                        'site_id' => $index
                    )
            );
            \App\Models\Action::create(
                    array(
                        'name'    => 'add_to_cart',
                        'site_id' => $index
                    )
            );
            \App\Models\Action::create(
                    array(
                        'name'    => 'buy',
                        'site_id' => $index
                    )
            );
        }
    }

}

class AccountSeeder extends Seeder
{

    public function run()
    {
        DB::table('accounts')->delete();

        App\Models\Account::create(array(
            'id'                => 1,
            'name'              => 'Rifki Yandhi',
            'email'             => 'rifkiyandhi@gmail.com',
            'password'          => Hash::make('password'),
            'confirmed'         => 1,
            'confirmation_code' => md5(microtime() . Config::get('app.key')),
            'plan_id'           => 1
        ));

        $faker = Faker::create();
        foreach (range(2, 10) as $index) {
            App\Models\Account::create(array(
                'id'                => $index,
                'name'              => $faker->name,
                'email'             => $faker->email,
                'password'          => Hash::make('password'),
                'confirmed'         => 1,
                'confirmation_code' => md5(microtime() . Config::get('app.key')),
                'plan_id'           => 1
            ));
        }
    }

}

class SitesTableSeeder extends Seeder
{

    public function run()
    {
        DB::table('sites')->delete();
        $faker = Faker::create();
        foreach (range(2, 10) as $index) {
            App\Models\Site::create(
                    array(
                        'name'       => "TEST_API" . $index,
                        'url'        => $faker->url,
                        'api_key'    => md5($faker->url),
                        "api_secret" => md5($faker->url . "TEST"),
                        "account_id" => $index
            ));
        }
        App\Models\Site::create(
                array(
                    'name'       => "REDMART",
                    'url'        => $faker->url,
                    'api_key'    => md5($faker->url),
                    "api_secret" => md5($faker->url . "TEST"),
                    "account_id" => 1
        ));
    }

}

class ItemTableSeeder extends Seeder
{

    public function run()
    {
        DB::table('items')->delete();
        $faker = Faker::create();
        foreach (range(1, 100) as $index) {
            App\Models\Item::create(
                    array(
                        'identifier' => $index,
                        'name'       => $faker->text(),
                        'site_id'    => 1,
                        "type"       => "product"
            ));
        }
    }

}

class CombinationTableSeeder extends Seeder
{

    public function run()
    {
        DB::table('combinations')->delete();
        \App\Models\Combination::create(
                array(
                    'name'        => 'Default',
                    'description' => 'Default'
                )
        );
    }

}

class ActionDataSeeder extends Seeder
{

    public function run()
    {
        $item_ids    = \App\Models\Item::all()->lists("id");
        $visitor_ids = \App\Models\Session::all()->lists("id");

        for ($i = 31; $i >= 0; $i--) {
            if ($i === 0)
                $dt_created = new Carbon\Carbon('today');
            else
                $dt_created = new Carbon\Carbon($i . " days ago");

            $n_action = rand(1, 200);
            for ($j = 0; $j < 50; $j++) {
//				$is_recommended	 = rand(0, 1);
                $item_index    = rand(0, count($item_ids) - 1);
                $visitor_index = rand(0, count($visitor_ids) - 1);


                //process action instance
                $action_instance             = new \App\Models\ActionInstance();
                $action_instance->action_id  = 21;
                $action_instance->item_id    = $item_ids[$item_index];
                $action_instance->session_id = $visitor_ids[$visitor_index];
                $action_instance->created    = $dt_created;
                $action_instance->save();

//                if (true) {
//                    $action_instance_meta                     = new \App\Models\ActionInstanceMeta();
//                    $action_instance_meta->key                = 'rec';
//                    $action_instance_meta->value              = 'true';
//                    $action_instance_meta->action_instance_id = $action_instance->id;
//                    $action_instance_meta->save();
//                }
            }
        }
    }

}

class EngineTableSeeder extends Seeder
{

    public function run()
    {
        DB::table('engines')->delete();
        \App\Models\Engine::create(
                array(
                    'name'        => 'Tapirus',
                    'description' => 'Tapirus recommendation engine by using graph db'
                )
        );
    }

}

class AlgorithmTableSeeder extends Seeder
{

    public function run()
    {
        $engines = [
            [
                'name'       => 'Tapirus',
                'algorithms' => [
                    [
                        'name'        => 'oiv',
                        'description' => 'Other items viewed'
                    ],
                    [
                        'name'        => 'anon-oiv',
                        'description' => 'Other items viewed, anonymously'
                    ],
                    [
                        'name'        => 'oivt',
                        'description' => 'Other items viewed together'
                    ],
                    [
                        'name'        => 'ct-oivt',
                        'description' => 'Other items viewed together, within the same category'
                    ],
                    [
                        'name'        => 'oip',
                        'description' => 'Other items purchased'
                    ],
                    [
                        'name'        => 'anon-oip',
                        'description' => 'Other items purchased, anonymously'
                    ],
                    [
                        'name'        => 'oipt',
                        'description' => 'Other items purchased together'
                    ],
                    [
                        'name'        => 'ct-oipt',
                        'description' => 'Other items purchased together, within the same category'
                    ],
                    [
                        'name'        => 'trv',
                        'description' => 'Top recent views'
                    ],
                    [
                        'name'        => 'trp',
                        'description' => 'Top recent purchases'
                    ],
                    [
                        'name'        => 'trac',
                        'description' => 'Top recent additions to cart'
                    ],
                    [
                        'name'        => 'utrv',
                        'description' => 'User\'s top recent views'
                    ],
                    [
                        'name'        => 'utrp',
                        'description' => "User's top recent purchases"
                    ],
                    [
                        'name'        => 'utrac',
                        'description' => "User's top recent additions to cart"
                    ],
                    [
                        'name'        => 'uvnp',
                        'description' => "User's unacquired interests"
                    ],
                    [
                        'name'        => 'uacnp',
                        'description' => "User's recent abandoned items"
                    ]
                ]
            ]
        ];

        DB::table('algorithms')->delete();
        foreach ($engines as $engine) {
            $obj = App\Models\Engine::where('name', $engine['name'])->first();
            if ($obj) {
                foreach ($engine['algorithms'] as $algo) {
                    $algo = array_merge(['engine_id' => $obj->id], $algo);
                    \App\Models\Algorithm::create($algo);
                }
            }
        }
    }

}
