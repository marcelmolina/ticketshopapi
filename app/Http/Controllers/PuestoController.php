<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Puesto;
use App\Models\Localidad;
use App\Models\Fila;
use Illuminate\Support\Facades\Input;
use Validator;
/**
 * @group Administración de Puesto
 *
 * APIs para la gestion de la tabla puesto
 */
class PuestoController extends BaseController
{
    /**
     * Lista de la tabla puesto.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $puesto = Puesto::paginate(15);
        return $this->sendResponse($puesto->toArray(), 'Puestos devueltos con éxito');
    }


     /**
     * Listado detallado de los puestos.
     *
     * @return \Illuminate\Http\Response
     */
    public function listado_detalle_puestos()
    {       
        
        $puesto = Puesto::with('localidad')                 
                  ->with('fila')
                  ->with('palcos')               
                  ->paginate(15);
        
        return $this->sendResponse($puesto, 'Puestos devueltos con éxito');
    }


    /**
     * Buscar Puestos por numero.
     *@bodyParam numero string Numero del puesto.
     *@response{
     *    "numero" : "1",
     * }
     * @return \Illuminate\Http\Response
     */
    public function buscarPuestos(Request $request)
    {
       
       $input = $request->all();
       
       if(isset($input["numero"]) && $input["numero"] != null){
            
            $input = $request->all();
            $puesto = Puesto::with('localidad')
                ->with('fila')
                ->where('puesto.numero','like', '%'.strtolower($input["numero"]).'%')
                ->get();
            return $this->sendResponse($puesto->toArray(), 'Todos los Puestos filtrados');
       }else{
            
            $puesto = Puesto::with('localidad')
                ->with('fila')
                ->get();
            return $this->sendResponse($puesto->toArray(), 'Todos los Puestos devueltos'); 
       }

        
    }


    /**
     * Agrega un nuevo elemento a la tabla puesto
     *@bodyParam numero string Numero del puesto.
     *@bodyParam id_localidad int required Id de la localidad.
     *@bodyParam id_fila int Id de la fila.
     * @response {      
     *  "numero": "AA1", 
     *  "id_localidad":1,
     *  "id_fila": 1     
     * }
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
       
        $validator = Validator::make($request->all(), [
            'id_localidad' => 'required',      
        ]);
        if($validator->fails()){
            return $this->sendError('Error de validación.', $validator->errors());       
        }
        
        $localidad = Localidad::find($request->input('id_localidad'));
        if (is_null($localidad)) {
            return $this->sendError('La localidad indicada no existe');
        }

        if($request->input('id_fila') != null){

            $fila = Fila::find($request->input('id_fila'));
            if (is_null($fila)) {
                return $this->sendError('La Fila indicada no existe');
            }

        }

        $puesto = Puesto::create($request->all());        
        return $this->sendResponse($puesto->toArray(), 'Puesto creado con éxito');
    }

    /**
     * Lista de una puesto en especifico 
     *
     * [Se filtra por el ID]
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        
        $puesto = Puesto::find($id);

        if (is_null($puesto)) {
            return $this->sendError('Puesto no encontrado');
        }

        return $this->sendResponse($puesto->toArray(), 'Puesto devuelto con éxito');
    }

    /**
     * Actualiza un elemeto de la tabla puesto 
     *
     * [Se filtra por el ID]
     *
     *@bodyParam numero string Numero del puesto.
     *@bodyParam id_localidad int required Id de la localidad.
     *@bodyParam id_fila int Id de la fila.
     *
     * @response {
     *  "numero": "BB1", 
     *  "id_localidad":1,
     *  "id_fila": 2    
     * }
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $input = $request->all();

        $validator = Validator::make($input, [
            'id_localidad' => 'required',           
        ]);

        if($validator->fails()){
            return $this->sendError('Error de validación', $validator->errors());       
        }

        $puesto_search = Puesto::find($id);        
        if (is_null($puesto_search)) {
            return $this->sendError('Puesto no encontrado');
        }


        $localidad = Localidad::find($request->input('id_localidad'));
        if (is_null($localidad)) {
            return $this->sendError('La localidad indicada no existe');
        }

        if($request->input('id_fila') != null){
            $fila = Fila::find($request->input('id_fila'));
            if (is_null($fila)) {
                return $this->sendError('La fila indicada no existe');
            }else{
                $puesto_search->id_fila = $input['id_fila']; 
            }             
        }
        
        $puesto_search->numero = $input['numero']; 
        $puesto_search->id_localidad = $input['id_localidad'];                    
        $puesto_search->save();

        return $this->sendResponse($puesto_search->toArray(), 'Puesto actualizado con éxito');
    }
    
    /**
     * Elimina un elemento de la tabla puesto
     *
     * [Se filtra por el ID]
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try { 

            $puesto = Puesto::find($id);
            if (is_null($puesto)) {
                return $this->sendError('Puesto no encontrado');
            }
            $puesto->delete();
            return $this->sendResponse($puesto->toArray(), 'Puesto eliminada con éxito');

        }catch (\Illuminate\Database\QueryException $e){
            return response()->json(['error' => 'El Puesto no se puedo eliminar, es usado en otra tabla', 'exception' => $e->errorInfo], 400);
        }
    }
}
