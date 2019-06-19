<?php

/**
 * Created by Reliese Model.
 * Date: Tue, 16 Oct 2018 19:32:04 +0000.
 */

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class DescuentoEvento
 * 
 * @property int $id
 * @property string $nombre
 * @property int $id_evento
 * @property \Carbon\Carbon $fecha_hora_inicio
 * @property \Carbon\Carbon $fecha_hora_fin
 * @property int $status
 * @property int $tipo_descuento
 * @property float $porcentaje
 * @property float $monto
 * @property int $cantidad_compra
 * @property int $cantidad_paga
 * 
 * @property \App\Models\Evento $evento
 * @property \Illuminate\Database\Eloquent\Collection $detalle_descuentos
 * @property \Illuminate\Database\Eloquent\Collection $detalle_vents
 *
 * @package App\Models
 */
class DescuentoEvento extends Eloquent
{
	protected $table = 'descuento_evento';
	public $timestamps = false;

	protected $casts = [
		'id_evento' => 'int',
		'status' => 'int',
		'tipo_descuento' => 'int',
		'porcentaje' => 'float',
		'monto' => 'float',
		'cantidad_compra' => 'int',
		'cantidad_paga' => 'int'
	];

	protected $dates = [
		'fecha_hora_inicio',
		'fecha_hora_fin'
	];

	protected $fillable = [
		'nombre',
		'id_evento',
		'fecha_hora_inicio',
		'fecha_hora_fin',
		'status',
		'tipo_descuento',
		'porcentaje',
		'monto',
		'cantidad_compra',
		'cantidad_paga',
		'codigo_moneda'
	];

	public function evento()
	{
		return $this->belongsTo(\App\Models\Evento::class, 'id_evento');
	}

	public function tipo_descuento()
	{
		return $this->belongsTo(\App\Models\TipoDescuento::class, 'tipo_descuento');
	}

	public function codigo_moneda()
	{
		return $this->belongsTo(\App\Models\Moneda::class, 'codigo_moneda');
	}

	public function detalle_descuentos()
	{
		return $this->hasMany(\App\Models\DetalleDescuento::class, 'id_descuento');
	}

	public function detalle_vents()
	{
		return $this->hasMany(\App\Models\DetalleVent::class, 'id_descuento');
	}
}
