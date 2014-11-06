<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddCounterToRuleSetsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rule_sets', function(Blueprint $table) {
            $table->integer('expiry_value_counter')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rule_sets', function(Blueprint $table) {
            $table->dropColumn('expiry_value_counter');
        });
    }

}
