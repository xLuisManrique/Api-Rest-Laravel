<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\User;

class UserController extends Controller {

    // metod testing
    public function pruebas(Request $request) {
        return "Pruebas de UserController";
    }

    // metod register users
    public function register(Request $request) {

        // Collect user data
        $json = $request->input('json', null);
        $params = json_decode($json); // object
        $params_array = json_decode($json, true); // array

        if (!empty($params) && !empty($params_array)) {
            
            // Clean data
            $params_array = array_map('trim', $params_array);

            // Validate data
            $validate = \Validator::make($params_array, [
                        'name' => 'required|alpha',
                        'surname' => 'required|alpha',
                        'email' => 'required|email|unique:users', // Check if user exists
                        'password' => 'required'
            ]);
            if ($validate->fails()) {
                $data = array(
                    'status' => 'error',
                    'code' => 404,
                    'message' => "the user its not created",
                    'errors' => $validate->errors()
                );
            } else {
                // Encrypt password
                $pwd = hash('sha256', $params->password);
                
                // Create the user
                $user = new User();
                $user->name = $params_array['name'];
                $user->surname = $params_array['surname'];
                $user->email = $params_array['email'];
                $user->password = $pwd;
                $user->role = 'ROLE_USER';
                
                // Save User
                $user->save();
                
                $data = array(
                    'status' => 'succes',
                    'code' => 200,
                    'message' => "the user is created",
                    'user' => $user
                );
            }
                
            }else{
                $data = array(
                    'status' => 'error',
                    'code' => 404,
                    'message' => 'Invalid data'
                );
            }
            return response()->json($data, $data['code']);
        }
        
     public function login(Request $request){
         
        $jwtAuth = new \JwtAuth();
        
        // Receive data from POST
        $json = $request->input('json', null);
        $params = json_decode($json);
        $params_array = json_decode($json, true);
        
        // Validate received data
        $validate = \Validator::make($params_array, [
                        'email' => 'required|email',
                        'password' => 'required'
        ]);
        if ($validate->fails()){
            $signup = array(
                'status' => 'error',
                'code' => 404,
                'message' => "the user could not be identified",
                'errors' => $validate->errors()
            );
        }else {
            // Encrypt password
            $pwd = hash('sha256', $params->password);
            
            // Return token or data
            $signup = $jwtAuth->signup($params->email, $pwd);
            
            if(!empty($params->gettoken)){
                $signup = $jwtAuth->signup($params->email, $pwd, true);
            }
        }
        return response()->json($signup, 200);
    }      
    
    public function update(Request $request){
        
        //Check if user is identified
        $token = $request->header('Authorization');
        $jwt = new \JwtAuth();
        $checkToken = $jwtAuth->checkToken($token);
        
         // Collect data per post
        $json = $request->input('json', null);
        $params_array = json_decode($json, true);
                   
        
        if($checkToken && !empty($params_array)){
            
            // Remove identified user
            $checkToken = $jwtAuth->checkToken($token, true);
             
            // Validate the data
            $validate = \Validator::make($params_array, [
                'name' => 'required|alpha',
                'surname' => 'required|alpha',
                'email' => 'required|email|unique:users,'.$user->sub
            ]);
            
            // Remove the fields I don't want to update
            unset($params_array['id']);
            unset($params_array['role']);
            unset($params_array['password']);
            unset($params_array['created_at']);
            unset($params_array['id']);
            unset($params_array['remember_token']);
            
            // Update the user in DB
            $user_update = User::where('id', $user-sub)->update($params_array);
            
            // Return an array with the result
            $data = array(
                'code' => 200,
                'status' => 'Success',
                'message' => $user,
                'change' => $params_array
            );
            
        }else{
            $data = array(
                'code' => 400,
                'status' => 'Error',
                'message' => 'User are not identicated'
            );
        }
        return response()->json($data, $data['code']);
    }
    
    public function upload(Request $request){
        
        $image = $request->fille('file0');
        
        $validate = \Validator::make($request->all(), [
            'file0' => 'required|image|mimes:jpg,jpeg,png,gif'
        ]);
        
        if($image || $validate->fails()){
            $data = array(
                'code' => 400,
                'status' => 'Error',
                'message' => 'Upload failed'
            );
        }else{
            $image_name = time().$image->getClientOriginalName();
            \Storage::disk('users')->put($image_name, \File::get($image));
            
            $data = array(
                'image' => $image_name,
                'code' => 200,
                'status' => 'success'
            );
        }
        return response()->json($data, $data['code']);
    }
    
    public function getImage($filename){
        
        $isset = \Storage::disk('users')->exists($filename);
        
        if($isset){
            $file = \Storage::disk('users')->get($filename);
            return new Response($file, 200);   
        }else{
            $data = array(
                'status' => 'error',
                'code' => 404,
                'message' => 'image not exist'
            );
            return response()->json($data, $data['code']);
        }
    }
    
    public function detail($id){
        $user = User::find($id);
        
        if(is_object($user)){
            $data = array(
                'code' => 200,
                'status' => 'success',
                'user' => $user
            );
        }else{
            $data = array(
                'status' => 'error',
                'code' => 404,
                'message' => 'user not exist'
            );
        }
        return response()->json($data, $data['code']);
    }
}

  
