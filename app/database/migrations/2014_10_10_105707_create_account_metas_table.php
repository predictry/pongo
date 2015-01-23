<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAccountMetasTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('account_metas', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('account_id')->unsigned();
            $table->string('key', 50);
            $table->string('value', 150);
            $table->timestamps();

            $table->foreign('account_id')->references('id')->on('accounts')->onDelete('restrict')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('account_metas');
    }

}
