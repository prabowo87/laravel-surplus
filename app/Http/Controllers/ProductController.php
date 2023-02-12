<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProductModel;
use App\Traits\HelperTrait;
use Response;
use Validator;
class ProductController extends Controller
{
    use HelperTrait;
    public function get(Request $req){
        
        $page=isset($req->page) ? $req->page : 0;
        $id=$req->id;
        $data=ProductModel::offset($page)->limit(10)->get();
        $res=$this->StandardResult(true,$data);
        if ($id){
            $data=ProductModel::find($id);
            $res=$this->StandardResult(true,$data);
        }
        return response()->json($res);
    }

    public function update(Request $req){
        $input = $req->all();
        $validator = Validator::make($input, [
            'id' => 'required',
            'name' => 'required',
            'description' => 'required',
            'enable' => 'required',
        ]);

        if($validator->fails()){
            return response()->json(['error' => $validator->errors()->all()]);
        }
        $data=ProductModel::find($input['id']);
        if ($data){
            $data->name=$input['name'];
            $data->description=$input['description'];
            $data->enable=$input['enable'];
            $data->save();
            $res=$this->StandardResult(true,$data);
        }
        return response()->json($res);
    }
    public function add(Request $req){
        $input = $req->all();
        $validator = Validator::make($input, [
            'name' => 'required',
            'enable' => 'required',
        ]);

        if($validator->fails()){
            return response()->json(['error' => $validator->errors()->all()]);
        }
        $data = new ProductModel;
            $data->name=$input['name'];
            $data->description=$input['description'];
            $data->enable=$input['enable'];
            $data->save();
            $res=$this->StandardResult(true,$data);
        
        return response()->json($res);
    }
    public function delete(Request $req){
        $input = $req->all();
        $validator = Validator::make($input, [
            'id' => 'required'
        ]);

        if($validator->fails()){
            return response()->json(['error' => $validator->errors()->all()]);
        }
        $data=ProductModel::find($input['id']);
        if ($data){
            $data->delete(); 
            
            $res=$this->StandardResult(true,$data);
        }
        return response()->json($res);
    }
}
