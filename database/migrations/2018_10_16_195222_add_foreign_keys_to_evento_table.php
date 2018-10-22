<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToEventoTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('evento', function(Blueprint $table)
		{
			$table->foreign('id_auditorio', 'evento_fk0')->references('id')->on('auditorio')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('id_cliente', 'evento_fk1')->references('id')->on('clientes')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('id_temporada', 'evento_fk2')->references('id')->on('temporada')->onUpdate('RESTRICT')->onDelete('RESTRICT');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('evento', function(Blueprint $table)
		{
			$table->dropForeign('evento_fk0');
			$table->dropForeign('evento_fk1');
			$table->dropForeign('evento_fk2');
		});
	}

}
