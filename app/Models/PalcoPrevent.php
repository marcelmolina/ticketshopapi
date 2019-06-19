<?php

/**
 * Created by Reliese Model.
 * Date: Tue, 16 Oct 2018 19:32:04 +0000.
 */

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class PalcoPrevent
 * 
 * @property int $id_palco_evento
 * @property int $id_preventa
 * @property float $precio_venta
 * @property float $precio_servicio
 * @property float $impuesto
 * @property string $status
 * @property string $codigo_moneda
 * 
 * @property \App\Models\PalcoEvento $palco_evento
 * @property \App\Models\Preventum $preventum
 *
 * @package App\Models
 */
class PalcoPrevent extends Eloquent
{
	protected $table = 'palco_prevent';
	protected $primaryKey = 'id_palco_evento';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'id_palco_evento' => 'int',
		'id_preventa' => 'int',
		'precio_venta' => 'float',
		'precio_servicio' => 'float',
		'impuesto' => 'float'
	];

	protected $fillable = [
		'id_preventa',
		'precio_venta',
		'precio_servicio',
		'impuesto',
		'status',
		'codigo_moneda'
	];

	public function palco_evento()
	{
		return $this->belongsTo(\App\Models\PalcoEvento::class, 'id_palco_evento');
	}

	public function preventum()
	{
		return $this->belongsTo(\App\Models\Preventum::class, 'id_preventa');
	}

	public function moneda()
	{
		return $this->belongsTo(\App\Models\Moneda::class, 'codigo_moneda');
	}
}
