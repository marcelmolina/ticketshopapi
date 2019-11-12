<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use App\Models\Palco;
use App\Models\Localidad;
use App\Models\Tribuna;
use Illuminate\Support\Facades\Input;
/**
 * @group Administración de Palco
 *
 * APIs para la gestion de la tabla palco
 */
class PalcoController extends BaseController
{
    


    public function __construct()
    {
        $this->middleware('auth:api', ['only' => ['store', 'update', 'destroy']]);        
    }

    /**
     * Lista de la tabla palco paginada.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $palco = Palco::with('localidad')                 
                  ->with('puestos')                 
                  ->paginate(15);
        
        return $this->sendResponse($palco, 'Palcos devueltos con éxito');
    }


    /**
     * Lista de los palcos.
     *
     * @return \Illuminate\Http\Response
     */
    public function palco_all()
    {
        $palco = Palco::with('localidad')                 
                  ->with('puestos')                 
                  ->get();
        return $this->sendResponse($palco->toArray(), 'Palcos devueltos con éxito');
    }


  
    /**
     * Buscar Palcos por nombre.
     *@bodyParam nombre string Nombre del palco.
     *@response{
     *    "nombre" : "Palco 1",
     * }
     * @return \Illuminate\Http\Response
     */
    public function buscarPalco(Request $request)
    {
       
       $input = $request->all();
       
       if(isset($input["nombre"]) && $input["nombre"] != null){
            
            $input = $request->all();
            $palco = Palco::with('localidad')
                ->with('puestos')
                ->where('palco.nombre','like', '%'.strtolower($input["nombre"]).'%')
                ->get();
            return $this->sendResponse($palco->toArray(), 'Todos los Palcos filtrados');
       }else{
            
            $palco = Palco::with('localidad')
                ->with('puestos')
                ->get();
            return $this->sendResponse($palco->toArray(), 'Todos los Palcos devueltos'); 
       }

        
    }

   
    /**
     * Agrega un nuevo elemento a la tabla palco
     *@bodyParam nombre string Nombre del palco.
     *@bodyParam id_localidad int required Id de la localidad.
     * @response {
     *  "nombre": "Palco New",
     *  "id_localidad": 1           
     * }
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        $validator = Validator::make($request->all(), [                        
            'id_localidad' => 'required',            
        ]);
        if($validator->fails()){
            return $this->sendError('Error de validación.', $validator->errors());       
        }
        
        $localidad = Localidad::find($request->input('id_localidad'));
        if (is_null($localidad)) {
            return $this->sendError('La Localidad indicada no existe');
        }

        $palco = Palco::create($request->all());        
        return $this->sendResponse($palco->toArray(), 'Palco creado con éxito');
    }

    /**
     * Lista de un palco en especifico 
     *
     * [Se filtra por el ID]
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        
        $palco = Palco::with('localidad')->with('puestos')->find($id);

        if (is_null($palco)) {
            return $this->sendError('Palco no encontrado');
        }

        return $this->sendResponse($palco->toArray(), 'Palco devuelto con éxito');
    }


    /**
     * Actualiza un elemeto de la tabla palco 
     *@bodyParam nombre string Nombre del palco.
     *@bodyParam id_localidad int required Id de la localidad.
     * [Se filtra por el ID]
     * @response {
     *  "nombre": "Palco 2",
     *  "id_localidad": 1
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

        $palco_search = Palco::find($id);        
        if (is_null($palco_search)) {
            return $this->sendError('Palco no encontrado');
        }

        $localidad_search = Localidad::find($input['id_localidad']);
        if (is_null($localidad_search)) {
            return $this->sendError('La Localidad indicada no existe');
        }

        $palco_search->nombre = $input['nombre'];
        $palco_search->id_localidad = $input['id_localidad'];         
        $palco_search->save();

        return $this->sendResponse($palco_search->toArray(), 'Palco actualizado con éxito');
    }

    /**
     * Elimina un elemento de la tabla palco
     *
     * [Se filtra por el ID]
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {

            $palco = Palco::find($id);
            if (is_null($palco)) {
                return $this->sendError('Palco no encontrado');
            }
            $palco->delete();
            return $this->sendResponse($palco->toArray(), 'Palco eliminado con éxito');

        }catch (\Illuminate\Database\QueryException $e){
            return response()->json(['error' => 'El palco no se puede eliminar, es usado en otra tabla', 'exception' => $e->errorInfo], 400);
        }
    }
}
