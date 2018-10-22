<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToTribunaTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('tribuna', function(Blueprint $table)
		{
			$table->foreign('id_auditorio', 'tribuna_fk0')->references('id')->on('auditorio')->onUpdate('RESTRICT')->onDelete('RESTRICT');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('tribuna', function(Blueprint $table)
		{
			$table->dropForeign('tribuna_fk0');
		});
	}

}
