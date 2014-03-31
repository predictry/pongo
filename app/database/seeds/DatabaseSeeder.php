<?php

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

class AccountSeeder extends Seeder
{

	public function run()
	{
		DB::table('accounts')->delete();

		$salt = uniqid(mt_rand(), true);

		Account::create(array(
			'id'				 => '1',
			'name'				 => 'Rifki Yandhi',
			'email'				 => 'rifki@vventures.asia',
			'password'			 => Hash::make('password'),
			'confirmed'			 => 1,
			'confirmation_code'	 => md5(microtime() . Config::get('app.key')),
			'plan_id'			 => 1
		));
	}

}
