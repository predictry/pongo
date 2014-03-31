<?php
/**
 * An helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace {
/**
 * Author       : Rifki Yandhi
 * Date Created : Mar 18, 2014 5:10:42 PM
 * File         : app/models/Account.php
 * Copyright    : rifkiyandhi@gmail.com
 * Function     :
 *
 * @property integer $id
 * @property string $name
 * @property string $email
 * @property string $password_hash
 * @property string $password_salt
 * @property integer $plan_id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property \Carbon\Carbon $deleted_at
 * @method static \Illuminate\Database\Query\Builder|\Account whereId($value) 
 * @method static \Illuminate\Database\Query\Builder|\Account whereName($value) 
 * @method static \Illuminate\Database\Query\Builder|\Account whereEmail($value) 
 * @method static \Illuminate\Database\Query\Builder|\Account wherePasswordHash($value) 
 * @method static \Illuminate\Database\Query\Builder|\Account wherePasswordSalt($value) 
 * @method static \Illuminate\Database\Query\Builder|\Account wherePlanId($value) 
 * @method static \Illuminate\Database\Query\Builder|\Account whereCreatedAt($value) 
 * @method static \Illuminate\Database\Query\Builder|\Account whereUpdatedAt($value) 
 * @method static \Illuminate\Database\Query\Builder|\Account whereDeletedAt($value) 
 */
	class Account {}
}

namespace {
/**
 * Author       : Rifki Yandhi
 * Date Created : Mar 18, 2014 5:19:50 PM
 * File         : app/models/Plan.php
 * Copyright    : rifkiyandhi@gmail.com
 * Function     :
 *
 * @property integer $id
 * @property string $name
 * @property string $description
 * @property string $currency
 * @property float $price
 * @property string $billing_cycle
 * @property string $limit_type
 * @property integer $limit_value
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property \Carbon\Carbon $deleted_at
 * @method static \Illuminate\Database\Query\Builder|\Plan whereId($value) 
 * @method static \Illuminate\Database\Query\Builder|\Plan whereName($value) 
 * @method static \Illuminate\Database\Query\Builder|\Plan whereDescription($value) 
 * @method static \Illuminate\Database\Query\Builder|\Plan whereCurrency($value) 
 * @method static \Illuminate\Database\Query\Builder|\Plan wherePrice($value) 
 * @method static \Illuminate\Database\Query\Builder|\Plan whereBillingCycle($value) 
 * @method static \Illuminate\Database\Query\Builder|\Plan whereLimitType($value) 
 * @method static \Illuminate\Database\Query\Builder|\Plan whereLimitValue($value) 
 * @method static \Illuminate\Database\Query\Builder|\Plan whereCreatedAt($value) 
 * @method static \Illuminate\Database\Query\Builder|\Plan whereUpdatedAt($value) 
 * @method static \Illuminate\Database\Query\Builder|\Plan whereDeletedAt($value) 
 */
	class Plan {}
}

namespace {
/**
 * Author       : Rifki Yandhi
 * Date Created : Mar 18, 2014 5:35:03 PM
 * File         : app/models/Site.php
 * Copyright    : rifkiyandhi@gmail.com
 * Function     :
 *
 * @property integer $id
 * @property string $name
 * @property string $api_key
 * @property string $api_secret
 * @property integer $account_id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property \Carbon\Carbon $deleted_at
 * @method static \Illuminate\Database\Query\Builder|\Site whereId($value) 
 * @method static \Illuminate\Database\Query\Builder|\Site whereName($value) 
 * @method static \Illuminate\Database\Query\Builder|\Site whereApiKey($value) 
 * @method static \Illuminate\Database\Query\Builder|\Site whereApiSecret($value) 
 * @method static \Illuminate\Database\Query\Builder|\Site whereAccountId($value) 
 * @method static \Illuminate\Database\Query\Builder|\Site whereCreatedAt($value) 
 * @method static \Illuminate\Database\Query\Builder|\Site whereUpdatedAt($value) 
 * @method static \Illuminate\Database\Query\Builder|\Site whereDeletedAt($value) 
 */
	class Site {}
}

namespace {
/**
 * User
 *
 */
	class User {}
}

