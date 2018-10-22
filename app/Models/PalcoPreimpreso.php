<?php

/**
 * Created by Reliese Model.
 * Date: Tue, 16 Oct 2018 19:32:04 +0000.
 */

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class PalcoPreimpreso
 * 
 * @property int $id_palco_evento
 * @property int $id_puntoventa
 * @property int $id_preventa
 * @property int $status
 * 
 * @property \App\Models\PalcoEvento $palco_evento
 * @property \App\Models\PuntoventaEvento $puntoventa_evento
 * @property \App\Models\BoletasPrevent $boletas_prevent
 *
 * @package App\Models
 */
class PalcoPreimpreso extends Eloquent
{
	protected $table = 'palco_preimpreso';
	protected $primaryKey = 'id_palco_evento';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'id_palco_evento' => 'int',
		'id_puntoventa' => 'int',
		'id_preventa' => 'int',
		'status' => 'int'
	];

	protected $fillable = [
		'id_puntoventa',
		'id_preventa',
		'status'
	];

	public function palco_evento()
	{
		return $this->belongsTo(\App\Models\PalcoEvento::class, 'id_palco_evento');
	}

	public function puntoventa_evento()
	{
		return $this->belongsTo(\App\Models\PuntoventaEvento::class, 'id_puntoventa', 'id_puntoventa');
	}

	public function boletas_prevent()
	{
		return $this->belongsTo(\App\Models\BoletasPrevent::class, 'id_preventa', 'id_preventa');
	}
}
