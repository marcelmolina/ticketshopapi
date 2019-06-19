<?php

/**
 * Created by Reliese Model.
 * Date: Tue, 16 Oct 2018 19:32:04 +0000.
 */

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class PuntoventaEvento
 * 
 * @property int $id_evento
 * @property int $id_puntoventa
 * 
 * @property \App\Models\Evento $evento
 * @property \App\Models\PuntoVentum $punto_ventum
 * @property \Illuminate\Database\Eloquent\Collection $boletas_preimpresas
 * @property \Illuminate\Database\Eloquent\Collection $palco_preimpresos
 *
 * @package App\Models
 */
class PuntoventaEvento extends Eloquent
{
	protected $table = 'puntoventa_evento';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'id_evento' => 'int',
		'id_puntoventa' => 'int'
	];

	protected $fillable = [
		'id_evento', 
		'id_puntoventa'
	];

	public function evento()
	{
		return $this->belongsTo(\App\Models\Evento::class, 'id_evento');
	}

	public function punto_ventum()
	{
		return $this->belongsTo(\App\Models\PuntoVentum::class, 'id_puntoventa');
	}

	public function boletas_preimpresas()
	{
		return $this->hasMany(\App\Models\BoletasPreimpresa::class, 'id_puntoventa', 'id_puntoventa');
	}

	public function palco_preimpresos()
	{
		return $this->hasMany(\App\Models\PalcoPreimpreso::class, 'id_puntoventa', 'id_puntoventa');
	}
}
