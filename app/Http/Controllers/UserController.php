<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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
                $pwd = password_hash($params->password, PASSWORD_BCRYPT, ['cost' => 4]);
                
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
    }

  