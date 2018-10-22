<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePalcoPreimpresoTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('palco_preimpreso', function(Blueprint $table)
		{
			$table->bigInteger('id_palco_evento')->primary();
			$table->bigInteger('id_puntoventa')->index('palco_preimpreso_fk1');
			$table->bigInteger('id_preventa')->nullable()->index('palco_preimpreso_fk2');
			$table->boolean('status')->nullable()->default(0);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('palco_preimpreso');
	}

}
