<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToPalcoEventoTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('palco_evento', function(Blueprint $table)
		{
			$table->foreign('id_evento', 'palco_evento_fk0')->references('id')->on('evento')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('id_palco', 'palco_evento_fk1')->references('id')->on('palco')->onUpdate('RESTRICT')->onDelete('RESTRICT');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('palco_evento', function(Blueprint $table)
		{
			$table->dropForeign('palco_evento_fk0');
			$table->dropForeign('palco_evento_fk1');
		});
	}

}
