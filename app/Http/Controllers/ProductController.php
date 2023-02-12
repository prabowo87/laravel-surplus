<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProductModel;
use App\Models\ImageModel;
use App\Traits\HelperTrait;
use Response;
use Validator;
use DB;
class ProductController extends Controller
{
    use HelperTrait;
    public function get(Request $req){
        
        $page=isset($req->page) ? $req->page : 0;
        $id=$req->id;
        $data=DB::table('product')->select('*')->offset($page)->limit(10)->get();
        
        foreach($data as $rowProduct){
            $categoryProduct=DB::table('category_product')->select('category_product.category_id','category.name')
            ->leftJoin('category','category.id','category_product.category_id')
            ->leftJoin('product','product.id','category_product.product_id')
            ->where('product.enable',1)
            ->where('category.enable',1)
            ->where('product_id',$rowProduct->id)
            ->get();
            $imageProduct=DB::table('product_image')->select('product_image.product_id','image.name','image.file')
            ->leftJoin('image','image.id','product_image.image_id')
            ->leftJoin('product','product.id','product_image.product_id')
            ->where('product.enable',1)
            ->where('image.enable',1)
            ->where('product_id',$rowProduct->id)
            ->get();
            $imgCollection=[];
            foreach ($imageProduct as $row){
                // dd(url('image/images/'.$row->file));
                $row->file=url('image/images/'.$row->file);
                array_push($imgCollection,$row);
                
            }
            $rowProduct->cagetory=$categoryProduct;
            $rowProduct->image=$imgCollection;
        }
        
        // dd($data);
        $res=$this->StandardResult(true,$data);
        if ($id){
            $data=DB::table('product')->where('enable',true)->where('id',$id)->first();
            // dd($data);
            if ($data){
                $categoryProduct=DB::table('category_product')->select('category_product.category_id','category.name')
                ->leftJoin('category','category.id','category_product.category_id')
                ->leftJoin('product','product.id','category_product.product_id')
                ->where('product.enable',1)
                ->where('category.enable',1)
                ->where('product_id',$data->id)
                ->get();
                $imageProduct=DB::table('product_image')->select('product_image.image_id','image.name','image.file')
                ->leftJoin('image','image.id','product_image.image_id')
                ->leftJoin('product','product.id','product_image.product_id')
                ->where('product.enable',1)
                ->where('image.enable',1)
                ->where('product_id',$data->id)
                ->get();
                $imgCollection=[];
                foreach ($imageProduct as $row){
                    // dd(url('image/images/'.$row->file));
                    $row->file=url('api/image/images/'.$row->file);
                    array_push($imgCollection,$row);
                    
                }
                $data->cagetory=$categoryProduct;
                $data->image=$imgCollection;
            
            }
            $res=$this->StandardResult(true,$data);
        }
        return response()->json($res);
    }

    public function getImage(Request $req){
        return $this->preview($req->folder,$req->filename);
    }

