<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;

class DownloadController extends Controller
{
    //
    public function DataInventory(Request $request){
    


    }

    #########
    public function DownloadRecords(Request $request){
    


        $invQuery="SELECT MC_NO,CC_NO,PACK_CODE,PACK_NAME,PACK_BATCHNO,PACK_EXPIRY,(SELECT COUNT(DISTINCT PACK_EPC) FROM WMSDB.dbo.Tbl_Rfid R WITH(NOLOCK)
        WHERE R.MC_NO=RR.MC_NO AND R.CC_NO=RR.CC_NO AND R.PACK_CODE=RR.PACK_CODE AND ISNULL(Status,'')=''  )  AS PACK_QTY
        FROM WMSDB.dbo.Tbl_Rfid RR with(nolock)  WHERE CC_NO IN ('1/9','1/13','1/15','1/22') GROUP BY RR.PACK_NAME,RR.CC_NO,RR.MC_NO,mc_no,cc_No,PACK_CODE ,PACK_BATCHNO,PACK_EXPIRY";
        
        $invData  = DB::select($invQuery);
    
    $data = json_encode($invData);
	  

      $jsonFile = time() . '_file.json';
    //   if (!File::exists(public_path()."/downloads/files/")) {
    //     File::makeDirectory(public_path() . "/downloads/files/",0777, true);
    // }
	//   File::put(public_path('/downloads/files/'.$jsonFile), $data);

      $path = public_path('downloads/files/'.$jsonFile);
      $jsonFile = 'asnSub.json'; 

      if(!empty($data)){
        return response()->json(['data'=>$data],200);
    }else{
        return response()->json(['error'=>'error'],404);
    }      

     // return Response::download($path, $jsonFile, ['Content-Type: application/json']);
	  //return Response::download(public_path('/upload/jsonfile/'.$jsongFile));
    }
}
