<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToPalcoTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('palco', function(Blueprint $table)
		{
			$table->foreign('id_localidad', 'palco_fk0')->references('id')->on('localidad')->onUpdate('RESTRICT')->onDelete('RESTRICT');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('palco', function(Blueprint $table)
		{
			$table->dropForeign('palco_fk0');
		});
	}

}
