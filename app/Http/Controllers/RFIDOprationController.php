<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;



class RFIDOprationController extends Controller
{
    //

    public function rfidSearchchildList(Request $request){


        $responseData = array_unique($request->params['ccNo']);



        $childBoxData = DB::table('Tbl_Rfid')
        ->select('PACK_EPC')
        ->where('CC_NO',$request->params['searchKey'])
        ->get(); 

        $currentDate= date('Y-m-d');

        $childBoxName = DB::table('Tbl_Rfid')
        ->select('CC_NO','CC_NAME')
        ->whereIn('CC_EPCNO',$responseData)
        ->groupBy('CC_NO','CC_NAME')
        ->get();

        $childBoxDatafull = DB::table('Tbl_Rfid')
        ->select('PACK_EPC','MC_NO','CC_NO','CC_NAME','CC_POSITION','PACK_NAME','PACK_EXPIRY')
        ->where('CC_NO',$request->params['searchKey'])
        ->groupBy('PACK_EPC','MC_NO','CC_NO','CC_POSITION','PACK_NAME','PACK_EXPIRY','CC_NAME')
        ->get(); 

        $i=0;
        foreach($childBoxDatafull as  $check ){
         
            if($currentDate > $check->PACK_EXPIRY ){
                $flag='red';
            }else{
                $flag='green';
            }
            $childBoxDatafull[$i]->flag=$flag;
        // array_push($matchData,$check->CC_EPCNO);
        $i++;
        }

        return response(['limitData'=>$childBoxData,'fullData'=>$childBoxDatafull,'childCubeName'=>$childBoxName]) ;
    }


###############

    public function rfidSearchchild(Request $request){

        $childBoxData = DB::table('Tbl_Rfid')
        ->select('PACK_EPC')
        ->where('CC_NO',$request->params['searchKey'])
        ->get(); 

        $currentDate= date('Y-m-d');



        $childBoxDatafull = DB::table('Tbl_Rfid')
        ->select('PACK_EPC','MC_NO','CC_NO','CC_NAME','CC_POSITION','PACK_NAME','PACK_EXPIRY')
        ->where('CC_NO',$request->params['searchKey'])
        ->groupBy('PACK_EPC','MC_NO','CC_NO','CC_POSITION','PACK_NAME','PACK_EXPIRY','CC_NAME')
        ->get(); 

        $i=0;
        foreach($childBoxDatafull as  $check ){
            if($currentDate > $check->PACK_EXPIRY ){
                $flag='red';
            }else{
                $flag='green';
            }
            $childBoxDatafull[$i]->flag=$flag;
        // array_push($matchData,$check->CC_EPCNO);
        $i++;
        }

        return response(['limitData'=>$childBoxData,'fullData'=>$childBoxDatafull]) ;
    }


###############
public function searchMatchedUnmatchedRecords(Request $request){
    $request->params['cc_no'];
    $request->params['searchKey'];
    $currentDate= date('Y-m-d');

    $responseData = array_unique($request->params['searchKey']);


    $matchedEpcData = DB::table('Tbl_Rfid')->select('PACK_EPC','CC_NO')
    ->groupBy('PACK_EPC','CC_NO')
    ->where('CC_NO', $request->params['cc_no'])
    ->whereIn('PACK_EPC',$responseData)
    ->get();


    foreach($matchedEpcData as  $value ){
        $updatequery = "UPDATE Tbl_Rfid WITH(TABLOCK) SET Status=NULL  WHERE CC_NO='$value->CC_NO'  AND PACK_EPC='$value->PACK_EPC '";
        $update = DB::update($updatequery);
    }

   
    $matchedData = DB::table('Tbl_Rfid')->select(DB::raw('COUNT(DISTINCT PACK_EPC) as PACKQTY'),'MC_NO','CC_NO','CC_NAME','PACK_NAME','PACK_CODE','PACK_EXPIRY','PACK_BATCHNO')->groupBy('MC_NO','CC_NO','PACK_NAME','PACK_CODE','PACK_EXPIRY','PACK_BATCHNO','CC_NAME')
    ->where('CC_NO', $request->params['cc_no'])
    ->whereIn('PACK_EPC',$responseData)
    ->get();

   

    $i=0;
    foreach($matchedData as  $check ){
     
        if($currentDate > $check->PACK_EXPIRY ){
            $flag='red';
        }else{
            $flag='green';
        }
        $matchedData[$i]->flag=$flag;
    // array_push($matchData,$check->CC_EPCNO);
    $i++;
    }



    $unMatchedEpcData = DB::table('Tbl_Rfid')->select('PACK_EPC','CC_NO')
    ->groupBy('PACK_EPC','CC_NO')
    ->where('CC_NO', $request->params['cc_no'])
    ->whereNotIn('PACK_EPC',$responseData)
    ->get();

    foreach($unMatchedEpcData as  $val ){
        $updatequery = "UPDATE Tbl_Rfid WITH(TABLOCK) SET Status='N'  WHERE CC_NO='$val->CC_NO'  AND PACK_EPC='$val->PACK_EPC '";
        $update = DB::update($updatequery);

    }


    $unmatchedData = DB::table('Tbl_Rfid')->select(DB::raw('COUNT(DISTINCT PACK_EPC) as PACKQTY'),'MC_NO','CC_NO','CC_NAME','PACK_NAME','PACK_CODE','PACK_EXPIRY','PACK_BATCHNO')->groupBy('MC_NO','CC_NO','PACK_NAME','PACK_CODE','PACK_EXPIRY','PACK_BATCHNO','CC_NAME')
    ->where('CC_NO', $request->params['cc_no'])
    ->whereNotIn('PACK_EPC',$responseData)
    ->get();
    $i=0;



    foreach($unmatchedData as  $check ){
     
     

        if($currentDate > $check->PACK_EXPIRY ){
            $flag='red';
        }else{
            $flag='green';
        }
        $unmatchedData[$i]->flag=$flag;
    // array_push($matchData,$check->CC_EPCNO);
    $i++;
    }

    $othercubeData = DB::table('Tbl_Rfid')->select(DB::raw('COUNT(distinct PACK_EPC) as PACKQTY'),'MC_NO','CC_NO','CC_NAME','PACK_NAME','PACK_CODE','PACK_EXPIRY','PACK_BATCHNO')->groupBy('MC_NO','CC_NO','PACK_NAME','PACK_CODE','PACK_EXPIRY','PACK_BATCHNO','CC_NAME' )
    ->where('CC_NO','<>', $request->params['cc_no'])
    ->whereIn('PACK_EPC',$responseData)
    ->get();
    $i=0;
    foreach($othercubeData as  $check ){
     
        if($currentDate > $check->PACK_EXPIRY ){
            $flag='red';
        }else{
            $flag='green';
        }
        $othercubeData[$i]->flag=$flag;
    // array_push($matchData,$check->CC_EPCNO);
    $i++;
    }

    return response(['matchedData'=>$matchedData,'unmatchedData'=>$unmatchedData,'othercubeData'=>$othercubeData]) ;

}

}
