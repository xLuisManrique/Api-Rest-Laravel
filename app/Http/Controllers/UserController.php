<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller
{
    // metod testing
    public function pruebas(Request $request){
        return "Pruebas de UserController";
    }
    
    // metod register users
    public function register(Request $request){
        
        $name = $request->input('name');
        $surname = $request->input('surname');
        
        return "register user: $name $surname";
    }
    
    // metod login 
    public function login(Request $request){
        return "login users";
    }
}
