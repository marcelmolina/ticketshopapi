<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Imagen;
use Validator;

/**
 * @group Administración de Imagen
 *
 * APIs para la gestion de la tabla imagen
 */
class ImagenController extends BaseController
{
    
    public function __construct()
    {
        $this->middleware('auth:api', ['only' => ['store', 'updateImage', 'destroy']]);        
    }


    /**
     * Lista de la tabla imagen.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        
        $imagen = Imagen::paginate(15);
        return $this->sendResponse($imagen->toArray(), 'Imagenes devueltas con éxito');
    }


    /**
     * Agrega un nuevo elemento a la tabla imagen
     * [Debe enviarse en el request un elemento tipo file que contenga como key="imagen" y el value="archivo tipo file"]
     *@bodyParam imagen file required Imagen.
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        
        $validator = Validator::make($request->all(), [
            'imagen' => 'required|mimes:jpeg,jpg,bmp,png',            
        ]);

        if($validator->fails()){
            return $this->sendError('Error de validación.', $validator->errors());       
        }

       
        $imagen = new Imagen();
        
        if($request->hasfile('imagen')){
            $file = $request->file('imagen');
            
            $name = $file->getClientOriginalName();
                        
            $fileUrl = Storage::disk('public')->put('imagenes', $file);
            
            $urlFile = env('APP_URL').'storage/'.$fileUrl;
            
            $imagen->url = $urlFile; 
            $imagen->nombre = $name;
            $imagen->save();

        }

        return $this->sendResponse($imagen->toArray(), 'Imagen creada con éxito');
    }

    /**
     * Lista una imagen en especifico
     * [Se filtra por el ID]
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        
        $imagen = Imagen::find($id);
        if (is_null($imagen)) {
            return $this->sendError('Imagen no encontrada');
        }

        return $this->sendResponse($imagen->toArray(), 'Imagen devuelta con éxito');
    }


    /**
     * Actualiza un elemeto de la tabla imagen
     * [Debe enviarse en el request un elemento tipo file que contenga como key="imagen" y el value="archivo tipo file"] 
     * [Se filtra por el ID]
      *@bodyParam imagen file required Imagen.
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateImage(Request $request, $id)
    {
               
        $validator = Validator::make($request->all(), [
            'imagen' => 'required|mimes:jpeg,jpg,bmp,png',            
        ]);

        if($validator->fails()){
            return $this->sendError('Error de validación.', $validator->errors());       
        }

        $imagen = Imagen::find($id);        
        if (is_null($imagen)) {
            return $this->sendError('Imagen no encontrada');
        }

        if($request->hasfile('imagen')){
            $file = $request->file('imagen');
            
            $name = $file->getClientOriginalName();
            
            $fileUrl = Storage::disk('public')->put('imagenes', $file);
            
            $urlFile = env('APP_URL').'storage/'.$fileUrl;
            
            $imagen->url = $urlFile; 
            $imagen->nombre = $name;
            $imagen->save();

        }  

        return $this->sendResponse($imagen->toArray(), 'Imagen actualizada con éxito');
    }

    /**
     * Elimina los elemento de la tabla imagen
     *
     * [Se filtra por el ID]
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try { 

            $imagen = Imagen::find($id);
            if (is_null($imagen)) {
                return $this->sendError('Imagen no encontrada');
            }
            $imagen->delete();
            return $this->sendResponse($imagen->toArray(), 'Imagen eliminada con éxito');

        }catch (\Illuminate\Database\QueryException $e){
            return response()->json(['error' => 'La imagen no se puedo eliminar, es usada en otra tabla', 'exception' => $e->errorInfo], 400);
        }
    }
}
