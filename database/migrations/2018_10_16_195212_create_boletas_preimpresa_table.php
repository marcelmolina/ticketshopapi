<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateBoletasPreimpresaTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('boletas_preimpresa', function(Blueprint $table)
		{
			$table->bigInteger('id_boleta')->primary();
			$table->bigInteger('id_puntoventa')->index('boletas_preimpresa_fk1');
			$table->bigInteger('id_preventa')->nullable()->index('boletas_preimpresa_fk2');
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
		Schema::drop('boletas_preimpresa');
	}

}
