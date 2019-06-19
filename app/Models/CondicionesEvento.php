<?php

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;
/**
 * Class CondicionesEvento
 * 
 * @property int $id_evento
 * @property int $id_condiciones
 * 
 * @property \App\Models\Evento $evento
 * @property \App\Models\Cuponera $condicion
 *
 * @package App\Models
 */
class CondicionesEvento extends Eloquent
{
    protected $table = 'condiciones_evento';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'id_evento' => 'int',
		'id_condiciones' => 'int'
	];

	protected $fillable = [
		'id_evento',
		'id_condiciones'		
	];

	public function evento()
	{
		return $this->belongsTo(\App\Models\Evento::class, 'id_evento');
	}

	public function condicion()
	{
		return $this->belongsTo(\App\Models\Condicion::class, 'id_condiciones');
	}
}
