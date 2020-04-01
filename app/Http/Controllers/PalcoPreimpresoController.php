<?php

namespace App\Http\Controllers;

use App\Models\PalcoPreimpreso;
use App\Models\PalcoEvento;
use App\Models\PuntoventaEvento;
use App\Models\Preventum;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Validator;

/**
 * @group Administración de Palcos PreImpresas
 *
 * APIs para la gestion de las palcos pre-impresos
 */
class PalcoPreimpresoController extends BaseController
{
    
    
    public function __construct()
    {
        $this->middleware('auth:api', ['only' => ['store', 'update', 'destroy']]);        
    }


    /**
     * Lista de los palcos preimpresos paginados.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $palcospre = PalcoPreimpreso::with('palco_evento', 'puntoventa_evento', 'preventa_palco')->paginate(15);
        return $this->sendResponse($palcospre->toArray(), 'Palcos preimpresos devueltos con éxito');
    }


    /**
     * Lista de todos los palcos preimpresos.
     *
     * @return \Illuminate\Http\Response
     */
    public function palcospreimpresos_all()
    {
        
        $palcospre = PalcoPreimpreso::with('palco_evento', 'puntoventa_evento', 'preventa_palco')->get();
        return $this->sendResponse($palcospre->toArray(), 'Palcos preimpresos devueltos con éxito');
    }

    

    /**
     * Agrega un nuevo elemento a la tabla PalcopreImpreso.
     *@bodyParam id_palco_evento int required ID del palco evento.
     *@bodyParam id_puntoventa int required ID del PuntoVenta relacionado con el evento (Tabla PuntoventaEvento).
     *@bodyParam id_preventa int Id de la preventa.
     *@bodyParam status int Estatdo de la boleta preImpresa.
     *@response{
     *    "id_palco_evento" : 1,
     *    "id_puntoventa" : 1,
     *    "id_preventa" : null,
     *    "status" : 0,
     * }
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_palco_evento' => 'required|integer',            
            'id_puntoventa' => 'required|integer',
            'id_preventa' => 'nullable|integer',
            'status' => 'nullable|integer'
            
        ]);
        if($validator->fails()){
            return $this->sendError('Error de validación.', $validator->errors());       
        }
        $palcoevento = PalcoEvento::find($request->input('id_palco_evento'));
        if (is_null($palcoevento)) {
            return $this->sendError('El palco_evento indicado no existe');
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


        $palcopre = new PalcoPreimpreso();
        $palcopre->id_palco_evento = $request->input('id_palco_evento');
        $palcopre->id_puntoventa = $request->input('id_puntoventa');
        $palcopre->id_preventa = $request->input('id_preventa');
        $palcopre->status = $request->input('status');
        $palcopre->save();

        return $this->sendResponse($palcopre->toArray(), 'Palco preimpreso registrado con éxito');

    }

    /**
     * Lista un palco preimpreso en especifico 
     *
     * [Se filtra por el ID de la PalcoEvento]
     *
     * @param  \App\Models\PalcoPreimpreso  $id_palco_evento
     * @return \Illuminate\Http\Response
     */
    public function show($id_palco_evento)
    {
        $palcoevento = PalcoEvento::find($id_palco_evento);
        if (is_null($palcoevento)) {
            return $this->sendError('El palco_evento indicado no existe');
        }

        $palcopre = PalcoPreimpreso::with('palco_evento', 'puntoventa_evento', 'preventa_palco')->where('id_palco_evento', $id_palco_evento)->get();
        return $this->sendResponse($palcopre->toArray(), 'Palco preimpreso devuelto con éxito');
    }

    

   /**
     * Actualiza un elemento a la tabla PalcoPreImpreso.
     * [Se filtra por el ID del PalcoEvento]
     *
     *@bodyParam id_puntoventa int required ID del PuntoVenta relacionado con el evento (Tabla PuntoventaEvento).
     *@bodyParam id_preventa int Id de la preventa.
     *@bodyParam status int Estatdo de la boleta preImpresa.
     *@response{
     *    "id_palco_evento" : 1,
     *    "id_puntoventa" : 1,
     *    "id_preventa" : null,
     *    "status" : 0,
     * }
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\PalcoPreimpreso  $palcoPreimpreso
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id_palco_evento)
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

        $palcospre = PalcoPreimpreso::where('id_palco_evento', $id_palco_evento)->get();
        if (sizeof($palcospre) == 0) {
            return $this->sendError('Palco preimpreso no encontrado');
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

        PalcoPreimpreso::where('id_palco_evento', $id_palco_evento)
                        ->update(['id_puntoventa' => $input['id_puntoventa'], 
                                  'id_preventa' => $input['id_preventa'],
                                  'status' => $input['status'],
                                 ]);


        $palcopre = PalcoPreimpreso::with('palco_evento', 'puntoventa_evento', 'preventa_palco')->where('id_palco_evento', $id_palco_evento)->get();
        return $this->sendResponse($palcopre->toArray(), 'Palco preimpreso actualizado con éxito');
    }




    /**
     * Elimina todas las palcos preimpresos
     *
     * [Se filtra por el ID del palco Evento]
     *
     * @param  \App\Models\PalcoPreimpreso  $palcoPreimpreso
     * @return \Illuminate\Http\Response
     */
    public function destroy($id_palco_evento)
    {
         try {
            $palcospre = PalcoPreimpreso::where('id_palco_evento', $id_palco_evento)->get();
            if (sizeof($palcospre) == 0) {
                return $this->sendError('Palcos preimpresos no encontrados');
            }
            PalcoPreimpreso::where('id_palco_evento', $id_palco_evento)->delete();
            return $this->sendResponse($palcospre->toArray(), 'Palcos preimpresos eliminados con éxito');

        }catch (\Illuminate\Database\QueryException $e){
            return response()->json(['error' => 'Los palcos preimpresos no se pueden eliminar', 'exception' => $e->errorInfo], 400);
        }
    }
}