    public function update(Request $req){
        $input = $req->all();
        $categoryCollection=[];
        $imageCollection=[];
        $validator = Validator::make($input, [
            'id' => 'required',
            'name' => 'required',
            'description' => 'required',
            'enable' => 'required',
        ]);

        if($validator->fails()){
            return response()->json(['error' => $validator->errors()->all()]);
        }
        try{
            DB::beginTransaction();
            $data=ProductModel::find($input['id']);
            if ($data){
                $data->name=$input['name'];
                $data->description=$input['description'];
                
                $data->enable=$input['enable'];
                $data->save();
                if ($input['category']){
                    $categoryRows = explode(',', $input['category']);
                    DB::table('category_product')->where('product_id',$input['id'])->delete();
                    foreach ($categoryRows as $rowCategory){
                        $categoryData=DB::table('category_product')->insert([
                            'product_id'=>$input['id'],
                            'category_id'=>$rowCategory,
                        ]);
                        array_push($categoryCollection,$rowCategory);
                    }
                }
                if(!$req->hasFile('fileName')) {
                    DB::rollback();
                    return response()->json(['upload_file_not_found'], 400);
                }else{
                    $allowedfileExtension=['jpg','png'];
                    $files = $req->file('fileName'); 
                    $errors = [];
                    DB::table('product_image')->where('product_id',$data->id)->delete();
                    foreach ($files as $file) {      
                       /*  DB::rollback();
                        dd($files); */
                        $extension = $file->getClientOriginalExtension();
                       /*  DB::rollback();
                        dd($file); */
                        $check = in_array($extension,$allowedfileExtension);
                 
                        if($check) {
                            foreach($req->fileName as $mediaFiles) {
                 
                                $path = $mediaFiles->store('public/images');
                                /* DB::rollback(); */
                                /* dd($path); */
                                $name = $mediaFiles->getClientOriginalName();
                      
                                //store image file into directory and db
                                $save = new ImageModel();
                                $save->name = basename($path);
                                $save->file = basename($path);
                                $save->enable = true;
                                $save->save();
                                array_push($imageCollection,$save);
                            }
                        } else {
                            //return response()->json(['invalid_file_format'], 422);
                        }
                 
                       // return response()->json(['file_uploaded'], 200);
                 
                    }
                }
             
                
                $data->category=$categoryCollection;
                $data->image=$imageCollection;
                DB::commit();
                $res=$this->StandardResult(true,$data);
            }
        } catch (\Exception $e) {
            
            DB::rollback();

            // dd($e);
            $res=$this->StandardResult(false,[],'failed update');
        }
        /* 
        "id":1,
    "name":"product 1 update",
    "category":"1,4",
    "description":"description update 1",
    "enable":true */
        return response()->json($res);
    }
    public function add(Request $req){
        $input = $req->all();
        $categoryCollection=[];
        $imageCollection=[];
        $validator = Validator::make($input, [
            'name' => 'required',
            'description' => 'required',
            'enable' => 'required',
        ]);

        if($validator->fails()){
            return response()->json(['error' => $validator->errors()->all()]);
        }
        try{
            DB::beginTransaction();
            $data = new ProductModel;
            $data->name=$input['name'];
            $data->description=$input['description'];
            $data->enable=$input['enable'];
            $data->save();
            if ($input['category']){
                $categoryRows = explode(',', $input['category']);
                DB::table('category_product')->where('product_id',$data->id)->delete();
                foreach ($categoryRows as $rowCategory){
                    $categoryData=DB::table('category_product')->insert([
                        'product_id'=>$data->id,
                        'category_id'=>$rowCategory,
                    ]);
                    array_push($categoryCollection,$rowCategory);
                }
            }
            if(!$req->hasFile('fileName')) {
                DB::rollback();
                return response()->json(['upload_file_not_found'], 400);
            }else{
                $allowedfileExtension=['jpg','png'];
                $files = $req->file('fileName'); 
                $errors = [];
                DB::table('product_image')->where('product_id',$data->id)->delete();
                foreach ($files as $file) {      
                   /*  DB::rollback();
                    dd($files); */
                    $extension = $file->getClientOriginalExtension();
                   /*  DB::rollback();
                    dd($file); */
                    $check = in_array($extension,$allowedfileExtension);
             
                    if($check) {
                        foreach($req->fileName as $mediaFiles) {
             
                            $path = $mediaFiles->store('public/images');
                            /* DB::rollback(); */
                            /* dd($path); */
                            $name = $mediaFiles->getClientOriginalName();
                  
                            //store image file into directory and db
                            $save = new ImageModel();
                            $save->name = basename($path);
                            $save->file = basename($path);
                            $save->enable = true;
                            $save->save();
                            
                            $imageData=DB::table('product_image')->insert([
                                'product_id'=>$data->id,
                                'image_id'=>$save->id,
                            ]);
                            array_push($imageCollection,$save->id);
                        }
                    } else {
                        //return response()->json(['invalid_file_format'], 422);
                    }
             
                   // return response()->json(['file_uploaded'], 200);
             
                }
            }
         
            
            $data->category=$categoryCollection;
            $data->image=$imageCollection;
            DB::commit();
            $res=$this->StandardResult(true,$data);
        } catch (\Exception $e) {
            
            DB::rollback();

            dd($e);
            $res=$this->StandardResult(false,[],'failed update');
        }
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
            DB::table('category_product')->where('product_id',$input['id'])->delete();
            DB::table('product_image')->where('product_id',$input['id'])->delete();
            $res=$this->StandardResult(true,$data);
        }
        return response()->json($res);
    }
}
