<?php

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class DescuentoEvento
 * 
 * @property string $codigo_moneda
 * @property string $descripcion
 * @property string $simbolo
 * 
 * @property \App\Models\Evento $descuento_evento
 *
 * @package App\Models
 */
class Moneda extends Eloquent
{
    protected $table = 'moneda';	
	protected $primaryKey = 'codigo_moneda';
	public $incrementing = false;
	public $timestamps = false;

	protected $fillable = [
		'codigo_moneda',
		'descripcion',
		'simbolo'
	];

	public function descuento_evento()
	{
		return $this->hasMany(\App\Models\DescuentoEvento::class, 'codigo_moneda');
	}

}
