<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToPalcoPreventTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('palco_prevent', function(Blueprint $table)
		{
			$table->foreign('id_palco_evento', 'palco_preventa_fk0')->references('id')->on('palco_evento')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('id_preventa', 'palco_preventa_fk1')->references('id')->on('preventa')->onUpdate('RESTRICT')->onDelete('RESTRICT');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('palco_prevent', function(Blueprint $table)
		{
			$table->dropForeign('palco_preventa_fk0');
			$table->dropForeign('palco_preventa_fk1');
		});
	}

}
