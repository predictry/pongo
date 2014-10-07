<?php

use Illuminate\Database\Migrations\Migration;

class AlterVisitorIdOnSessionsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared('ALTER TABLE "sessions" ALTER "visitor_id" TYPE integer, ALTER "visitor_id" DROP DEFAULT, ALTER "visitor_id" DROP NOT NULL');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //DB::unprepared('ALTER TABLE "sessions" ALTER "visitor_id" TYPE integer, ALTER "visitor_id" DROP DEFAULT, ALTER "visitor_id" SET NOT NULL');
    }

}
