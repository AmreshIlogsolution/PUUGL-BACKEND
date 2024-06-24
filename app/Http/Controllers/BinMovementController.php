<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;


class BinMovementController extends Controller
{



 
######      ####### FROM BIN SEARCH   #####          ######
public function fromBinSearch(Request $request){

    if(!empty($request->warehouseId) &&  !empty($request->clientId)){
  
        #Search Key Based on ASNNUMBER AND //R11-01-01
    $query = "select * from Tbl_putway WITH(NOLOCK) where wh='$request->warehouseId' and cust='$request->clientId' and WHLOCATION='$request->whLocation' and balqty>0";
  
    $QData =  \DB::select($query);
    if(!empty($QData)){
        
        $query2 = "select sku,batchNo,balqty,sloc,whLocation from Tbl_putway WITH(NOLOCK) where WHLOCATION='$request->whLocation' and cust='$request->clientId' and wh='$request->warehouseId' and balqty>0";

        $QData =  \DB::select($query2);

        $Data = ["data"=>$QData]; 

    }  else{

        $Data=['message'=>"Somethings went wrong","status"=>"401","data"=>[]];
    }
  
   
  
    return response()->json($Data);
   
  
     }
  
  ############



}

public function scanToBinTransfer(Request $request){
   
    if(!empty($request->warehouseId) &&  !empty($request->clientId)){

        $query = "DECLARE @TestVariable AS VARCHAR(100)='F-Z999' select distinct LOCATION FROM WHLocation WITH(NOLOCK) WHERE WH='$request->warehouseId' AND Custid='$request->clientId' AND ISNULL(CCSTATUS,'')='' AND ISNULL(LOCATIONSTATUS,'')=''  AND Location='$request->scanBinNo' UNION select 'F-Z999'  AS LOCATION  WHERE @TestVariable='$request->scanBinNo'";
        $QData =  \DB::select($query);

        if($QData){
        $Data = ['message'=>"Data Fetched Successfully","status"=>"200","data"=>$QData]; 
        }else{
            $Data=['message'=>"Somethings went wrong","status"=>"401"];
        }
       

    } else{

        $Data=['message'=>"Somethings went wrong","status"=>"401"];
    }
    return response()->json($Data);
}
######

public function binToBinTransferSave(Request $request){

 
    if(!empty($request->warehouseId) &&  !empty($request->clientId)){
      

        $query = "select (SELECT ISNULL(SUM(QTY),0) - ISNULL(SUM(PICKQTY),0) FROM Tbl_PickingDataRetail r WITH(NOLOCK) WHERE r.WH=p.WH AND r.CUSTID=p.CUST AND r.WHLOCATION=p.WHLOCATION  AND ISNULL(WMSIN,'')='' and r.SKU=p.sku ) as PendingForPick from Tbl_putway p WITH(NOLOCK) where p.wh='$request->warehouseId' and CUST='$request->clientId' and p.WHLOCATION='$request->fromBinNo'  group by p.sku,p.WHLocation,p.WH,p.cust";

        $QData =  \DB::select($query);



        if(count($QData)>0){

           
            try { 
                
                // check pendingForPick is not equal zero 
                if($QData[0]->PendingForPick != 0){
                    $Data=['message'=>"Some Qty is Blocked","status"=>"401"]; 
                    return response()->json($Data);

                }else{
                  // return response($request->body);

                   // 
                   //$body=json_decode($request->body, true);
                   $body= $request->body;
                   $unique = uniqid(); //Generate unique id for insert entry//
                  // print_r($unique);die();
                    foreach($body as $k => $item){ 
                   
                            $sku=  $item['sku'];
                            $batchNo=  $item['batchNo'];
                            $balqty = $item['balqty'];
                            $sloc=  $item['sloc'];
                            $whLocation=  $item['whLocation'];

                        $qry = "select qty from Tbl_putway WITH(NOLOCK) where sloc='$sloc' and whLocation='$request->fromBinNo' and SKU='$sku' AND BATCHNO='$batchNo'  and cust='$request->clientId' AND WH='$request->warehouseId' ";
                        $Qry =  \DB::select($qry);

                       
                         ///$Qry === false//
                        if( is_null($Qry) ){
                            $Data=['message'=>"Some Qty is Blocked","status"=>"0"]; 
                            return response()->json($Data);
                        }else{
                           
                            if($Qry[0]->qty > 0){
                                //print_r( $Qry[0]->qty );   die();
                                $update =  "update Tbl_putway WITH(TABLOCK) set qty=qty-'$balqty',balqty=isnull(balqty,0)-'$balqty' where sloc='$sloc' and whLocation='$request->fromBinNo' and SKU='$sku'  AND BATCHNO='$batchNo' and cust='$request->clientId' AND WH='$request->warehouseId' ";
                               // print_r($update);
                                $qryExc =  \DB::update($update);
                            }

                            $qty2 = "select qty from Tbl_putway WITH(NOLOCK) where sloc='$sloc' and whLocation='$request->toBinNo' and SKU='$sku' AND BATCHNO='$batchNo' and cust='$request->clientId' AND WH='$request->warehouseId' ";
                           
                            $checkQty2 =  \DB::select($qty2);
                          
                         #check qty is greater than zero ##   
                            //print_r($checkQty2);
                            if(count($checkQty2) > 0){
                                $update = "update Tbl_putway WITH(TABLOCK) set qty=qty+'$balqty', balqty=isnull(balqty,0)+'$balqty' where sloc='$sloc' and whLocation='$request->toBinNo' and SKU='$sku' AND BATCHNO='$batchNo'  and cust='$request->clientId' AND WH='$request->warehouseId' ";
                                $qryExc =  \DB::update($update);
                            }else{
                                $insert =  "insert into Tbl_putway  WITH(TABLOCK) (SKU,batchNo,whLocation,qty,sloc,cust,wh,balqty) values('$sku','$batchNo','$request->toBinNo','$balqty','$sloc','$request->clientId','$request->warehouseId','$balqty')";
                                $qryExc =  \DB::insert($insert);
                            }
                          
                        $queryMain = "insert into Tbl_Putawaytransfer  WITH(TABLOCK) (SKU,batchNo,FROMWHLOC,TOWHLOC,qty,sloc,custID,wh,ENTRYBY,ENTRYON,DOCNO)values('$sku','$batchNo','$request->fromBinNo','$request->toBinNo','$balqty','$sloc','$request->clientId','$request->warehouseId','$request->UserId',GETDATE(), '".$unique."')";
                        $qInsert =  \DB::insert($queryMain);

                        if ($qInsert){
                            $Data=['message'=>"Bin transfer Successfully","status"=>"200"]; 
                          
                        }else{
                            $Data=['message'=>"Somethings went wrong","status"=>"40133"]; 
                        }
                           
                        }

                  
                   
                }
                }


               } catch(\Illuminate\Database\QueryException $ex){ 
                $Data=['message'=>"Somethings went wrong","status"=>"401cccc"]; 
                // Note any method of class PDOException can be called on $ex.
              }
        }else{
            $Data=['message'=>"Somethings went wrong","status"=>"401elseee"]; 
        }
       
    }else{
        $Data=['message'=>"Somethings went wrong","status"=>"401eeeee"];   
    }
   

    return response()->json($Data);

}


