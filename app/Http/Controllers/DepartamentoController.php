<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Input;
use App\Models\Departamento;
use App\Models\Pais;
use Illuminate\Http\Request;
use Validator;


/**
 * @group Administración de Departamento
 *
 * APIs para la gestion de la tabla departamento
 */
class DepartamentoController extends BaseController
{
    /**
     * Lista de la tabla departamento.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
         $departamento = Departamento::paginate(15);

        return $this->sendResponse($departamento->toArray(), 'Departamentos devueltos con éxito');
    }


    /**
     * Buscar Departamentos por descripcion.
     *@bodyParam nombre string Descripcion del departamento.
     *@response{
     *    "nombre" : "Departamento",
     * }
     * @return \Illuminate\Http\Response
     */
    public function buscarDepartamento(Request $request)
    {
       
       $input = $request->all();
       
       if(isset($input["nombre"]) && $input["nombre"] != null){
            
            $input = $request->all();
            $departamento = Departamento::with('pais')
                ->where('departamento.descripcion','like', '%'.strtolower($input["nombre"]).'%')
                ->get();
            return $this->sendResponse($departamento->toArray(), 'Todos los Departamento filtrados');
       }else{
            
            $departamento = Departamento::with('pais')->get();
            return $this->sendResponse($departamento->toArray(), 'Todos los Departamentos devueltos'); 
       }

        
    }


    /**
     * Agrega un nuevo elemento a la tabla departamento
     *@bodyParam id_pais int required ID del pais.
     *@bodyParam descripcion string required Descripcion del departamento.
     * @response {
     	"id_pais": 1
     *  "descripcion": "Departamento New"
     * }
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        $validator = Validator::make($request->all(), [
            'id_pais' => 'required|integer',            
            'descripcion' => 'required',            
        ]);
        if($validator->fails()){
            return $this->sendError('Error de validación.', $validator->errors());       
        }

        $pais = Pais::find($request->input('id_pais'));
        if (is_null($pais)) {
            return $this->sendError('El Pais indicado no existe');
        }
        
        $departamento = Departamento::create($request->all());        
        return $this->sendResponse($departamento->toArray(), 'Departamento creado con éxito');
    }


    /**
     * Lista de un departamento en especifico 
     *
     * [Se filtra por el ID]
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        
        $departamento = Departamento::find($id);
        if (is_null($departamento)) {
            return $this->sendError('Departamento no encontrado');
        }
        return $this->sendResponse($departamento->toArray(), 'Departamento devuelto con éxito');
    }


    /**
     * Actualiza un elemento a la tabla departamento
     *@bodyParam id_pais int required ID del pais.
     *@bodyParam descripcion string required Descripcion del departamento.
     *@response {
     *	"id_pais": 2
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
            'id_pais' => 'required|integer',            
            'descripcion' => 'required',            
        ]);
        if($validator->fails()){
            return $this->sendError('Error de validación', $validator->errors());       
        }
        

        $depart_search = Departamento::find($id);        
        if (is_null($depart_search)) {
            return $this->sendError('Departamento no encontrado');
        }

        $pais = Pais::find($request->input('id_pais'));
        if (is_null($pais)) {
            return $this->sendError('El Pais indicado no existe');
        }

        $depart_search->id_pais = $input['id_pais'];
        $depart_search->descripcion = $input['descripcion'];
        $depart_search->save();

        return $this->sendResponse($depart_search->toArray(), 'Departamento actualizado con éxito');

    }


    /**
     * Elimina un elemento de la tabla departamento
     *
     * [Se filtra por el ID]
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {        

        try {
            $departamento = Departamento::find($id);
            if (is_null($departamento)) {
                return $this->sendError('Departamento no encontrado');
            }
            $departamento->delete();
            return $this->sendResponse($departamento->toArray(), 'Departamento eliminado con éxito');

        }catch (\Illuminate\Database\QueryException $e){
            return response()->json(['error' => 'El departamento no se puedo eliminar, es usado en otra tabla', 'exception' => $e->errorInfo], 400);
        }
        
    }

}
