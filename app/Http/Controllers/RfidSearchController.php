<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
class RfidSearchController extends Controller
{
    public function rfidSearch(Request $request){
        if($request->params['radioVal']=='inv'){       
            //  $filter = explode(',', $request->params['searchKey']);
            $matchData =  array();
            $responseData = array_unique($request->params['searchKey']);
            
            $invData = DB::table('Tbl_Rfid')->select('MC_NO','CC_NO','CC_POSITION','PACK_NAME','SKU_NAME','SKU_QTY','PACK_EPC')
                    ->whereIn('PACK_EPC',$responseData)
                    ->get(); 
                // return response($invData);

            foreach($invData as  $check ){
                $matchData[]=$check->PACK_EPC;
            // array_push($matchData,$check->CC_EPCNO);
            }
            //return response($matchData);
            $dat=array();
            $notFoundD = array_diff($responseData,$matchData);

            $impArray= implode(',',$notFoundD);
           
            $expArray=explode(',',$impArray);
            if(empty($expArray)){
                $dar[]=array();
            }else{
                foreach( $expArray as $k =>$r){
                    $dar[]= array('pecNo' => $r);
                }
            }
           
           // return response($dar);
       
          

        return response()->json(['data'=>$invData,'responseData'=>$responseData,'matchData'=>$matchData,'resultDiff'=>$dar]);         

          
        }
    }