 ####  ######  #############


public function binToLocationSearch(Request $request){


    if(!empty($request->warehouseId) &&  !empty($request->clientId)){

       if($request->whLocation){
        $query = "select distinct whlocation from Tbl_putway 
        where wh='$request->warehouseId' and CUST='$request->clientId' and BALQTY>0 and WHLOCATION='$request->whLocation' ";

        $QData =  \DB::select($query);
        if(count($QData)>0){
            $Data = ['message'=>"Data Fetched Successfully","status"=>"200","data"=>$QData]; 
            }else{
            $Data=['message'=>"No Data Found","status"=>"401"];    
            }
        }else{
            $Data=['message'=>"Somethings went wrong !Client and warehouse not found","status"=>"401"];  
        }
       
       
       
      
}else{
    $Data=['message'=>"Somethings went wrong !Client and warehouse not found","status"=>"401"]; 
}

return response()->json($Data);
}


public function scanSkuBinTransfer(Request $request){




    if(!empty($request->warehouseId) &&  !empty($request->clientId)){
        
        $query = "select distinct whlocation AS BIN,SLOC ,SKU,QTY, BATCHNO,BALQTY AS AVAILABLEQTY from Tbl_putway with(nolock) where wh='$request->warehouseId' and CUST='$request->clientId' and BALQTY>0 and WHLOCATION='$request->fromBinNo' AND SKU='$request->scanSkuNo'";
     
        $QData =  \DB::select($query);
      
        if($QData){
            if(count($QData) > 0){
               
                $Data = ['message'=>"Data Fetched Successfully","status"=>"200","data"=>$QData]; 

            }else{
                $Data=['message'=>"Somethings went wrong !No SKU Not Found or Not belongs to this Bin","status"=>"401"];     
            }

        }else{
            $Data=['message'=>"Somethings went wrong !SKU Not Found","status"=>"401"];   
        }


        $data='';
    }else{
        $Data=['message'=>"Somethings went wrong !Client and warehouse not found","status"=>"401"]; 
    }
    return response()->json($Data);

}
##end

public function getToBinLocationSearch(Request $request){


    if(!empty($request->warehouseId) &&  !empty($request->clientId)){
        
        // $query = "select distinct whlocation AS BIN,SLOC ,SKU,QTY, BATCHNO,BALQTY AS AVAILABLEQTY from Tbl_putway with(nolock) where wh='$request->warehouseId' and CUST='$request->clientId' and BALQTY>0 and WHLOCATION='$request->fromBinNo' AND SKU='$request->scanSkuNo'";
        $query=" DECLARE @TestVariable AS VARCHAR(100)='F-Z999' select distinct LOCATION FROM WHLocation WITH(NOLOCK) WHERE WH='$request->warehouseId' AND Custid='$request->clientId' AND ISNULL(CCSTATUS,'')='' AND ISNULL(LOCATIONSTATUS,'')=''  AND Location='$request->getBinNo' UNION select 'F-Z999'  AS LOCATION  WHERE @TestVariable='$request->getBinNo'";
        
     
        $QData =  \DB::select($query);
     
        if($QData){
            if(count($QData) > 0){
               
                $Data = ['message'=>"Data Fetched Successfully","status"=>"200","data"=>$QData]; 

            }else{
                $Data=['message'=>"Somethings went wrong !No SKU Not Found or Not belongs to this Bin","status"=>"401"];     
            }

        }else{
            $Data=['message'=>"Somethings went wrong !SKU Not Found","status"=>"401"];   
        }


        $data='';
    }else{
        $Data=['message'=>"Somethings went wrong !Client and warehouse not found","status"=>"401"]; 
    }
    return response()->json($Data);
}

public function binToLocationTransferSave(Request $request){

    if(!empty($request->warehouseId) &&  !empty($request->clientId)){
        $entryBy=$request->UserId;

        $query = "select qty from Tbl_putway WITH(NOLOCK) where sloc='$request->sloc' and whLocation='$request->fromBinNo' and SKU='$request->skuNo' AND BATCHNO='$request->batchNo' and cust='$request->clientId' AND WH='$request->warehouseId' ";
        $QData =  \DB::select($query);

        if($QData){

            if(count($QData) > 0){
             $updQry="update Tbl_putway WITH(TABLOCK) set qty=qty-'$request->toQty',balqty=isnull(balqty,0)-'$request->toQty' where sloc='$request->sloc' and whLocation='$request->fromBinNo' and SKU='$request->skuNo' and BATCHNO='$request->batchNo' and cust='$request->clientId' AND WH='$request->warehouseId'";
            // print_r($updQry);die();
             $toBinQry =  \DB::insert($updQry);
             //print_r($toBinQry);die();

            }
            else{
                $Data=['message'=>"Somethings went wrong !No SKU Not Found or Not belongs to this Bin","status"=>"401"];     
            }
        }else{
            $Data=['message'=>"Somethings went wrong !SKU Not Found","status"=>"401"]; 
        }

        $tquery = "select qty from Tbl_putway WITH(NOLOCK) where sloc='$request->sloc' and whLocation='$request->toBinNo' and SKU='$request->skuNo' AND BATCHNO='$request->batchNo'  and cust='$request->clientId' AND WH='$request->warehouseId'";
        $toBinQry =  \DB::select($tquery);

       

        if($toBinQry){
            if(count($toBinQry) > 0){
                $updateQry = "update Tbl_putway WITH(TABLOCK) set qty=qty+'$request->toQty', balqty=isnull(balqty,0)+'$request->toQty' where sloc='$request->sloc' and whLocation='$request->toBinNo' and SKU='$request->skuNo' AND BATCHNO='$request->batchNo' and cust='$request->clientId' AND WH='$request->warehouseId'"; 
                $binINQry =  \DB::update($updateQry);

               
            }else{
                $insertQry = "insert into Tbl_putway  WITH(TABLOCK) (SKU,batchNo,whLocation,qty,sloc,cust,wh,balqty)values('$request->skuNo','$request->batchNo','$request->toBinNo','$request->toQty','$request->sloc','$request->clientId','$request->warehouseId','$request->toQty')";
                $binINQry =  \DB::insert($insertQry);
               // print_r($binINQry);die();
            }

            $inQuery = "insert into Tbl_Putawaytransfer  WITH(TABLOCK) (SKU,batchNo,FROMWHLOC,TOWHLOC,qty,sloc,custID,wh,ENTRYBY,ENTRYON,DOCNO)values
         ('$request->skuNo','$request->batchNo','$request->fromBinNo','$request->toBinNo','$request->toQty','$request->sloc','$request->clientId','$request->warehouseId','".$entryBy."',GETDATE(), '".uniqid()."')";
  $toBinQry =  \DB::insert($inQuery);
  if($toBinQry){
    $Data=['message'=>"Successfully Update!","status"=>"200"];   
  }else{
    $Data=['message'=>"Insert Not done","status"=>"401"]; 
  }

        }else{
            $Data=['message'=>"Somethings went wrong !No SKU Not Found or Not belongs to this Bin","status"=>"401"];   
        }

    }else{
        $Data=['message'=>"Somethings went wrong !Client and warehouse not found","status"=>"401"]; 
    }

}

##
}
?>