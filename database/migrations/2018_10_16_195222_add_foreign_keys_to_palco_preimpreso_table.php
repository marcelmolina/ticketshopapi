<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToPalcoPreimpresoTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('palco_preimpreso', function(Blueprint $table)
		{
			$table->foreign('id_palco_evento', 'palco_preimpreso_fk0')->references('id')->on('palco_evento')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('id_puntoventa', 'palco_preimpreso_fk1')->references('id_puntoventa')->on('puntoventa_evento')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('id_preventa', 'palco_preimpreso_fk2')->references('id_preventa')->on('boletas_prevent')->onUpdate('RESTRICT')->onDelete('RESTRICT');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('palco_preimpreso', function(Blueprint $table)
		{
			$table->dropForeign('palco_preimpreso_fk0');
			$table->dropForeign('palco_preimpreso_fk1');
			$table->dropForeign('palco_preimpreso_fk2');
		});
	}

}
