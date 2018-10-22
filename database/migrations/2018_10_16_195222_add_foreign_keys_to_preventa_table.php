<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToPreventaTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('preventa', function(Blueprint $table)
		{
			$table->foreign('id_evento', 'preventa_fk0')->references('id')->on('evento')->onUpdate('RESTRICT')->onDelete('RESTRICT');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('preventa', function(Blueprint $table)
		{
			$table->dropForeign('preventa_fk0');
		});
	}

}
