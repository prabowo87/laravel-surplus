<?php
namespace App\Traits;
use DB;
use Auth;
use DateTime;
use Response;
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
    public function preview($folder,$filename)
    {
        // $menuakses=(new HomeController)->getMenuAkses(Session::get('previllage'),31);
        // if ($menuakses != null){
            $path = storage_path('app/public/') . $folder.'/'.$filename ;
            $handler = new \Symfony\Component\HttpFoundation\File\File($path);

            $lifetime = 31556926; // One year in seconds

            /**
            * Prepare some header variables
            */
            $file_time = $handler->getMTime(); // Get the last modified time for the file (Unix timestamp)

            $header_content_type = $handler->getMimeType();
            $header_content_length = $handler->getSize();
            $header_etag = md5($file_time . $path);
            $header_last_modified = gmdate('r', $file_time);
            $header_expires = gmdate('r', $file_time + $lifetime);

            $headers = array(
                'Content-Disposition' => 'inline; filename="' . $filename . '"',
                'Last-Modified' => $header_last_modified,
                'Cache-Control' => 'must-revalidate',
                'Expires' => $header_expires,
                'Pragma' => 'public',
                'Etag' => $header_etag
            );

            /**
            * Is the resource cached?
            */
            $h1 = isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) && $_SERVER['HTTP_IF_MODIFIED_SINCE'] == $header_last_modified;
            $h2 = isset($_SERVER['HTTP_IF_NONE_MATCH']) && str_replace('"', '', stripslashes($_SERVER['HTTP_IF_NONE_MATCH'])) == $header_etag;

            if ($h1 || $h2) {
                return Response::make('', 304, $headers); // File (image) is cached by the browser, so we don't have to send it again
            }

            $headers = array_merge($headers, array(
                'Content-Type' => $header_content_type,
                'Content-Length' => $header_content_length
            ));

            return Response::make(file_get_contents($path), 200, $headers);
        // }else{
        //     return redirect()->action('HomeController@index');  
        // }    
    }
    
}