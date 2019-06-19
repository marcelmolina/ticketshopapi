<?php

/**
 * Created by Reliese Model.
 * Date: Tue, 16 Oct 2018 19:32:04 +0000.
 */

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class ImagenArtist
 * 
 * @property int $id_artista
 * @property int $id_imagen
 * 
 * @property \App\Models\Artist $artist
 * @property \App\Models\Imagen $imagen
 *
 * @package App\Models
 */
class ImagenArtist extends Eloquent
{
	protected $table = 'imagen_artist';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'id_artista' => 'int',
		'id_imagen' => 'int'
	];

	protected $fillable = [
		'id_artista',
		'id_imagen'
	];

	public function artist()
	{
		return $this->belongsTo(\App\Models\Artist::class, 'id_artista');
	}

	public function imagen()
	{
		return $this->belongsTo(\App\Models\Imagen::class, 'id_imagen');
	}
}
