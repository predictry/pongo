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

//		$this->call('SitesTableSeeder');
//		$this->call('PlanSeeder');
//		$this->call('AccountSeeder');
//		$this->call('ActionsTableSeeder');
		$this->call('ActionTypesSeeder');
	}

}

class PlanSeeder extends Seeder
{

	public function run()
	{
		DB::table('plans')->delete();

		Plan::create(
				array(
					'id'			 => '1',
					'name'			 => 'Plan A',
					'description'	 => 'Plan A',
					'currency'		 => 'USD',
					'price'			 => '9',
					'limit_value'	 => '1',
				)
		);

		Plan::create(
				array(
					'id'			 => '2',
					'name'			 => 'Plan B',
					'description'	 => 'Plan B',
					'currency'		 => 'USD',
					'price'			 => '99',
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
		Actions::create(
				array(
					'name'			 => 'view',
					'description'	 => ''
				)
		);
		ActionType::create(
				array(
					'name'	 => 'rate',
					'score'	 => '2'
				)
		);
		ActionType::create(
				array(
					'name'	 => 'add_to_cart',
					'score'	 => '3'
				)
		);
		ActionType::create(
				array(
					'name'	 => 'buy',
					'score'	 => '4'
				)
		);
	}

}

class AccountSeeder extends Seeder
{

	public function run()
	{
		DB::table('accounts')->delete();

		$faker = Faker::create();

		foreach (range(1, 10) as $index)
		{
			Account::create(array(
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

class ActionsTableSeeder extends Seeder
{

	public function run()
	{
		DB::table('actions')->delete();
		DB::table('action_metas')->delete();
		$actions = array("view", "rate", "add_to_cart", "buy");

		foreach (range(1, 2000) as $index)
		{
			$action	 = $score	 = rand(0, 3);
			$user_id = rand(1, 500);
			$item_id = rand(200, 1000);

			$desc = array(
				'score'		 => $score + 1,
				"item_id"	 => $item_id,
				"user_id"	 => $user_id
			);

			$id = Action::create(
							array(
								'name'			 => $actions[$action],
								'description'	 => json_encode($desc),
								'site_id'		 => 1
			));

			ActionMeta::create(array(
				"key"		 => "score",
				"value"		 => $score + 1,
				"action_id"	 => (int) $id->id
			));

			ActionMeta::create(array(
				"key"		 => "item_id",
				"value"		 => $item_id,
				"action_id"	 => (int) $id->id
			));
			ActionMeta::create(array(
				"key"		 => "user_id",
				"value"		 => $user_id,
				"action_id"	 => (int) $id->id
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
			Site::create(
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
