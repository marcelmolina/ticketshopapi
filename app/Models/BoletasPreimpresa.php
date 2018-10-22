<?php

/**
 * Created by Reliese Model.
 * Date: Tue, 16 Oct 2018 19:32:03 +0000.
 */

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class BoletasPreimpresa
 * 
 * @property int $id_boleta
 * @property int $id_puntoventa
 * @property int $id_preventa
 * @property int $status
 * 
 * @property \App\Models\BoletaEvento $boleta_evento
 * @property \App\Models\PuntoventaEvento $puntoventa_evento
 * @property \App\Models\Preventum $preventum
 *
 * @package App\Models
 */
class BoletasPreimpresa extends Eloquent
{
	protected $table = 'boletas_preimpresa';
	protected $primaryKey = 'id_boleta';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'id_boleta' => 'int',
		'id_puntoventa' => 'int',
		'id_preventa' => 'int',
		'status' => 'int'
	];

	protected $fillable = [
		'id_puntoventa',
		'id_preventa',
		'status'
	];

	public function boleta_evento()
	{
		return $this->belongsTo(\App\Models\BoletaEvento::class, 'id_boleta');
	}

	public function puntoventa_evento()
	{
		return $this->belongsTo(\App\Models\PuntoventaEvento::class, 'id_puntoventa', 'id_puntoventa');
	}

	public function preventum()
	{
		return $this->belongsTo(\App\Models\Preventum::class, 'id_preventa');
	}
}
