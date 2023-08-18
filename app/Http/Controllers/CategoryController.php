<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Category;

class CategoryController extends Controller {

    public function __construct() {
        $this->middleware('api.auth', ['except' => ['index', 'show']]);
    }

    public function index() {
        $categories = Category::all();

        return response()->json([
                    'code' => 200,
                    'status' => 'succes',
                    'categories' => $categories,
        ]);
    }

    public function show($id) {
        $category = Category::find($id);

        if (is_object($category)) {
            $data = [
                'code' => 200,
                'status' => 'succes',
                'category' => $category,
            ];
        } else {
            $data = [
                'code' => 404,
                'status' => 'error',
                'message' => 'category not exist',
            ];
        }
        return response()->json($data, $data['code']);
    }

    public function store(Request $request) {

        // Collect data by Post
        $json = $request->input('json', null);
        $params_array = json_decode($json, true);

        if (!empty($params_array)) {
            // Validate the data
            $validate = \Validator::make($params_array, [
                        'name' => 'required'
            ]);

            // Save category
            if ($validate->fails()) {
                $data = [
                    'code' => 400,
                    'status' => 'error',
                    'message' => 'categorie dont save'
                ];
            } else {
                $category = new Category();
                $category->name = $params_array['name'];
                $category->save();

                $data = [
                    'code' => 200,
                    'status' => 'success',
                    'message' => $category
                ];
            }
        } else {
            $data = [
                'code' => 400,
                'status' => 'error',
                'message' => 'categorie dont send'
            ];
        }

        // Return the result
        return response()->json($data, $data['code']);
    }
}
