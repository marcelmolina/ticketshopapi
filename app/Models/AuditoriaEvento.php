<?php

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;

class AuditoriaEvento extends Eloquent
{
    protected $table = 'evento_auditoria';
	public $timestamps = false;

	protected $fillable = [
		'id_evento',
		'observacion',
		'status_1',
		'status_2',
		'date',
		'email_usuario'		
	];

	public function evento()
	{
		return $this->belongsTo(\App\Models\Evento::class, 'id_evento');
	}

	public function usuario()
	{
		return $this->belongsTo(\App\Models\Usuario::class, 'email_usuario');
	}
}

