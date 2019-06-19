<?php

/**
 * Created by Reliese Model.
 * Date: Tue, 16 Oct 2018 19:32:04 +0000.
 */

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class ImagenesAuditorio
 * 
 * @property int $id_imagen
 * @property int $id_auditorio
 * 
 * @property \App\Models\Imagen $imagen
 * @property \App\Models\Auditorio $auditorio
 *
 * @package App\Models
 */
class ImagenesAuditorio extends Eloquent
{
	protected $table = 'imagenes_auditorio';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'id_imagen' => 'int',
		'id_auditorio' => 'int'
	];

	protected $fillable = [
		'id_imagen',
		'id_auditorio'
	];

	public function imagen()
	{
		return $this->belongsTo(\App\Models\Imagen::class, 'id_imagen');
	}

	public function auditorio()
	{
		return $this->belongsTo(\App\Models\Auditorio::class, 'id_auditorio');
	}
}
