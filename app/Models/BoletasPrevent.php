<?php

/**
 * Created by Reliese Model.
 * Date: Tue, 16 Oct 2018 19:32:03 +0000.
 */

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class BoletasPrevent
 * 
 * @property int $id_boleta
 * @property int $id_preventa
 * @property float $precio_venta
 * @property float $precio_servicio
 * @property float $impuesto
 * @property int $status
 * 
 * @property \App\Models\BoletaEvento $boleta_evento
 * @property \App\Models\Preventum $preventum
 * @property \Illuminate\Database\Eloquent\Collection $palco_preimpresos
 *
 * @package App\Models
 */
class BoletasPrevent extends Eloquent
{
	protected $table = 'boletas_prevent';
	protected $primaryKey = 'id_boleta';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'id_boleta' => 'int',
		'id_preventa' => 'int',
		'precio_venta' => 'float',
		'precio_servicio' => 'float',
		'impuesto' => 'float',
		'status' => 'int'
	];

	protected $fillable = [
		'id_preventa',
		'precio_venta',
		'precio_servicio',
		'impuesto',
		'status'
	];

	public function boleta_evento()
	{
		return $this->belongsTo(\App\Models\BoletaEvento::class, 'id_boleta');
	}

	public function preventum()
	{
		return $this->belongsTo(\App\Models\Preventum::class, 'id_preventa');
	}

	public function palco_preimpresos()
	{
		return $this->hasMany(\App\Models\PalcoPreimpreso::class, 'id_preventa', 'id_preventa');
	}
}
