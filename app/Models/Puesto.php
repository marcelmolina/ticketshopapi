<?php

/**
 * Created by Reliese Model.
 * Date: Tue, 16 Oct 2018 19:32:04 +0000.
 */

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class Puesto
 * 
 * @property int $id
 * @property string $numero
 * @property int $id_localidad
 * @property int $id_fila
 * 
 * @property \App\Models\Localidad $localidad
 * @property \App\Models\Fila $fila
 * @property \Illuminate\Database\Eloquent\Collection $boleta_eventos
 * @property \Illuminate\Database\Eloquent\Collection $palcos
 *
 * @package App\Models
 */
class Puesto extends Eloquent
{
	protected $table = 'puesto';
	public $timestamps = false;

	protected $casts = [
		'id_localidad' => 'int',
		'id_fila' => 'int'
	];

	protected $fillable = [
		'numero',
		'id_localidad',
		'id_fila'
	];

	public function localidad()
	{
		return $this->belongsTo(\App\Models\Localidad::class, 'id_localidad');
	}

	public function fila()
	{
		return $this->belongsTo(\App\Models\Fila::class, 'id_fila');
	}

	public function boleta_eventos()
	{
		return $this->hasMany(\App\Models\BoletaEvento::class, 'id_puesto');
	}

	public function palcos()
	{
		return $this->belongsToMany(\App\Models\Palco::class, 'puestos_palco', 'id_puesto', 'id_palco');
	}
}
