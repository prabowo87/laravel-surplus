<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CategoryModel;
use App\Traits\HelperTrait;
use Response;
use Validator;
class CategoryController extends Controller
{
    use HelperTrait;
    public function get(Request $req){
        
        $page=isset($req->page) ? $req->page : 0;
        $id=$req->id;
        $data=CategoryModel::offset($page)->limit(10)->get();
        $res=$this->StandardResult(true,$data);
        if ($id){
            $data=CategoryModel::find($id);
            $res=$this->StandardResult(true,$data);
        }
        return response()->json($res);
    }

    public function update(Request $req){
        $input = $req->all();
        $validator = Validator::make($input, [
            'id' => 'required',
            'name' => 'required',
            'enable' => 'required',
        ]);

        if($validator->fails()){
            return response()->json(['error' => $validator->errors()->all()]);
        }
        $data=CategoryModel::find($input['id']);
        if ($data){
            $data->name=$input['name'];
            $data->enable=$input['enable'];
            $data->save();
            $res=$this->StandardResult(true,$data);
        }
        return response()->json($res);
    }
}
