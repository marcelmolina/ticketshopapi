<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToVentaTemporadaTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('venta_temporada', function(Blueprint $table)
		{
			$table->foreign('email_usuario', 'venta_temporada_fk0')->references('email')->on('usuario')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('id_temporada', 'venta_temporada_fk1')->references('id')->on('temporada')->onUpdate('RESTRICT')->onDelete('RESTRICT');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('venta_temporada', function(Blueprint $table)
		{
			$table->dropForeign('venta_temporada_fk0');
			$table->dropForeign('venta_temporada_fk1');
		});
	}

}
