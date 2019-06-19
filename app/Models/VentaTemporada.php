<?php

/**
 * Created by Reliese Model.
 * Date: Tue, 16 Oct 2018 19:32:04 +0000.
 */

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class VentaTemporada
 * 
 * @property int $id
 * @property \Carbon\Carbon $fecha
 * @property string $email_usuario
 * @property int $id_temporada
 * @property string $tipo_venta
 * @property string $identificacion
 * @property string $nombre
 * @property string $direccion
 * @property string $telefono
 * @property string $email
 * @property bool $tipo_identidicacion
 * @property float $precio_venta
 * @property float $impuesto
 * @property string $codigo_moneda
 * 
 * @property \App\Models\Usuario $usuario
 * @property \App\Models\Moneda $moneda
 * @property \App\Models\Temporada $temporada
 * @property \Illuminate\Database\Eloquent\Collection $detalle_venta_temporadas
 *
 * @package App\Models
 */
class VentaTemporada extends Eloquent
{
	protected $table = 'venta_temporada';
	public $timestamps = false;

	protected $casts = [
		'id_temporada' => 'int',
		'tipo_identidicacion' => 'bool',
		'precio_venta' => 'float',
		'impuesto' => 'float'
	];

	protected $dates = [
		'fecha'
	];

	protected $fillable = [
		'fecha',
		'email_usuario',
		'id_temporada',
		'tipo_venta',
		'identificacion',
		'nombre',
		'direccion',
		'telefono',
		'email',
		'tipo_identidicacion',
		'precio_venta',
		'impuesto',
		'codigo_moneda'
	];

	public function usuario()
	{
		return $this->belongsTo(\App\Models\Usuario::class, 'email_usuario');
	}

	public function moneda()
	{
		return $this->belongsTo(\App\Models\Moneda::class, 'codigo_moneda');
	}

	public function temporada()
	{
		return $this->belongsTo(\App\Models\Temporada::class, 'id_temporada');
	}

	public function detalle_venta_temporadas()
	{
		return $this->hasMany(\App\Models\DetalleVentaTemporada::class, 'id_venta_temporada');
	}
}
