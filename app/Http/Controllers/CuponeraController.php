<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cuponera;
use Validator;
use Illuminate\Support\Facades\Input;
/**
 * @group Administración de Cuponera
 *
 * APIs para la gestion de la cuponera
 */
class CuponeraController extends BaseController
{
    /**
     * Lista de la tabla cuponera paginadas.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $cuponera = Cuponera::paginate(15);
        return $this->sendResponse($cuponera->toArray(), 'Cuponeras devueltas con éxito');
    }


    /**
     * Lista de la tabla de todas las cuponeras.
     *
     * @return \Illuminate\Http\Response
     */
    public function cuponera_all()
    {
        $cuponera = Cuponera::get();
        return $this->sendResponse($cuponera->toArray(), 'Cuponeras devueltas con éxito');
    }


    /**
     * Listado detallado de las cuponeras.
     *
     * @return \Illuminate\Http\Response
     */
    public function listado_detalle_cuponeras()
    {
        
        $cuponera = Cuponera::with('cupons')                
                ->paginate(15);
        $lista_cuponera = compact('cuponera');
        return $this->sendResponse($lista_cuponera, 'Cuponeras devueltas con éxito');
    }


    /**
     * Buscar Cuponera por nombre.
     *@bodyParam nombre string Nombre de la cuponera.
     *@response{
     *    "nombre" : "cuponera 1",
     * }
     * @return \Illuminate\Http\Response
     */
    public function buscarCuponera(Request $request)
    {       
       $input = $request->all();
       
       if(isset($input["nombre"]) && $input["nombre"] != null){
            
            $input = $request->all();
            $cuponera = \DB::table('cuponera')
                ->where('cuponera.nombre','like', '%'.strtolower($input["nombre"]).'%')
                ->select('cuponera.*')
                ->get();
            return $this->sendResponse($cuponera->toArray(), 'Todos las Cuponeras filtradas');
       }else{
            
            $cuponera = Cuponera::get();
            return $this->sendResponse($cuponera->toArray(), 'Todos las Cuponeras devueltas'); 
       }
        
    }


     /**
     * Agrega un nuevo elemento a la tabla cuponera
     *@bodyParam nombre string required Nombre de la cuponera.
     *@bodyParam fecha_inicio date Fecha de inicio  de la cuponera.
     *@bodyParam fecha_fin date Fecha fin de la cuponera.
     *@bodyParam status int Status de la cuponera.        
     *@response{
     *       "nombre" : "Cuponera New",
     *       "fecha_inicio" : null,
     *       "fecha_fin": null,
     *       "status": 0       
     *     }
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
       
        $validator = Validator::make($request->all(), [
            'nombre' => 'required',      
        ]);
        if($validator->fails()){
            return $this->sendError('Error de validación.', $validator->errors());       
        }
        
        if(!is_null($request->input('fecha_inicio'))){
            $validator = Validator::make($request->all(), [
                'fecha_inicio' => 'date',      
            ]);
            if($validator->fails()){
                return $this->sendError('Error de validación.', $validator->errors());       
            }
        }
 
        if(!is_null($request->input('fecha_fin'))){
            $validator = Validator::make($request->all(), [
                'fecha_fin' => 'date',      
            ]);
            if($validator->fails()){
                return $this->sendError('Error de validación.', $validator->errors());       
            }
        }

        if(is_null($request->input('status'))){
            Input::merge(['status' => 0]);
        }

        $cuponera = Cuponera::create($request->all());        
        return $this->sendResponse($cuponera->toArray(), 'Cuponera creada con éxito'); 
    }

    /**
     * Lista una cuponera en especifico 
     *
     * [Se filtra por el ID]
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $cuponera = Cuponera::find($id);

        if (is_null($cuponera)) {
            return $this->sendError('Cuponera no encontrada');
        }
        return $this->sendResponse($cuponera->toArray(), 'Cuponera devuelta con éxito');
    }


     /**
     * Actualiza un elemeto de la tabla cuponera 
     *
     * [Se filtra por el ID]
     *@bodyParam nombre string required Nombre de la cuponera.
     *@bodyParam fecha_inicio date Fecha de inicio  de la cuponera.
     *@bodyParam fecha_fin date Fecha fin de la cuponera.
     *@bodyParam status int Status de la cuponera.        
     *@response{
     *       "nombre" : "Cuponera Basic",
     *       "fecha_inicio" : null,
     *       "fecha_fin": null,
     *       "status": 2       
     *     }
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'nombre' => 'required',           
        ]);
        if($validator->fails()){
            return $this->sendError('Error de validación', $validator->errors());       
        }

        $cuponera_search = Cuponera::find($id);
        if (is_null($cuponera_search)) {
            return $this->sendError('Fila no encontrada');
        } 

        if(!is_null($input['fecha_inicio'])){
            $validator = Validator::make($input, [
                'fecha_inicio' => 'date',      
            ]);
            if($validator->fails()){
                return $this->sendError('Error de validación.', $validator->errors());       
            }
            $cuponera_search->fecha_inicio = $input['fecha_inicio'];
        }

        if(!is_null($input['fecha_fin'])){
            $validator = Validator::make($input, [
                'fecha_fin' => 'date',      
            ]);
            if($validator->fails()){
                return $this->sendError('Error de validación.', $validator->errors());       
            }
            $cuponera_search->fecha_fin = $input['fecha_fin'];
        }

        if(is_null($input['status'])){
            $input['status'] = 0;                      
        }else{
            $validator = Validator::make($input, [
                'status' => 'integer',      
            ]);
            if($validator->fails()){
                return $this->sendError('Error de validación.', $validator->errors());       
            }            
        }   
        
        $cuponera_search->nombre = $input['nombre'];
        $cuponera_search->status = $input['status'];
        $cuponera_search->save();
        return $this->sendResponse($cuponera_search->toArray(), 'Cuponera actualizada con éxito');
    }

     /**
     * Elimina un elemento de la tabla cuponera
     *
     * [Se filtra por el ID]
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try { 

            $cuponera = Cuponera::find($id);
            if (is_null($cuponera)) {
                return $this->sendError('Cuponera no encontrada');
            }
            $cuponera->delete();
            return $this->sendResponse($cuponera->toArray(), 'Cuponera eliminada con éxito');

        }catch (\Illuminate\Database\QueryException $e){
            return response()->json(['error' => 'La Cuponera no se puedo eliminar, es usada en otra tabla', 'exception' => $e->errorInfo], 400);
        }
    }
}
