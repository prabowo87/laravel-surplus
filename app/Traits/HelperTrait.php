<?php
namespace App\Traits;
use DB;
use Auth;
use DateTime;
trait HelperTrait {
    public function string2Time($waktu=false){
       // if (Auth::check()){
            if ($waktu==false)
                return $hasil=date("Y-m-d");
            else{
                //01/03/2107 11:59
                //2017-03-01 11:59
                $arr=explode("-",$waktu);
                $thn=$arr[2];//substr($waktu, 6,4);
                $bulan=$arr[1];//substr($waktu, 3,2);
                $tgl=$arr[0];//substr($waktu, 0,2);
                
                $hasil=$thn."-".$bulan."-".$tgl." ";
                return $hasil;
            }
       // }
    }
    public function StandardResult($success,$data,$message=false) {
        if ($message){
            $res=[
                'success'=>$success,
                'message'=>$success,
                'data'=>$data
               ];
        }else{
            $res=[
                'success'=>$success,
                'data'=>$data
               ];
        }
       
       return $res;
                 
    }
    function validateDate($date, $format = 'Y-m-d')
    {
        $d = DateTime::createFromFormat($format, $date);
        // The Y ( 4 digits year ) returns TRUE for any integer with any number of digits so changing the comparison from == to === fixes the issue.
        return $d && $d->format($format) === $date;
    }
   
    
}