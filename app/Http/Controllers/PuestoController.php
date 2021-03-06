<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Puesto;
use App\Models\Localidad;
use App\Models\Auditorio;
use App\Models\Palco;
use App\Models\PuestosPalco;
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
    
    public function __construct()
    {
        $this->middleware('auth:api', ['only' => ['store', 'update', 'destroy']]);        
    }

    /**
     * Lista de la tabla puesto paginada.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $puesto = Puesto::with('localidad')                 
                  ->with('fila')
                  ->with('palcos')               
                  ->paginate(15);
        return $this->sendResponse($puesto->toArray(), 'Puestos devueltos con éxito');
    }


     /**
     * Listado de los puestos.
     *
     * @return \Illuminate\Http\Response
     */
    public function puesto_all()
    {       
        
        $puesto = Puesto::with('localidad')                 
                  ->with('fila')
                  ->with('palcos')               
                  ->get();
        
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
     * Buscar Puestos por fila.
     * [Se filtra por ID de la fila]
     *
     * @return \Illuminate\Http\Response
     */
    public function puestos_fila($id_fila)
    {
       
        $fila = Fila::find($id_fila);

        if (!$fila) {
            return $this->sendError('Fila no encontrada');
        }       
            
        $puesto = Puesto::with('localidad')
            ->with('fila')
            ->where('id_fila', $id_fila)
            ->get();
        return $this->sendResponse($puesto->toArray(), 'Todos los Puestos filtrados por fila');
        
    }

    /**
     * Todos los Puestos por auditorio.
     * [Se filtra por ID del auditorio]
     *
     * @return \Illuminate\Http\Response
     */
    public function puestos_auditorio($id_auditorio)
    {


        $auditorio = Auditorio::find($id_auditorio);

        if (is_null($auditorio)) {
            return $this->sendError('Auditorio no encontrado');
        }

    $puestos = Puesto::wherehas('localidad.tribuna',function($query) use($id_auditorio){
                            $query->where('id_auditorio', $id_auditorio);
                        })
                    ->with(['localidad.tribuna' => function($query) use($id_auditorio){
                            $query->where('id_auditorio', $id_auditorio);
                        }])
                ->get();

        if(is_null($puestos) || sizeof($puestos) == 0){
            return $this->sendError('No hay puestos registrados para el auditorio');
        }
             
        return $this->sendResponse($puestos->toArray(), 'Todos los Puestos filtrados por auditorio');
        
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
     * Agrega un nuevo elemento a la tabla puesto
     *@bodyParam numeros array-string cantidad de puestos por fila.
     *@bodyParam id_localidad int required Id de la localidad.
     *@bodyParam id_fila int Id de la fila.
     * @response {      
     *  "numeros": ["AA1"], 
     *  "id_localidad":1,
     *  "id_fila": 1     
     * }
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storexfila(Request $request)
    {
        if (count($request->numeros)>0) {

            $localidad = Localidad::find($request->input('id_localidad'));
            if (is_null($localidad)) {
                return $this->sendError('La localidad indicada no existe');
            }

            foreach ($request->numeros as $key => $numero) {
                $puesto = Puesto::create([
                    'numero'       => $numero,
                    'id_localidad' => $localidad->id,
                    'id_fila'      => $request->id_fila
                ]);

                if ($localidad->palco == 1 && (($key%$localidad->puestosxpalco)==0 || $key==0)) {
                    $palco = Palco::create([
                        'nombre'       => '',
                        'id_localidad' => $localidad->id
                    ]);
                }

                if ($localidad->palco == 1) {
                    $puestopalco = PuestosPalco::create([
                        'id_palco'  => $palco->id,
                        'id_puesto' => $puesto->id
                    ]);
                }
            }
        } else {
            $this->store($request);
        }
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

