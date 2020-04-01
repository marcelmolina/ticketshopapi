<?php

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class PreciosMonedas
 * 
 * @property int $id
 * @property int $id_evento
 * @property int $id_palco_evento
 * @property int $id_boleta_evento
 * @property int $id_localidad_evento
 * @property int $id_preventa
 * @property int $id_costo_evento
 * @property float $monto_minimo
 * @property float $precio_venta
 * @property float $precio_servicio
 * @property float $porcentaje_descuento_precio
 * @property float $porcentaje_descuento_servicio
 * @property float $valor
 * @property bolean $status
 * @property string $codigo_moneda
 * 
 * @property \App\Models\Evento $evento
 * @property \App\Models\BoletaEvento $boleta_evento
 * @property \App\Models\PalcoEvento $palco_evento
 * @property \App\Models\Preventum $preventum 
 * @property \App\Models\CostoEvento $costo_evento
 * @property \App\Models\Moneda $moneda 
 *
 * @package App\Models
 */
class PreciosMonedas extends Eloquent
{
    protected $table = 'precios_monedas';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'id_palco_evento' => 'int',
		'id_preventa' => 'int',
		'id_evento' => 'int',
		'id_boleta_evento' => 'int',
		'id_costo_evento' => 'int',	
		
	];

	protected $fillable = [
		'id_preventa',
		'id_palco_evento',
		'id_evento',
		'id_boleta_evento',
		'id_costo_evento',
		'monto_minimo',
		'precio_venta',
		'precio_servicio',
		'descuento_fijo_precio',
		'descuento_fijo_servicio',
		'valor',
		'status',
		'codigo_moneda'
	];

	public function evento()
	{
		return $this->belongsTo(\App\Models\Evento::class, 'id_evento');
	}

	public function palco_evento()
	{
		return $this->belongsTo(\App\Models\PalcoEvento::class, 'id_palco_evento');
	}

	public function boleta_evento()
	{
		return $this->belongsTo(\App\Models\BoletaEvento::class, 'id_boleta_evento');
	}

	public function preventum()
	{
		return $this->belongsTo(\App\Models\Preventum::class, 'id_preventa');
	}

	public function costo_evento()
	{
		return $this->belongsTo(\App\Models\CostoEvento::class, 'id_costo_evento');
	}

	public function moneda()
	{
		return $this->belongsTo(\App\Models\Moneda::class, 'codigo_moneda');
	}

}
