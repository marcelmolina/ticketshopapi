<?php

/**
 * Created by Reliese Model.
 * Date: Tue, 16 Oct 2018 19:32:03 +0000.
 */

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class Cupon
 * 
 * @property int $id
 * @property string $codigo
 * @property bool $status
 * @property float $monto
 * @property float $porcentaje_descuento
 * @property int $id_tipo_cupon
 * @property int $id_cuponera
 * @property int $cantidad_compra
 * @property int $cantidad_paga
 * 
 * @property \App\Models\TipoCupon $tipo_cupon
 * @property \App\Models\Cuponera $cuponera
 *
 * @package App\Models
 */
class Cupon extends Eloquent
{
	protected $table = 'cupon';
	public $timestamps = false;

	protected $casts = [
		'status' => 'bool',
		'monto' => 'float',
		'porcentaje_descuento' => 'float',
		'id_tipo_cupon' => 'int',
		'id_cuponera' => 'int',
		'cantidad_compra' => 'int',
		'cantidad_paga' => 'int'
	];

	protected $fillable = [
		'codigo',
		'status',
		'monto',
		'porcentaje_descuento',
		'id_tipo_cupon',
		'id_cuponera',
		'cantidad_compra',
		'cantidad_paga'
	];

	public function tipo_cupon()
	{
		return $this->belongsTo(\App\Models\TipoCupon::class, 'id_tipo_cupon');
	}

	public function cuponera()
	{
		return $this->belongsTo(\App\Models\Cuponera::class, 'id_cuponera');
	}
}
