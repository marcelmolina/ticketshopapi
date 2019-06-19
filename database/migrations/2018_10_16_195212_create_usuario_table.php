<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUsuarioTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('usuario', function(Blueprint $table)
		{
			$table->string('email', 200)->primary();
			$table->string('nombre', 200);
			$table->binary('password', 65535);
			$table->string('identificacion', 200);
			$table->boolean('tipo_identificacion');
			$table->string('direccion', 200);
			$table->string('ciudad', 200);
			$table->string('departamento', 200);
			$table->string('telefono', 200);
			$table->bigInteger('id_rol')->index('usuario_fk0');
			$table->boolean('active') ->default(false);
			$table->string('provider')->nullable();
			$table->string('provider_id')->nullable();			 
            $table->string('activation_token');
			$table->softDeletes();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('usuario');
	}

}
