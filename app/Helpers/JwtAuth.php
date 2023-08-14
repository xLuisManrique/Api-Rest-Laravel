<?php

namespace App\Helpers;

use Firebase\JWT\JWT;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class JwtAuth{
    
    public $key;
    public function __construct() {
        $this->key = 'secret_key_for_api_rest_proyect_jsmaster-*';
    }
    
    public function signup($email, $password, $getToken = null){
        
        // Find if user exists with credentials
        $user = User::where([
            'email' => $email,
            'password' => $password
        ])->first();
        
        // Check if is correct (return object)
        $signup = false;
        if(is_object($user)){
            $signup = true;
        }
        
        // Generated token whit data user
        if($signup){
            $token = array(
                'sub'       =>  $user->id,
                'email'     =>  $user->email,
                'name'      =>  $user->name,
                'surname'   =>  $user->surname,
                'iat'       =>  time(),
                'exped'     =>  time() + (7 * 24 * 60 * 60)
            );
            
            $jwt = JWT::encode($token, $this->key, 'HS256');
            $decoded = JWT::decode($jwt, $this->key, ['HS256']);
            
        // Return datas decode or token
            if(is_null($getToken)){
                $data = $jwt;
            }else{
                $data = $decoded;
            }
        }else{
            $data = array(
                'status' => 'error',
                'message' => 'login invalid'
            );
        }
        return $data;
    }
    
   
    
    
    
}

