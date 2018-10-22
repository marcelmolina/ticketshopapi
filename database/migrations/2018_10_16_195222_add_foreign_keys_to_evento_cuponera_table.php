<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToEventoCuponeraTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('evento_cuponera', function(Blueprint $table)
		{
			$table->foreign('id_evento', 'evento_cuponera_fk0')->references('id')->on('evento')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('id_cuponera', 'evento_cuponera_fk1')->references('id')->on('cuponera')->onUpdate('RESTRICT')->onDelete('RESTRICT');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('evento_cuponera', function(Blueprint $table)
		{
			$table->dropForeign('evento_cuponera_fk0');
			$table->dropForeign('evento_cuponera_fk1');
		});
	}

}
