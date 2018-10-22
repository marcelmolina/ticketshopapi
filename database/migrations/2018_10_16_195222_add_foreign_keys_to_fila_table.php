<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToFilaTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('fila', function(Blueprint $table)
		{
			$table->foreign('id_localidad', 'fila_fk0')->references('id')->on('localidad')->onUpdate('RESTRICT')->onDelete('RESTRICT');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('fila', function(Blueprint $table)
		{
			$table->dropForeign('fila_fk0');
		});
	}

}
