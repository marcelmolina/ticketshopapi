<?php

namespace App\Http\Controllers;

use App\Models\BoletasPreimpresa;
use App\Models\BoletaEvento;
use App\Models\PuntoventaEvento;
use App\Models\Preventum;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Validator;


/**
 * @group Administración de Boletas PreImpresas
 *
 * APIs para la gestion de las boletas pre-impresas
 */
class BoletasPreimpresaController extends BaseController
{
    
    public function __construct()
    {
        $this->middleware('auth:api', ['only' => ['store', 'update', 'destroy']]);        
    }


     /**
     * Lista de las boletas preimpresas paginadas.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $boletaspre = BoletasPreimpresa::with('boleta_evento', 'puntoventa_evento', 'preventum')->paginate(15);
        return $this->sendResponse($boletaspre->toArray(), 'Boletas preimpresas devueltas con éxito');
    }

    /**
     * Lista de todas las boletas preimpresas.
     *
     * @return \Illuminate\Http\Response
     */
    public function boletaspreimpresas_all()
    {
        
        $boletaspre = BoletasPreimpresa::with('boleta_evento', 'puntoventa_evento', 'preventum')->get();
        return $this->sendResponse($boletaspre->toArray(), 'Boletas preimpresas devueltas con éxito');
    }
    

    /**
     * Agrega un nuevo elemento a la tabla BoletapreImpresa.
     *@bodyParam id_boleta int required ID de la boleta evento.
     *@bodyParam id_puntoventa int required ID del PuntoVenta relacionado con el evento (Tabla PuntoventaEvento).
     *@bodyParam id_preventa int Id de la preventa.
     *@bodyParam status int Estatdo de la boleta preImpresa.
     *@response{
     *    "id_boleta" : 1,
     *    "id_puntoventa" : 1,
     *    "id_preventa" : 2,
     *    "status" : 0,
     * }
      * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_boleta' => 'required|integer',            
            'id_puntoventa' => 'required|integer',
            'id_preventa' => 'nullable|integer',
            'status' => 'nullable|integer'
            
        ]);
        if($validator->fails()){
            return $this->sendError('Error de validación.', $validator->errors());       
        }
        $boletaevento = BoletaEvento::find($request->input('id_boleta'));
        if (is_null($boletaevento)) {
            return $this->sendError('La boleta_evento indicada no existe');
        }

        $puntoventa = PuntoventaEvento::where('id_puntoventa',$request->input('id_puntoventa'))->get();
        if (sizeof($puntoventa) == 0) {
            return $this->sendError('El puntoventa_evento indicado no esta relacionado con ningun evento.');
        }

        if(!is_null($request->input('id_preventa'))){
            $preventa = Preventum::find($request->input('id_preventa'));
            if (is_null($preventa)) {
                return $this->sendError('La preventa indicada no existe');
            }
        }


        $boletapre = new BoletasPreimpresa();
        $boletapre->id_boleta = $request->input('id_boleta');
        $boletapre->id_puntoventa = $request->input('id_puntoventa');
        $boletapre->id_preventa = $request->input('id_preventa');
        $boletapre->status = $request->input('status'); 
         $boletapre->save();

        return $this->sendResponse($boletapre->toArray(), 'Boleta preimpresa registrada con éxito');


    }

    /**
     * Lista una boleta preimpresa en especifico 
     *
     * [Se filtra por el ID de la BoletaEvento]
     *
     * @param  \App\Models\BoletasPreimpresa  $id_boleta
     * @return \Illuminate\Http\Response
     */
    public function show($id_boleta)
    {

        $boletaevento = BoletaEvento::find($id_boleta);
        if (is_null($boletaevento)) {
            return $this->sendError('La boleta_evento indicada no existe');
        }

        $boletaspre = BoletasPreimpresa::with('boleta_evento', 'puntoventa_evento', 'preventum')->where('id_boleta', $id_boleta)->get();
        return $this->sendResponse($boletaspre->toArray(), 'Boletas preimpresas devueltas con éxito');
    }

    /**
     * Actualiza un elemento a la tabla BoletapreImpresa.
     * [Se filtra por el ID de la BoletaEvento]
     *
     *@bodyParam id_puntoventa int required ID del PuntoVenta relacionado con el evento (Tabla PuntoventaEvento).
     *@bodyParam id_preventa int Id de la preventa.
     *@bodyParam status int Estatdo de la boleta preImpresa.
     *@response{
     *    "id_puntoventa" : 1,
     *    "id_preventa" : 2,
     *    "status" : 0,
     * }
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\BoletasPreimpresa  $id_boleta
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id_boleta)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'id_puntoventa' => 'required|integer',
            'id_preventa' => 'nullable|integer',
            'status' => 'nullable|integer'
            
        ]);
        if($validator->fails()){
            return $this->sendError('Error de validación.', $validator->errors());       
        }

        $boletaspre = BoletasPreimpresa::where('id_boleta', $id_boleta)->get();
        if (sizeof($boletaspre) == 0) {
            return $this->sendError('Boleta preimpresa no encontrada');
        }


        $puntoventa = PuntoventaEvento::where('id_puntoventa',$input['id_puntoventa'])->get();
        if (sizeof($puntoventa) == 0) {
            return $this->sendError('El puntoventa_evento indicado no esta relacionado con ningun evento.');
        }

        if(!is_null($input['id_preventa'])){
            $preventa = Preventum::find($input['id_preventa']);
            if (is_null($preventa)) {
                return $this->sendError('La preventa indicada no existe');
            }
        }


        BoletasPreimpresa::where('id_boleta', $id_boleta)
                        ->update(['id_puntoventa' => $input['id_puntoventa'], 
                                  'id_preventa' => $input['id_preventa'],
                                  'status' => $input['status'],
                                 ]);


        $boletapre = BoletasPreimpresa::with('boleta_evento', 'puntoventa_evento', 'preventum')->where('id_boleta', $id_boleta)->get();
        return $this->sendResponse($boletapre->toArray(), 'Boletas preimpresa actualizada con éxito');

    }

   
    /**
     * Elimina todas las boletaspreimpresas
     *
     * [Se filtra por el ID de la boleta Evento]
     *
     * @param  \App\Models\BoletasPreimpresa  $boletasPreimpresa
     * @return \Illuminate\Http\Response
     */
    public function destroy($id_boleta)
    {
        try {
            $boletaspre = BoletasPreimpresa::where('id_boleta', $id_boleta)->get();
            if (sizeof($boletaspre) == 0) {
                return $this->sendError('BoletasPreimpresa no encontradas');
            }
            BoletasPreimpresa::where('id_boleta', $id_boleta)->delete();
            return $this->sendResponse($boletaspre->toArray(), 'Boletas preimpresas eliminadas con éxito');

        }catch (\Illuminate\Database\QueryException $e){
            return response()->json(['error' => 'Las boletas preimpresas no se pueden eliminar', 'exception' => $e->errorInfo], 400);
        }
    }
}
