<?php

/**
 * Created by Reliese Model.
 * Date: Tue, 16 Oct 2018 19:32:03 +0000.
 */

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class LocalidadEvento
 * 
 * @property int $id_localidad
 * @property int $id_evento
 * @property string $url_imagen
 * @property float $impuesto
 * @property float $precio_venta
 * @property float $precio_servicio
 * @property string $codigo_moneda
 * 
 * @property \App\Models\Evento $evento
 * @property \App\Models\Localidad $localidad
 *
 * @package App\Models
 */
class LocalidadEvento extends Eloquent
{
	protected $table = 'localidad_evento';
	public $timestamps = false;

	protected $casts = [
		'id_localidad' => 'int',
		'id_evento' => 'int',
		'id_precios_monedas' => 'int',
		'impuesto' => 'float',
		'precio_venta' => 'float',
		'precio_servicio' => 'float',
		'precio_venta2' => 'float',
		'precio_servicio2' => 'float'
	];

	protected $fillable = [
		'id_localidad',
		'id_evento',
		'url_imagen',
		'impuesto',
		'precio_venta',
		'precio_servicio',
		'codigo_moneda',
		'precio_venta2',
		'precio_servicio2',
		'codigo_moneda2'						
	];

	public function localidad()
	{
		return $this->belongsTo(\App\Models\Localidad::class, 'id_localidad');
	}

	public function evento()
	{
		return $this->belongsTo(\App\Models\Evento::class, 'id_evento');
	}

	public function codigo_moneda()
	{
		return $this->belongsTo(\App\Models\Moneda::class, 'codigo_moneda')->whereNotNull('codigo_moneda');
	}

	public function codigo_moneda2()
	{
		return $this->belongsTo(\App\Models\Moneda::class, 'codigo_moneda2')->whereNotNull('codigo_moneda');
	}

	
}
