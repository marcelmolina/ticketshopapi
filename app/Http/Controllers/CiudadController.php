<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Input;
use App\Models\Ciudad;
use App\Models\Departamento;
use Illuminate\Http\Request;
use Validator;

/**
 * @group Administración de Ciudad
 *
 * APIs para la gestion de la tabla ciudad
 */
class CiudadController extends BaseController
{
    /**
     * Lista de la tabla ciudades.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
         $ciudad = Ciudad::paginate(15);

        return $this->sendResponse($ciudad->toArray(), 'Ciudades devueltas con éxito');
    }

    /**
     * Buscar Ciudades por descripcion.
     *@bodyParam nombre string Descripcion de la ciudad.
     *@response{
     *    "nombre" : "Ciudad",
     * }
     * @return \Illuminate\Http\Response
     */
    public function buscarCiudad(Request $request)
    {
       
       $input = $request->all();
       
       if(isset($input["nombre"]) && $input["nombre"] != null){
            
            $input = $request->all();
            $ciudad = Ciudad::with('departamento')
                ->where('ciudad.descripcion','like', '%'.strtolower($input["nombre"]).'%')
                ->get();
            return $this->sendResponse($ciudad->toArray(), 'Todos las Ciudades filtradas');
       }else{
            
            $ciudad = Ciudad::with('departamento')->get();
            return $this->sendResponse($ciudad->toArray(), 'Todos las Ciudades devueltas'); 
       }
        
    }


    /**
     * Agrega un nuevo elemento a la tabla ciudad
     *@bodyParam id_departamento int required ID del departamento.
     *@bodyParam descripcion string required Descripcion de la ciudad.
     * @response {
     *	"id_departamento": 1
     *  "descripcion": "Ciudad New"
     * }
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        $validator = Validator::make($request->all(), [
            'id_departamento' => 'required|integer',   
            'descripcion' => 'required',            
        ]);
        if($validator->fails()){
            return $this->sendError('Error de validación.', $validator->errors());       
        }

        $departamento = Departamento::find($request->input('id_departamento'));
        if (is_null($departamento)) {
            return $this->sendError('El Departamento indicado no existe');
        }
        
        $ciudad = Ciudad::create($request->all());        
        return $this->sendResponse($ciudad->toArray(), 'Ciudad creada con éxito');
    }


    /**
     * Lista de una ciudad en especifico 
     *
     * [Se filtra por el ID]
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        
        $ciudad = Ciudad::find($id);
        if (is_null($ciudad)) {
            return $this->sendError('Ciudad no encontrado');
        }
        return $this->sendResponse($ciudad->toArray(), 'Ciudad devuelta con éxito');
    }


    /**
     * Actualiza un elemento a la tabla ciudad
     *@bodyParam id_departamento int required ID del departamento.
     *@bodyParam descripcion string required Descripcion de la ciudad.
     *@response {
     *	"id_departamento": 2
     *  "descripcion": "Departamento New 1"
     * }
     * [Se filtra por el ID]
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $input = $request->all();
        $validator = Validator::make($request->all(), [
            'id_departamento' => 'required|integer',            
            'descripcion' => 'required',            
        ]);
        if($validator->fails()){
            return $this->sendError('Error de validación', $validator->errors());       
        }        

        $ciudad_search = Ciudad::find($id);        
        if (is_null($ciudad_search)) {
            return $this->sendError('Ciudad no encontrada');
        }

        $departamento = Departamento::find($request->input('id_departamento'));
        if (is_null($departamento)) {
            return $this->sendError('El Departamento indicado no existe');
        }

        $ciudad_search->id_departamento = $input['id_departamento'];
        $ciudad_search->descripcion = $input['descripcion'];
        $ciudad_search->save();

        return $this->sendResponse($ciudad_search->toArray(), 'Ciudad actualizada con éxito');

    }


    /**
     * Elimina un elemento de la tabla ciudad
     *
     * [Se filtra por el ID]
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {        

        try {
            $ciudad = Ciudad::find($id);
            if (is_null($ciudad)) {
                return $this->sendError('Ciudad no encontrada');
            }
            $ciudad->delete();
            return $this->sendResponse($ciudad->toArray(), 'Ciudad eliminada con éxito');

        }catch (\Illuminate\Database\QueryException $e){
            return response()->json(['error' => 'La ciudad no se puedo eliminar, es usado en otra tabla', 'exception' => $e->errorInfo], 400);
        }
        
    }

}