    ################
    public function rfidSearchInventoryByPack(Request $request){

        
        if($request->params['radioVal']=='inventoryByPack'){       
            //  $filter = explode(',', $request->params['searchKey']);
            $matchData =  array();
            $responseData = array_unique($request->params['searchKey']);
            
            $invData = DB::table('Tbl_Rfid')->select(DB::raw('COUNT(PACK_EPC) as PACKNO'),'MC_NO','CC_NO','CC_POSITION','PACK_NAME','PACK_EPC')->whereIn('PACK_EPC',$responseData)->groupBy('MC_NO','CC_NO','CC_POSITION','PACK_NAME','PACK_EPC')
                    ->get(); 
             
            foreach($invData as  $check ){
                $matchData[]=$check->PACK_EPC;
            // array_push($matchData,$check->CC_EPCNO);
            }
            //
            $dat=array();
            $notFoundD = array_diff($responseData,$matchData);

            $impArray= implode(',',$notFoundD);
           
            $expArray=explode(',',$impArray);
            foreach( $expArray as $k =>$r){
                $dar[]= array('pecNo' => $r);
            }
           // return response($dar);
       
          

        return response()->json(['data'=>$invData,'responseData'=>$responseData,'matchData'=>$matchData,'resultDiff'=>$dar]);         

          
        }
    }

##########

public function inventoryAllData(Request $request){

        
    if($request->params['radioVal']=='inventoryAllData'){       
        //  $filter = explode(',', $request->params['searchKey']);
        $matchData =  array();
        $responseData = array_unique($request->params['searchKey']);
        
        $invData = DB::table('Tbl_Rfid')->select(DB::raw('COUNT(distinct pack_epc) as TotalPack'),'mc_name','cc_name','cc_no','pack_name')->groupBy('mc_name','cc_name','cc_no','pack_name')
        ->get(); 

       
   
    return response()->json(['data'=>$invData]);         

      
    }
}
###

public function searchInventory(Request $request){

    // if($request->params['radioVal']=='inventoryAllData'){       
         //  $filter = explode(',', $request->params['searchKey']);
         $matchData =  array();
        // $responseData = array_unique($request->params['searchKey']);

        //  $invData = DB::table('Tbl_Rfid')->select(DB::raw('COUNT(distinct pack_epc) as TotalPack'),'mc_name','cc_name','cc_no','pack_name')->groupBy('mc_name','cc_name','cc_no','pack_name')
        //  ->get(); 
        $invData = DB::table('Tbl_Rfid')->select('MC_NAME','MC_NO')->groupBy('MC_NAME','MC_NO')
        ->get(); 
 
        
     return response()->json(['data'=>$invData]);         
    // }    
     
 
 }
##########

# ####

public function searchIdentify(Request $request){
    $matchData =  array();
    $responseData = array_unique($request->params['searchKey']);
    
    // $invData = DB::table('Tbl_Rfid')->select('MC_NO','CC_NO','CC_POSITION','PACK_NAME','SKU_NAME','SKU_QTY','PACK_EPC','PACK_EXPIRY')
    //         ->whereIn('PACK_EPC',$responseData)
    //         ->get(); 
    $currentDate= date('Y-m-d');
   

    $invData = DB::table('Tbl_Rfid')->select(DB::raw('COUNT(DISTINCT PACK_EPC) as PACK_EPC'),'MC_NO','CC_NO','CC_NAME','PACK_NAME','PACK_CODE','PACK_EXPIRY','PACK_BATCHNO','PACK_EPC')->groupBy('MC_NO','CC_NO','PACK_NAME','PACK_CODE','PACK_EXPIRY','PACK_BATCHNO','PACK_EPC','CC_NAME')
    ->whereIn('PACK_EPC',$responseData)
    ->get();
        // return response($invData);
  

//   $dataPack = DB::table('Tbl_Rfid')->select(DB::raw('COUNT(DISTINCT PACK_EPC) as PACKQTY'),'MC_NO','CC_NO','PACK_NAME','PACK_CODE','PACK_EXPIRY','PACK_BATCHNO')->groupBy('MC_NO','CC_NO','PACK_NAME','PACK_CODE','PACK_EXPIRY','PACK_BATCHNO')
//     ->whereIn('PACK_EPC',$responseData)
//     ->get();

    $dataPack = DB::table('Tbl_Rfid')->select(DB::raw('COUNT(DISTINCT PACK_EPC) as PACKQTY'),'MC_NO','CC_NO','PACK_NAME','PACK_CODE','PACK_EXPIRY','PACK_BATCHNO','KITURL','CC_NAME')
    ->leftJoin('Tbl_KitImage','Tbl_KitImage.KITNO','=','Tbl_Rfid.PACK_CODE')
    ->groupBy('MC_NO','CC_NO','PACK_NAME','PACK_CODE','PACK_EXPIRY','PACK_BATCHNO','KITURL','CC_NAME')
      ->whereIn('PACK_EPC',$responseData)
      ->get();


    $dataPackNew = DB::table('Tbl_Rfid')->select('MC_NO','MC_NAME')->groupBy('MC_NO','MC_NAME')
    ->whereIn('PACK_EPC',$responseData)
    ->get();

        // return response($invData);
        $k=0;
        foreach($dataPack as  $pack ){
            if($currentDate > $pack->PACK_EXPIRY ){
                $flag='red';
            }else{
                $flag='green';
            }
            $dataPack[$k]->flag=$flag;
        $k++;
        }

        $i=0;
    foreach($invData as  $check ){
        $matchData[]=$check->PACK_EPC;
        if($currentDate > $check->PACK_EXPIRY ){
            $flag='red';
        }else{
            $flag='green';
        }
        $invData[$i]->flag=$flag;
    // array_push($matchData,$check->CC_EPCNO);
    $i++;
    }
    //return response($matchData);
    $dat=array();
    $notFoundD = array_diff($responseData,$matchData);

    $impArray= implode(',',$notFoundD);
   
    $expArray=explode(',',$impArray);
    if(empty($expArray)){
        $dar[]=array();
    }else{
        foreach( $expArray as $k =>$r){
            $dar[]= array('pecNo' => $r);
        }
    }
   
   // return response($dar);
return response()->json(['data'=>$invData,'responseData'=>$responseData,'matchData'=>$matchData,'resultDiff'=>$dar,'newData'=>$dataPack]);         

}

##########

public function rfidKitSkus(Request $request){

    $matchData =  array();
   // $responseData = array_unique($request->params['searchKey']);
    $invData = DB::table('Tbl_Rfid')->select(DB::raw('DISTINCT SKU_NAME ,SKU_QTY , SKU_BATCH,BATCH_EXPIRY'),'CC_NAME','CC_NO' )
    ->where('PACK_BATCHNO',$request->params['packBatchNO'])
    ->where('PACK_CODE',$request->params['packCode'])
    ->where('CC_NO',$request->params['ccNo'] )
    ->get();

      // return response($invData);
return response()->json(['data'=>$invData]);    
}

#########


public function masterCubeDetails(Request $request){

    // if($request->params['radioVal']=='inventoryAllData'){       
         //  $filter = explode(',', $request->params['searchKey']);
         $matchData =  array();
        // $responseData = array_unique($request->params['searchKey']);
         
        //  $invData = DB::table('Tbl_Rfid')->select( DB::raw('COUNT(distinct pack_epc) as TotalPack') ,(DB::raw('select COUNT(distinct pack_epc) from  WMSDB.dbo.Tbl_Rfid as r
        //  WHERE r.MC_NO =rr.MC_NO and r.CC_NO =rr.CC_NO and r.PACK_NAME =rr.PACK_NAME and isnull(Status,"")="" ')),'MC_NAME','CC_NAME','CC_NO','PACK_NAME','PACK_BATCHNO','PACK_CODE','KITURL')->groupBy('MC_NO','MC_NAME','CC_NAME','CC_NO','PACK_NAME','PACK_BATCHNO','PACK_CODE','KITURL')
        //  ->leftjoin('Tbl_KitImage','Tbl_KitImage.KITNO', '=', 'Tbl_Rfid.CC_NO')
        //  ->where('MC_NO',$request->params['motherCube'] )
        //  ->get(); 
       
        
       $motherCubeNo= $request->params['motherCube'];


        $invQuery="select  COUNT(distinct pack_epc)  as actualQty ,(select COUNT(distinct pack_epc) from  WMSDB.dbo.Tbl_Rfid as r
        WHERE r.MC_NO =rr.MC_NO and r.CC_NO =rr.CC_NO and r.PACK_CODE  =rr.PACK_CODE and r.PACK_BATCHNO =rr.PACK_BATCHNO  and isnull(Status,'')=''  ) as availableQty ,MC_NAME,CC_NAME,
        CC_NO,PACK_NAME,PACK_BATCHNO,PACK_CODE,KITURL FROM WMSDB.dbo.Tbl_Rfid rr
        left join WMSDB.dbo.Tbl_KitImage kimg ON kimg.KITNO=rr.PACK_CODE
        WHERE MC_NO=$motherCubeNo
        GROUP BY MC_NAME,CC_NAME,MC_NO,CC_NO,PACK_NAME,PACK_BATCHNO,PACK_CODE,KITURL 
        order by CC_NAME asc";
        
        $invData  = DB::select($invQuery);
       
        // $invData = DB::table('Tbl_Rfid')->select('MC_NAME','MC_NO')->groupBy('MC_NAME','MC_NO')
        // ->get(); 
 
     return response()->json(['childCubedata'=>$invData]);         
    // }    
     
 
 }

}
