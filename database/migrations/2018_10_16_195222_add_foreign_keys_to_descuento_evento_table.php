<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToDescuentoEventoTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('descuento_evento', function(Blueprint $table)
		{
			$table->foreign('id_evento', 'descuento_evento_fk0')->references('id')->on('evento')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('tipo_descuento', 'descuento_evento_fk1')->references('id')->on('tipo_descuento')->onUpdate('RESTRICT')->onDelete('RESTRICT');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('descuento_evento', function(Blueprint $table)
		{
			$table->dropForeign('descuento_evento_fk0');
			$table->dropForeign('descuento_evento_fk1');
		});
	}

}
