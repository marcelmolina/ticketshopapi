<?php

/**
 * Created by Reliese Model.
 * Date: Tue, 16 Oct 2018 19:32:03 +0000.
 */

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class Artist
 * 
 * @property int $id
 * @property string $nombre
 * @property string $manager
 * @property int $id_genero
 * 
 * @property \App\Models\Genero $genero
 * @property \Illuminate\Database\Eloquent\Collection $eventos
 * @property \Illuminate\Database\Eloquent\Collection $imagens
 *
 * @package App\Models
 */
class Artist extends Eloquent
{
	protected $table = 'artist';
	public $timestamps = false;

	protected $casts = [
		'id_genero' => 'int'
	];

	protected $fillable = [
		'nombre',
		'manager',
		'id_genero'
	];

	public function genero()
	{
		return $this->belongsTo(\App\Models\Genero::class, 'id_genero');
	}

	public function eventos()
	{
		return $this->belongsToMany(\App\Models\Evento::class, 'artista_evento', 'id_artista', 'id_evento');
	}

	public function imagens()
	{
		return $this->belongsToMany(\App\Models\Imagen::class, 'imagen_artist', 'id_artista', 'id_imagen');
	}
}
