<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToBoletasPreventTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('boletas_prevent', function(Blueprint $table)
		{
			$table->foreign('id_boleta', 'boletas_preventa_fk0')->references('id')->on('boleta_evento')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('id_preventa', 'boletas_preventa_fk1')->references('id')->on('preventa')->onUpdate('RESTRICT')->onDelete('RESTRICT');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('boletas_prevent', function(Blueprint $table)
		{
			$table->dropForeign('boletas_preventa_fk0');
			$table->dropForeign('boletas_preventa_fk1');
		});
	}

}
