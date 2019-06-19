<?php

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;
/**
 * Class Departamento
 * 
 * @property int $id
 * @property int $id_pais
 * @property string $descripcion
 * 
 * @property \Illuminate\Database\Eloquent\Collection $pais
 * @package App\Models
 */
class Departamento extends Eloquent
{
    protected $table = 'departamento';
	public $timestamps = false;

	protected $fillable = [
		'id_pais', 'descripcion'
	];

	public function pais()
	{
		return $this->belongsTo(\App\Models\Pais::class, 'id_pais');
	}
}
