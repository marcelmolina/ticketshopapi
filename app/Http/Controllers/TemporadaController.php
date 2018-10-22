<?php

namespace App\Http\Controllers;

use App\Models\Temporada;
use Illuminate\Http\Request;
use Validator;

class TemporadaController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
         $temporada = Temporada::paginate(15);

        return $this->sendResponse($temporada->toArray(), 'Temporadas devueltas con éxito');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
 

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
         $validator = Validator::make($request->all(), [
            'nombre' => 'required'            
        ]);
        if($validator->fails()){
            return $this->sendError('Error de validación.', $validator->errors());       
        }
          $temporada=Temporada::create($request->all());        
         return $this->sendResponse($temporada->toArray(), 'Temporada creada con éxito');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Temporada  $temporada
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
          $temporada = Temporada::find($id);


        if (is_null($temporada)) {
            return $this->sendError('Temporada no encontrada');
        }


        return $this->sendResponse($temporada->toArray(), 'Temporada devuelta con éxito');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Temporada  $temporada
     * @return \Illuminate\Http\Response
     */
 
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Temporada  $temporada
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Temporada $temporada)
    {
        //
        $input = $request->all();

        $validator = Validator::make($input, [
            'nombre' => 'required',            
        ]);

        if($validator->fails()){
            return $this->sendError('Error de validación', $validator->errors());       
        }
        
        $temporada->nombre = $input['nombre'];
        if (!is_null($request->input('status'))) 
            $temporada->status = $input['status'];
         $temporada->save();

        return $this->sendResponse($temporada->toArray(), 'Temporada actualizada con éxito');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Temporada  $temporada
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        $temporada =Temporada::find($id);
         if (is_null($temporada)) {
            return $this->sendError('Temporada no encontrada');
        }
        $temporada->delete();


        return $this->sendResponse($temporada->toArray(), 'Temporada eliminada con éxito');
    }
}
