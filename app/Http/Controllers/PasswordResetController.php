<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Notifications\PasswordResetRequest;
use App\Notifications\PasswordResetSuccess;
use App\Models\Usuario;
use App\Models\PasswordReset;
/**
 * @group Restablecimiento de contraseña
 *
 * APIs para la gestion del restablecimiento de la contraseña del usuario
 */
class PasswordResetController extends BaseController
{
    /**
     * Creación del token para restablecer la contraseña
     *@bodyParam email string required Email del usuario.
     *@response {
     *  "email": "email@example.com"            
     * }
     * @param  [string] email
     * @return [string] message
     */
    public function creatreset(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
        ]);

        $user = Usuario::where('email', $request->email)->first();
        
        if (!$user){
            return $this->sendError('No se encuentra un usuario con esa dirección de correo electrónico.');
        }
        
        $passwordReset = PasswordReset::updateOrCreate(
            ['email' => $user->email],
            [
                'email' => $user->email,
                'token' => str_random(60)
            ]
        );
        if ($user && $passwordReset)
            $user->notify(
                new PasswordResetRequest($passwordReset->token)
            );
       
        return $this->sendResponse($user->toArray(), 'Hemos enviado por correo electrónico su enlace de restablecimiento de contraseña');
    }

    /**
     * Buscar token para restablecer contraseña
     *
     * @param  [string] $token
     * @return [string] message
     * @return [json] passwordReset object
     */

    public function find($token)
    {
        $passwordReset = PasswordReset::where('token', $token)
            ->first();
        if (!$passwordReset){
        	return $this->sendError('Este token de restablecimiento de contraseña no es válido.');
        }
           
        if (Carbon::parse($passwordReset->updated_at)->addMinutes(720)->isPast()) {
            $passwordReset->delete();
            return $this->sendError('Este token de restablecimiento de contraseña no es válido.');
        }
        
        return $this->sendResponse($passwordReset->toArray(), 'Este Token debe ser usado para restablecer la contraseña.');
    }


    /**
     * Restablecer la contraseña
     *@bodyParam email string Email del usuario.
     *@bodyParam password string required Password del usuario.
     *@bodyParam password_confirmation string required Password de confirmación.
     *@bodyParam token string required Token recibido despues de la solicitud de restablecimiento de contraseña.
     *
     *@response {
     *  "email": "email@example.com",
     *  "password": "123456789",
     *  "password_confirmation": "123456789",
     *  "token": "kghkvnñskrnslnv34433GGHGthfhfnlknvlvnlnvlvnl8688",                
     * }
     * @param  [string] email
     * @param  [string] password
     * @param  [string] password_confirmation
     * @param  [string] token 
     * @return [string] message
     * @return [json] user object
     */
    public function reset(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string|confirmed',
            'token' => 'required|string'
        ]);
        $passwordReset = PasswordReset::where([
            ['token', $request->token],
            ['email', $request->email]
        ])->first();
        
        if (!$passwordReset){            
        	return $this->sendError('Este token de restablecimiento de contraseña no es válido.');
        }
        
        $user = Usuario::find($passwordReset->email);
        
        if (!$user){
            return $this->sendError('El email del usuario no se encuentra');
        }

        $user->password = bcrypt($request->password);
        $user->save();
        $passwordReset->delete();
        $user->notify(new PasswordResetSuccess($passwordReset));
        
        
        return $this->sendResponse($user->toArray(), 'Restablecimiento y cambio de contraseña exitoso.');
    }
}
