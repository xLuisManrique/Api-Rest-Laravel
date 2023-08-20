<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\PostController;
use App\Helpers\JwtAuth;

class PostController extends Controller {

    public function __construct() {
        $this->middleware('api.auth', ['except' => ['index', 'show']]);
    }

    public function index() {
        $posts = Post::all()->load('category');

        return response()->json([
                    'code' => 200,
                    'status' => 'succes',
                    'posts' => $posts,
                        ], 200);
    }

    public function show($id) {
        $post = Category::find($id)->load('category');

        if (is_object($post)) {
            $data = [
                'code' => 200,
                'status' => 'succes',
                'post' => $post,
            ];
        } else {
            $data = [
                'code' => 404,
                'status' => 'error',
                'message' => 'post not exist',
            ];
        }
        return response()->json($data, $data['code']);
    }

    public function store(Request $request) {

        // Collect data by Post
        $json = $request->input('json', null);
        $params = json_decode($json);
        $params_array = json_decode($json, true);

        if (!empty($params_array)) {

            // Get identified user
            $jwtAuth = new JwtAuth();
            $token = $request->header('Authorization', null);
            $user = $jwtAuth->checkToken($token, true);

            // Validate the data
            $validate = \Validator::make($params_array, [
                        'title' => 'required',
                        'content' => 'required',
                        'category_id' => 'required',
                        'image' => 'required|image'
            ]);

            // Save post
            if ($validate->fails()) {
                $data = [
                    'code' => 400,
                    'status' => 'error',
                    'message' => 'post dont save'
                ];
            } else {
                $post = new Post();
                $post->user_id = $user->sub;
                $post->category_id = $params->category_id;
                $post->title = $params->title;
                $post->content = $params->content;
                $post->image = $params->image;
                $post->save();

                $data = [
                    'code' => 200,
                    'status' => 'success',
                    'post' => $post
                ];
            }
        } else {
            $data = [
                'code' => 400,
                'status' => 'error',
                'message' => 'post dont send'
            ];
        }

        // Return the result
        return response()->json($data, $data['code']);
    }

    public function update($id, Request $request) {

        // Collect data by Post
        $json = $request->input('json', null);
        $params_array = json_decode($json, true);
        
        // Datas for return
        $data = array(
                'code' => 400,
                'status' => 'error',
                'message' => 'post not update'
            );

        if (!empty($params_array)) {

            // Validate the data
            $validate = \Validator::make($params_array, [
                        'title' => 'required',
                        'content' => 'required',
                        'category_id' => 'required'
            ]);

            if($validate->fails()){
                $data['errors'] = $validate->errors();
                return response()->json($data, $data['code']);
            }
            // Remove i dont want update
            unset($params_array['id']);
            unset($params_array['user_id']);
            unset($params_array['created_at']);
            unset($params_array['user']);

            // Update the register
            $post = Post::where('id', $id)->update($params_array);

            $data = array(
                'code' => 200,
                'status' => 'success',
                'post' => $params_array
            );
        }

        // Return data
        return response()->json($data, $data['code']);
    }
}
