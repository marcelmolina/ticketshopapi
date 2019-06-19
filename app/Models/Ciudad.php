<?php

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;
/**
 * Class Ciudad
 * 
 * @property int $id
 * @property int $id_departamento
 * @property string $descripcion
 * 
 * @property \Illuminate\Database\Eloquent\Collection $departamento
 * @package App\Models
 */
class Ciudad extends Eloquent
{
    protected $table = 'ciudad';
	public $timestamps = false;

	protected $fillable = [
		'id_departamento', 'descripcion'
	];


	public function departamento()
	{
		return $this->belongsTo(\App\Models\Departamento::class, 'id_departamento');
	}

}
