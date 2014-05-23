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
//		$this->call('ItemTableSeeder');
	}

}

class PlanSeeder extends Seeder
{

	public function run()
	{
		DB::table('plans')->delete();

		App\Models\Plan::create(
				array(
					'id'			 => '1',
					'name'			 => 'Plan A',
					'description'	 => 'Plan A',
					'currency'		 => 'USD',
					'price'			 => '9',
					'limit_value'	 => '1',
				)
		);

		App\Models\Plan::create(
				array(
					'id'			 => '2',
					'name'			 => 'Plan B',
					'description'	 => 'Plan B',
					'currency'		 => 'USD',
					'price'			 => '99',
					'limit_value'	 => '12',
				)
		);

		App\Models\Plan::create(
				array(
					'id'			 => '3',
					'name'			 => 'Plan C',
					'description'	 => 'Plan C',
					'currency'		 => 'USD',
					'price'			 => '999',
					'limit_value'	 => '12',
				)
		);
	}

}

class ActionsSeeder extends Seeder
{

	function run()
	{
		DB::table('actions')->delete();
		\App\Models\Action::create(
				array(
					'name'		 => 'view',
					'site_id'	 => 1
				)
		);
		\App\Models\Action::create(
				array(
					'name'		 => 'rate',
					'site_id'	 => 1
				)
		);
		\App\Models\Action::create(
				array(
					'name'		 => 'add_to_cart',
					'site_id'	 => 1
				)
		);
		\App\Models\Action::create(
				array(
					'name'		 => 'buy',
					'site_id'	 => 1
				)
		);
	}

}

class AccountSeeder extends Seeder
{

	public function run()
	{
		DB::table('accounts')->delete();

		App\Models\Account::create(array(
			'id'				 => 1,
			'name'				 => 'Rifki Yandhi',
			'email'				 => 'rifkiyandhi@gmail.com',
			'password'			 => Hash::make('password'),
			'confirmed'			 => 1,
			'confirmation_code'	 => md5(microtime() . Config::get('app.key')),
			'plan_id'			 => 1
		));

		$faker = Faker::create();
		foreach (range(2, 10) as $index)
		{
			App\Models\Account::create(array(
				'id'				 => $index,
				'name'				 => $faker->name,
				'email'				 => $faker->email,
				'password'			 => Hash::make('password'),
				'confirmed'			 => 1,
				'confirmation_code'	 => md5(microtime() . Config::get('app.key')),
				'plan_id'			 => 1
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
		foreach (range(1, 10) as $index)
		{
			App\Models\Site::create(
					array(
						'name'		 => "TEST_API" . $index,
						'url'		 => $faker->url,
						'api_key'	 => md5($faker->url),
						"api_secret" => md5($faker->url . "TEST"),
						"account_id" => rand(1, 10)
			));
		}
	}

}

class ItemTableSeeder extends Seeder
{

	public function run()
	{
		DB::table('items')->delete();
		$faker = Faker::create();
		foreach (range(1, 100) as $index)
		{
			App\Models\Item::create(
					array(
						'identifier' => $index,
						'name'		 => $faker->text(),
						'site_id'	 => 1,
						"type"		 => "product"
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
					'name'			 => 'Default',
					'description'	 => 'Default'
				)
		);
	}

}
