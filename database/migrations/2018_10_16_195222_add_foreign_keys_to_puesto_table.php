<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToPuestoTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('puesto', function(Blueprint $table)
		{
			$table->foreign('id_localidad', 'puesto_fk0')->references('id')->on('localidad')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('id_fila', 'puesto_fk1')->references('id')->on('fila')->onUpdate('RESTRICT')->onDelete('RESTRICT');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('puesto', function(Blueprint $table)
		{
			$table->dropForeign('puesto_fk0');
			$table->dropForeign('puesto_fk1');
		});
	}

}
