<?php

/**
 * Created by Reliese Model.
 * Date: Tue, 16 Oct 2018 19:32:04 +0000.
 */

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class Imagen
 * 
 * @property int $id
 * @property string $nombre
 * @property string $url
 * 
 * @property \Illuminate\Database\Eloquent\Collection $artists
 * @property \Illuminate\Database\Eloquent\Collection $eventos
 * @property \Illuminate\Database\Eloquent\Collection $auditorios
 *
 * @package App\Models
 */
class Imagen extends Eloquent
{
	protected $table = 'imagen';
	public $timestamps = false;

	protected $fillable = [
		'nombre',
		'url'
	];

	public function artists()
	{
		return $this->belongsToMany(\App\Models\Artist::class, 'imagen_artist', 'id_imagen', 'id_artista');
	}

	public function eventos()
	{
		return $this->belongsToMany(\App\Models\Evento::class, 'imagen_evento', 'id_imagen', 'id_evento');
	}

	public function auditorios()
	{
		return $this->belongsToMany(\App\Models\Auditorio::class, 'imagenes_auditorio', 'id_imagen', 'id_auditorio');
	}
}
