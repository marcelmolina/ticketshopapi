<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateArtistTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('artist', function(Blueprint $table)
		{
			$table->bigInteger('id', true);
			$table->string('nombre', 200);
			$table->string('manager', 200);
			$table->bigInteger('id_genero')->index('artista_fk0');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('artist');
	}

}
