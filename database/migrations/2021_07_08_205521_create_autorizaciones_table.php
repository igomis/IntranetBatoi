<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAutorizacionesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('autorizaciones', function(Blueprint $table)
		{
			$table->integer('idActividad')->unsigned();
            $table->string('idAlumno', 8)->collation('utf8_unicode_ci')->index('autorizaciones_idalumno_foreign');
            $table->boolean('autorizado')->default(0);
            $table->primary(['idActividad','idAlumno']);
            $table->foreign('idActividad')->references('id')->on('actividades')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign('idAlumno')->references('nia')->on('alumnos')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('autorizaciones');
	}

}
