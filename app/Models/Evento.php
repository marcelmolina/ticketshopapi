<?php

/**
 * Created by Reliese Model.
 * Date: Tue, 16 Oct 2018 19:32:04 +0000.
 */

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class Evento
 * 
 * @property int $id
 * @property \Carbon\Carbon $fecha_evento
 * @property string $nombre
 * @property \Carbon\Carbon $hora_inicio
 * @property \Carbon\Carbon $hora_apertura
 * @property \Carbon\Carbon $hora_finalizacion
 * @property string $codigo_pulep
 * @property bool $tipo_evento
 * @property int $domicilios
 * @property int $venta_linea
 * @property int $id_auditorio
 * @property int $id_cliente
 * @property int $id_temporada
 * @property int $status
 * @property \Carbon\Carbon $fecha_inicio_venta
 * 
 * @property \App\Models\Auditorio $auditorio
 * @property \App\Models\Cliente $cliente
 * @property \App\Models\Temporada $temporada
 * @property \Illuminate\Database\Eloquent\Collection $artists
 * @property \Illuminate\Database\Eloquent\Collection $boleta_eventos
 * @property \Illuminate\Database\Eloquent\Collection $descuento_eventos
 * @property \Illuminate\Database\Eloquent\Collection $detalle_venta_temporadas
 * @property \Illuminate\Database\Eloquent\Collection $cuponeras
 * @property \Illuminate\Database\Eloquent\Collection $imagens
 * @property \Illuminate\Database\Eloquent\Collection $palcos
 * @property \Illuminate\Database\Eloquent\Collection $preventa
 * @property \Illuminate\Database\Eloquent\Collection $puntoventa_eventos
 *
 * @package App\Models
 */
class Evento extends Eloquent
{
	protected $table = 'evento';
	public $timestamps = false;

	protected $casts = [
		'tipo_evento' => 'bool',
		'domicilios' => 'int',
		'venta_linea' => 'int',
		'id_auditorio' => 'int',
		'id_cliente' => 'int',
		'id_temporada' => 'int',
		'status' => 'int'
	];

	protected $dates = [
		'fecha_evento',
		'hora_inicio',
		'hora_apertura',
		'hora_finalizacion',
		'fecha_inicio_venta'
	];

	protected $fillable = [
		'fecha_evento',
		'nombre',
		'hora_inicio',
		'hora_apertura',
		'hora_finalizacion',
		'codigo_pulep',
		'tipo_evento',
		'domicilios',
		'venta_linea',
		'id_auditorio',
		'id_cliente',
		'id_temporada',
		'status',
		'fecha_inicio_venta'
	];

	public function auditorio()
	{
		return $this->belongsTo(\App\Models\Auditorio::class, 'id_auditorio');
	}

	public function cliente()
	{
		return $this->belongsTo(\App\Models\Cliente::class, 'id_cliente');
	}

	public function temporada()
	{
		return $this->belongsTo(\App\Models\Temporada::class, 'id_temporada');
	}

	public function artists()
	{
		return $this->belongsToMany(\App\Models\Artist::class, 'artista_evento', 'id_evento', 'id_artista');
	}

	public function boleta_eventos()
	{
		return $this->hasMany(\App\Models\BoletaEvento::class, 'id_evento');
	}

	public function descuento_eventos()
	{
		return $this->hasMany(\App\Models\DescuentoEvento::class, 'id_evento');
	}

	public function detalle_venta_temporadas()
	{
		return $this->hasMany(\App\Models\DetalleVentaTemporada::class, 'id_evento');
	}

	public function cuponeras()
	{
		return $this->belongsToMany(\App\Models\Cuponera::class, 'evento_cuponera', 'id_evento', 'id_cuponera');
	}

	public function imagens()
	{
		return $this->belongsToMany(\App\Models\Imagen::class, 'imagen_evento', 'id_evento', 'id_imagen');
	}

	public function palcos()
	{
		return $this->belongsToMany(\App\Models\Palco::class, 'palco_evento', 'id_evento', 'id_palco')
					->withPivot('id', 'precio_venta', 'precio_servicio', 'impuesto', 'status');
	}

	public function preventa()
	{
		return $this->hasMany(\App\Models\Preventum::class, 'id_evento');
	}

	public function puntoventa_eventos()
	{
		return $this->hasMany(\App\Models\PuntoventaEvento::class, 'id_evento');
	}
}
