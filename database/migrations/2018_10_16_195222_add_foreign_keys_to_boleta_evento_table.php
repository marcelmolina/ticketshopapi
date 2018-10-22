<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToBoletaEventoTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('boleta_evento', function(Blueprint $table)
		{
			$table->foreign('id_evento', 'boleta_evento_fk0')->references('id')->on('evento')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('id_puesto', 'boleta_evento_fk1')->references('id')->on('puesto')->onUpdate('RESTRICT')->onDelete('RESTRICT');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('boleta_evento', function(Blueprint $table)
		{
			$table->dropForeign('boleta_evento_fk0');
			$table->dropForeign('boleta_evento_fk1');
		});
	}

}
