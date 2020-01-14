<?php

namespace App\Http\Controllers;

use App\Models\AuditoriaEvento;
use App\Models\Evento;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Validator;
use Carbon\Carbon;

/**
 * @group Administración de Auditoria-Evento
 *
 * APIs para la gestion de la auditoria de aprobación o rechazo del evento
 */
class AuditoriaEventoController extends BaseController
{
    
    public function __construct()
    {
        $this->middleware('auth:api', ['only' => ['store', 'update', 'destroy']]);        
    }

    /**
     * Lista de la tabla auditoria evento paginadas.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $aud_evento = AuditoriaEvento::with('evento')
                    ->with("usuario")                                      
                    ->paginate(15);
        return $this->sendResponse($aud_evento->toArray(), 'Auditoria de eventos devueltos con éxito');
    }

    
    /**
     * Agrega un nuevo elemento a la tabla auditoria evento
     *     
     *@bodyParam id_evento int required Id del evento.
     *@bodyParam observacion string Observación cuando se solicita modificaciones.
     *@bodyParam status_1 int Estado antiguo
     *@bodyParam status_2 int Estado actual
     *@response{
     *       "id_evento" : 1,
     *       "observacion" : null,
     *       "status_1" : 1,
     *       "status_2": 0,            
     *     }
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_evento' => 'required|integer',
            'observacion' => 'nullable|string',
            'status_1' => 'nullable|integer',
            'status_2' => 'nullable|integer',
        ]);
        if($validator->fails()){
            return $this->sendError('Error de validación.', $validator->errors());       
        }

        $evento = Evento::find($request->input('id_evento'));
        if (is_null($evento)) {
            return $this->sendError('Evento indicado no encontrado');
        }

        $aud_evento = new AuditoriaEvento();
        
        $aud_evento->id_evento = $request->input('id_evento');
        $aud_evento->observacion = $request->input('observacion');
        $aud_evento->status_1 = $request->input('status_1');
        $aud_evento->status_2 = $request->input('status_2');
        $aud_evento->email_usuario = auth()->user()->email;
        $aud_evento->date = Carbon::now();
        
        $aud_evento->save();

        return $this->sendResponse($aud_evento->toArray(), 'Auditoria evento creada con éxito');
    }

    /**
     * Lista una auditoria por evento en específico 
     *
     * [Se filtra por el ID]
     *
     * @param  \App\Models\AuditoriaEvento  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        
        //$aud_evento = AuditoriaEvento::with('evento')->with('usuario')->find($id);
        $aud_evento = AuditoriaEvento::with(['evento'=>function($query) use($id) {$query->where('id','=',$id);}])->with('usuario')->get();
        if (is_null($aud_evento)) {
            return $this->sendError('Auditoria evento no encontrada');
        }
        return $this->sendResponse($aud_evento->toArray(), 'Auditoria evento devuelta con éxito');
    }

    
    /**
     * Actualiza un elemeto de la tabla auditoria evento
     *     
     *@bodyParam id_evento int required Id del evento.
     *@bodyParam observacion string Observación cuando se solicita modificaciones.
     *@bodyParam status_1 int Estado antiguo
     *@bodyParam status_2 int Estado actual
     *@response{
     *       "id_evento" : 1,
     *       "observacion" : "no aprobado",
     *       "status_1" : 0,
     *       "status_2": 2            
     *     }
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\AuditoriaEvento  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'id_evento' => 'required|integer',
            'observacion' => 'nullable|string',
            'status_1' => 'nullable|integer',
            'status_2' => 'nullable|integer'
        ]);
        if($validator->fails()){
            return $this->sendError('Error de validación.', $validator->errors());       
        }

        $aud_evento = AuditoriaEvento::find($id);        
        if (is_null($aud_evento)) {
            return $this->sendError('Auditoria evento no encontrada');
        } 

        $evento = Evento::find($request->input('id_evento'));
        if (is_null($evento)) {
            return $this->sendError('Evento indicado no encontrado');
        }

        $aud_evento->id_evento = $request->input('id_evento');
        $aud_evento->observacion = $request->input('observacion');
        $aud_evento->status_1 = $request->input('status_1');
        $aud_evento->status_2 = $request->input('status_2');
        $aud_evento->email_usuario = auth()->user()->email;
        $aud_evento->date = Carbon::now();        
        $aud_evento->save();

        return $this->sendResponse($aud_evento->toArray(), 'Auditoria evento actualizada con éxito');
    }

    /**
     * Elimina un elemento de la tabla auditoria evento
     *
     * [Se filtra por el ID]
     *
     * @param  \App\Models\AuditoriaEvento  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try { 

            $aud_evento = AuditoriaEvento::find($id);        
            if (is_null($aud_evento)) {
                return $this->sendError('Auditoria evento no encontrada');
            } 
            $aud_evento->delete();
            return $this->sendResponse($aud_evento->toArray(), 'Auditoria evento eliminada con éxito');

        }catch (\Illuminate\Database\QueryException $e){
            return response()->json(['error' => 'La Auditoria no se puedo eliminar, es usada en otra tabla', 'exception' => $e->errorInfo], 400);
        }
    }
}

