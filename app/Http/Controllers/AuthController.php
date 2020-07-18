<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    //input: username/password, output: token
    public function login(Request $request)
    {
        //todo validation username password
        $username = $request->username;
        $password = $request->password;

        $token = Str::random(80);

        if(Auth::attempt($request->only('username', 'password'))){
            //accesso riuscito
            $user = User::where('username', $username)->first();
            $user->api_token = hash('sha256', $username . $token);
            $user->save();
            return response()->json(['token' => $user->api_token], 200);
        } else {
            //credenziali errate
            return response()->json(['error' => 'Credenziali errate'], 401);
        }
    }

    public function register(Request $request)
    {
        //todo validation username password
        $username = $request->username;
        $password = $request->password;
        $email = $request->email;

        $token = Str::random(80);

        //$user = User::where([['username', $username], ['password', bcrypt($password)]])->first();
        $user = new User;
        $user->username = $username;
        $user->email = $email;
        $user->password = Hash::make($password);
        //è definito un mutator per crittografare il token
        $user->api_token = $token;
        $user->save();

        //todo ritornare il token
        return response()->json(['token' => $user->api_token], 200);
    }

    //invalida token
    public function logout($sid)
    {
        //todo validation sid
        if(! $sid) {
            return response()->json(['message' => 'Token non inserito'], 404);
        }
        //nuovo token random
        $token = Str::random(80);
        //ricerca dell'utente con quel sid
        $user = User::where('api_token', $sid)->firstOrFail();
        //aggiornamento del token
        $user->api_token = $token;
        //salvataggio dell'utente
        $user->save();
        return response()->json(['message' => 'Token invalidato']);

    }
}
