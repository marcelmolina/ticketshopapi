<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToBoletasPreimpresaTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('boletas_preimpresa', function(Blueprint $table)
		{
			$table->foreign('id_boleta', 'boletas_preimpresa_fk0')->references('id')->on('boleta_evento')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('id_puntoventa', 'boletas_preimpresa_fk1')->references('id_puntoventa')->on('puntoventa_evento')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('id_preventa', 'boletas_preimpresa_fk2')->references('id')->on('preventa')->onUpdate('RESTRICT')->onDelete('RESTRICT');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('boletas_preimpresa', function(Blueprint $table)
		{
			$table->dropForeign('boletas_preimpresa_fk0');
			$table->dropForeign('boletas_preimpresa_fk1');
			$table->dropForeign('boletas_preimpresa_fk2');
		});
	}

}
