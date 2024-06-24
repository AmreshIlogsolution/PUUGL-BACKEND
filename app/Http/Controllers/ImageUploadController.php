<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;


class ImageUploadController extends Controller
{
    //

public function UploadsReceivedDocuments(Request $request){

  
    //images storing in minio //
  
          $data =$request->validate([
            'warehouseId' =>'required',
            'clientId' =>'required',
            'userId'=>'required',
            'vehicleImage'=> 'required',
            'vehicleSealImage'=>'required',
            'vehicleNoPlateImage'=>'required',
            'materialLoadedone'=>'required',
            'materialLoadedtwo'=>'required',
            'materialLoadedthree'=>'required'
          ]);
    // print_r( $data);
    // die;

   $warehouseId=   $request->warehouseId;
   $clientId=   $request->clientId;
   $userId=   $request->userId;
   $vehicleImage =  $request->vehicleImage;
   $vehicleSealImage=  $request->vehicleSealImage;
   $vehicleNoPlateImage = $request->vehicleNoPlateImage;
   $materialLoadedone=  $request->materialLoadedone;
   $materialLoadedtwo=  $request->materialLoadedtwo;
   $materialLoadedthree=  $request->materialLoadedthree;
   $asnNumber=$request->asnNumber;
   
  //    $fileUrl=$fileUrl;
  //    $fileName=$fileName;
  // $array=["warehouseId"=>$warehouseId,"clientId"=>$clientId,""]

  if(!empty($warehouseId) && !empty($clientId) && !empty($userId)  && !empty($asnNumber) && !empty($materialLoadedthree) && !empty($materialLoadedtwo) && !empty($materialLoadedone)  && !empty($vehicleNoPlateImage)  && !empty($vehicleSealImage) && !empty($vehicleImage) ){
   
    try{
      
    //   $imageName = time().'.'.$request->vehicleImage->extension();  
    //  return response($imageName);
    //     $request->image->move(public_path('images'), $imageName);


        if($request->hasFile('vehicleImage')){
          
            $vehicleImage=  $request->vehicleImage;
            $vehicleImagename= $asnNumber.'-vehicleImage-'.date('YmdHis').$vehicleImage->getClientOriginalExtension();

            $folder = public_path('/public/SWIM/Inward/Vehicle/'.$asnNumber.'/');
            if (!Storage::exists($folder)) {
                Storage::makeDirectory($folder, 0775, true, true);
            }

            
            $vehicleImage-> move($folder, $vehicleImagename);
            $fileUrl=public_path($folder.$vehicleImagename);
            $fileName= $vehicleImagename;
         
            $docType='vehicleImage';

            $vehicleImagecheck= $this->InsertImageQry($asnNumber,$fileName,$fileUrl,$docType,$userId,$clientId,$warehouseId);
          
        }else{
          $vehicleImagecheck=false;
        }
        
        if($request->hasFile('vehicleSealImage')){
          
            $vehicleSealImage=  $request->vehicleSealImage;
            $vsealImage= $asnNumber.'-vehicleSealImage-'.date('YmdHis').$vehicleSealImage->getClientOriginalExtension();

            $folder = public_path('/SWIM/Inward/Vehicle/'.$asnNumber.'/');
            if (!Storage::exists($folder)) {
                Storage::makeDirectory($folder, 0775, true, true);
            }

            $vehicleSealImage-> move($folder, $vsealImage);
            $fileUrl=public_path($folder.$vsealImage);
            $fileName= $vsealImage;
            $docType='vehicleSealImage';
          $vehicleSealImagecheck= $this->InsertImageQry($asnNumber,$fileName,$fileUrl,$docType,$userId,$clientId,$warehouseId);
         
        }else{
          $vehicleSealImagecheck=false;
        }
      
        if($request->hasFile('vehicleNoPlateImage')){
            $vehicleNoPlateImage=  $request->vehicleNoPlateImage;
            $vPlateName= $asnNumber.'-vehicleNoPlateImage-'.date('YmdHis').$vehicleNoPlateImage->getClientOriginalExtension();

            $folder = public_path('/SWIM/Inward/Vehicle/'.$asnNumber.'/');
            if (!Storage::exists($folder)) {
                Storage::makeDirectory($folder, 0775, true, true);
            }

            $vehicleNoPlateImage-> move($folder, $vPlateName);
            $fileUrl=public_path($folder.$vPlateName);
            $fileName= $vPlateName;
            $docType='vehicleNoPlateImage';
          $vehicleNoPlateImagecheck= $this->InsertImageQry($asnNumber,$fileName,$fileUrl,$docType,$userId,$clientId,$warehouseId);
        
        }else{
          $vehicleNoPlateImagecheck=false;
        }
    
      if($request->hasFile('materialLoadedone')){
            $materialLoadedone=  $request->materialLoadedone;
            $imageName= $asnNumber.'-materialLoadedone-'.date('YmdHis').$materialLoadedone->getClientOriginalExtension();

            $folder = public_path('/SWIM/Inward/Vehicle/'.$asnNumber.'/');
            if (!Storage::exists($folder)) {
                Storage::makeDirectory($folder, 0775, true, true);
            }

            $materialLoadedone-> move($folder, $imageName);
            $fileUrl=public_path($folder.$imageName);
            $fileName= $imageName;
            $docType='materialLoadedone';

          $checkMlone= $this->InsertImageQry($asnNumber,$fileName,$fileUrl,$docType,$userId,$clientId,$warehouseId);
         
        }else{
          $checkMlone=false;
        }
    
        if($request->hasFile('materialLoadedtwo')){

          $materialLoadedtwo=  $request->materialLoadedtwo;
            $mtwoName= $asnNumber.'-materialLoadedtwo-'.date('YmdHis').$materialLoadedtwo->getClientOriginalExtension();

            $folder = public_path('/SWIM/Inward/Vehicle/'.$asnNumber.'/');
            if (!Storage::exists($folder)) {
                Storage::makeDirectory($folder, 0775, true, true);
            }

            $materialLoadedtwo-> move($folder, $mtwoName);
            $fileUrl=public_path($folder.$mtwoName);
            $fileName= $mtwoName;
            $docType='materialLoadedtwo';
          $checkMltwo= $this->InsertImageQry($asnNumber,$fileName,$fileUrl,$docType,$userId,$clientId,$warehouseId);
        }else{
          $checkMltwo=false;
        }
    
    
       
        if($request->hasFile('materialLoadedthree')){
            $materialLoadedthree=  $request->materialLoadedthree;
            $materialthreeName= $asnNumber.'-materialLoadedthree-'.date('YmdHis').$materialLoadedthree->getClientOriginalExtension();

            $folder = public_path('/SWIM/Inward/Vehicle/'.$asnNumber.'/');
            if (!Storage::exists($folder)) {
                Storage::makeDirectory($folder, 0775, true, true);
            }

            $materialLoadedthree-> move($folder, $materialthreeName);
            $fileUrl=public_path($folder.$materialthreeName);

            $fileName= $materialthreeName;
            $docType='materialLoadedthree';
          $checkMlthree= $this->InsertImageQry($asnNumber,$fileName,$fileUrl,$docType,$userId,$clientId,$warehouseId);
        
        }else{
          $checkMlthree=false;
        }



        if($checkMlone ==true &&  $checkMlthree == true && $checkMltwo == true && $vehicleImagecheck==true && $vehicleNoPlateImagecheck==true && $vehicleSealImagecheck==true)
        {
          
         $flagUppdated= $this->preMRNupdationFlag($warehouseId,$clientId,$asnNumber,$userId);
         if($flagUppdated ==true){
          $jResponse=[ "success"=>"Sucessfully Uploaded",'status'=>'200'];
          return \Response::json($jResponse,200);
         }else{
          $jResponse=["error"=>"Somthings Went Wrong--1",'status'=>'401'];
          return \Response::json($jResponse,401);
         }
        
        }else{
            $jResponse=["error"=>"Somthings Went Wrong",'status'=>'401'];
            return \Response::json($jResponse,401);
        }

    }catch(Exception $ex){
     $msg=['error'=>'files are incompleted','status'=>'500'];
    return response()->json($msg);
    }

}
  else{
    $msg=['error'=>'files are incompleted','status'=>'500'];
    return response()->json($msg);
  }



   ## 
    // $jResponse= array();
    // $jResponse['status']=200;
    // $jResponse['message']='Files Uploaded Successfully !';
    // $path = \Storage::cloud()->put('files',$request->file('files'));
    // $url= \Storage::cloud()->temporaryUrl($path, \carbon\Carbon::now()->addMinutes(1));
    // $jResponse['data']=[ "url"=> $url, "path"=>$path ];
    // return \Response::json($jResponse,200);
    // $msg=['error'=>'files are incompleted','status'=>'404'];
    // return response()->json($msg);

}







public function InsertImageQry($asnNumber,$fileName,$fileUrl,$docType,$userId,$clientId,$warehouseId){

    $createDateTime=date('Y-m-d H:i:s');
    //$docType='';

   $queryExc= \DB::table('gDrive_Data')->insert(['tranId'=> $asnNumber,'masterFolder' =>'SWIM','subFolder'=>'SKU MASTER' ,'fileName'=>$fileName ,'createTime'=>$createDateTime ,'flag'=>'POST' ,'file_url'=>$fileUrl,'docType'=>$docType,'userName'=>$userId,'WhID'=>$warehouseId,'custid'=>$clientId]);
   if( $queryExc){
    return true;
   }else{
    return false;
   }
   
   

}


 ##Search Received ASN ##

 public function asnSearchImages(Request $request){

  if(!empty($request->warehouseId) &&  !empty($request->clientId)) {

      #Search Key Based on ASNNUMBER AND 
      if (!empty($request->searchScanValue)) 
      {
      $Rquery="SELECT P_MRN_No AS ASN_No,CustInv as InvoiceNo,VENINVOICE as VendorInvoice,convert(date,CustInvDate) as InvoiceDate,PONO FROM PreMRN_Upload WITH(NOLOCK) WHERE   WH='$request->warehouseId' AND CustID='$request->clientId' AND ISNULL(IM_UPLOAD,'') ='' and  ( CustInv='$request->searchScanValue' or P_MRN_No='$request->searchScanValue') and convert(date,p_mrn_date)>'2024-03-31' group by P_MRN_No ,CustInv,VENINVOICE ,convert(date,CustInvDate), PONO";
      }else{
      $Rquery  = "SELECT P_MRN_No AS ASN_No,CustInv as InvoiceNo,VENINVOICE as VendorInvoice,  convert(date,CustInvDate) as InvoiceDate,PONO FROM PreMRN_Upload WITH(NOLOCK) WHERE   WH='$request->warehouseId' AND CustID='$request->clientId' AND ISNULL(IM_UPLOAD,'') ='' and convert(date,p_mrn_date)>'2024-03-31' group by P_MRN_No ,CustInv,VENINVOICE ,convert(date,CustInvDate),PONO";
      }
       
     
      $QData =  \DB::select($Rquery);
      
      if(!empty($QData)){
      
      $notices = $this->arrayPaginator($QData, $request->page,$request);
      return response()->json($notices);
      }else{
      $Data=['message'=>"Somethings went wrong","status"=>"401"];
      }
     
      //print_r($QData);
     // $Data = ['message'=>"Data Fetched Successfully","status"=>"200","data"=>$QData]; 
      

   }
   else{
    $Data=['message'=>"Somethings went wrong","status"=>"401"];
  }

  return response()->json($Data);
 

}

############

public function uploadsInvoiceDocuments(Request $request){
 //images storing in minio //
  
 $data =$request->validate([
  'warehouseId' =>'required',
  'clientId' =>'required',
  'userId'=>'required',
  'invoicedocuments'=> 'required',
  // 'lrdocuments'=>'required',
  //'otherdocumentsurl'=>'required'
]);

  $warehouseId=   $request->warehouseId;
  $clientId=   $request->clientId;
  $userId=   $request->userId;
  $invoiceDocuments =  $request->invoicedocuments;
  $lrDocuments=  $request->lrdocuments;
  $otherDocuments = $request->otherdocuments;
  $asnNumber=$request->asnNumber;

 

      if(!empty($warehouseId) && !empty($clientId) && !empty($userId)  && !empty($asnNumber) && !empty($invoiceDocuments) ){

      try{

      if($request->hasFile('invoicedocuments')){

      $invoiceDocuments=  $request->invoicedocuments;
      $invoiceDocName= 'invoicedocuments-'.date('YmdHis').$invoiceDocuments->getClientOriginalExtension();

      $folder = public_path('/SWIM/Inward/Documents/Invoice/'.$asnNumber.'/');
      if (!Storage::exists($folder)) {
          Storage::makeDirectory($folder, 0775, true, true);
      } 

      $invoiceDocuments-> move( $folder, $invoiceDocName);
      $fileUrl=$folder.$invoiceDocName;
      $fileName= $invoiceDocName;

      $docType='InvoiceDoc';

      $invoiceCheck= $this->InsertDocumentQry($asnNumber,$fileName,$fileUrl,$docType,$userId,$clientId,$warehouseId);

      }else{
      $invoiceCheck=false;
      }

      if($request->hasFile('lrdocuments')){

      $lrdocumentsImg=  $request->lrdocuments;
      $vsealImage= 'lrdocuments-'.date('YmdHis').$lrdocumentsImg->getClientOriginalExtension();

      $folder = public_path('/SWIM/Inward/Documents/Invoice/'.$asnNumber.'/');
      if (!Storage::exists($folder)) {
          Storage::makeDirectory($folder, 0775, true, true);
      }


      $lrdocumentsImg-> move($folder, $vsealImage);
      $fileUrl=public_path($folder.$vsealImage);
      $fileName= $vsealImage;
      $docType='lrdocumentsImg';

      $lrImgcheck= $this->InsertDocumentQry($asnNumber,$fileName,$fileUrl,$docType,$userId,$clientId,$warehouseId);

      }else{
      $lrImgcheck=false;
      }

    if($request->hasFile('otherdocuments')){
      $otherdocImg=  $request->otherdocuments;
      $vPlateName= 'otherdocuments-'.date('YmdHis').$otherdocImg->getClientOriginalExtension();

    
      $folder = public_path('/SWIM/Outward/Documents/Invoice/'.$asnNumber.'/');
      if (!Storage::exists($folder)) {
          Storage::makeDirectory($folder, 0775, true, true);
      }

      $otherdocImg-> move($folder , $vPlateName);
      $fileUrl=public_path($folder.$vPlateName);
      $fileName= $vPlateName;
      $docType='otherdocImg';
    $otherdocImgcheck= $this->InsertDocumentQry($asnNumber,$fileName,$fileUrl,$docType,$userId,$clientId,$warehouseId);

    }else{
    $otherdocImgcheck=false;
    }


    if($invoiceCheck ==true  || $otherdocImgcheck==true || $lrImgcheck==true)
    {

     
     $flagUpt= $this->updationFlag($warehouseId,$clientId,$asnNumber,$userId) ;

 
     if($flagUpt ==true){
      $jResponse=[ "success"=>"Sucessfully Uploaded",'status'=>'200'];
      return \Response::json($jResponse,200);
     }else{
      $jResponse=["error"=>"Somthings Went Wrong",'status'=>'401'];
      return \Response::json($jResponse,401);
     }
   
    }else{
    $jResponse=["error"=>"Somthings Went Wrong",'status'=>'401'];
    return \Response::json($jResponse,401);
    }

}catch(Exception $ex){
$msg=['error'=>'files are incompleted','status'=>'500'];
return response()->json($msg);
}

}
else{
$msg=['error'=>'files are incompleted','status'=>'500'];
return response()->json($msg);
}


}
######

public function InsertDocumentQry($asnNumber,$fileName,$fileUrl,$docType,$userId,$clientId,$warehouseId){

  $createDateTime=date('Y-m-d H:i:s');
  //$docType='';

 $queryExc= \DB::table('gDrive_Data')->insert(['tranId'=> $asnNumber,'masterFolder' =>'SWIM','subFolder'=>'SKU MASTER' ,'fileName'=>$fileName ,'createTime'=>$createDateTime ,'flag'=>'POST' ,'file_url'=>$fileUrl,'docType'=>$docType,'userName'=>$userId,'WhID'=>$warehouseId,'custid'=>$clientId]);

 if( $queryExc){


  // $UpdateFlag="update PreMRN_Upload WITH(TABLOCK) set IMFLAG='Y' WHERE WH='$warehouseId' AND CustID='$clientId' AND P_MRN_No='$asnNumber'";


  return true;

 }else{
  return false;
 }
 
 

}
###
public function arrayPaginator($array, $page, $request)
{
    
    $page = $page;
    $perPage = 10;
    $offset = ($page * $perPage) - $perPage;

    return new  \Illuminate\Pagination\LengthAwarePaginator(array_slice($array, $offset, $perPage, true), count($array), $perPage, $page,
        ['path' => $request->url(), 'query' => $request->query()]);
}

#################

 public function asnInwardReceiving(Request $request){


  if(!empty($request->warehouseId) &&  !empty($request->clientId)) {
   #Search Key Based on ASNNUMBER AND 
    if (!empty($request->searchScanValue)) 
    {
    $Rquery="SELECT MRN_No AS SRN_NO,CUSTINV AS INVOICE,RECEIPTFROM AS PARTYNAME FROM premrn_upload WITH(NOLOCK) WHERE WH='$request->warehouseId' AND Custid='$request->clientId' AND mrn_no<>'' AND (custinv='$request->searchScanValue' OR MRN_No='$request->searchScanValue') AND ISNULL(DOCUPLOAD,'')='' AND CONVERT(DATE,ENTRYON)>'2024-03-31' GROUP BY MRN_No ,CUSTINV,RECEIPTFROM ORDER BY  MRN_No ASC";
    }else{
    $Rquery  = "SELECT MRN_No AS SRN_NO,CUSTINV AS INVOICE,RECEIPTFROM AS PARTYNAME FROM premrn_upload WITH(NOLOCK) WHERE WH='$request->warehouseId' AND Custid='$request->clientId' AND mrn_no<>'' AND CONVERT(DATE,ENTRYON)>'2024-03-31'  AND ISNULL(DOCUPLOAD,'')='' GROUP BY MRN_No ,CUSTINV,RECEIPTFROM ORDER BY  MRN_No ASC";
    }

    $QData =  \DB::select($Rquery);
    $notices = $this->arrayPaginator($QData, $request->page,$request);
    //print_r($QData);
   // $Data = ['message'=>"Data Fetched Successfully","status"=>"200","data"=>$QData]; 
    return response()->json($notices);

 }
 else{
  $Data=['message'=>"Somethings went wrong","status"=>"401"];
}

}





### UPDATION FLAG #####
public function preMRNupdationFlag($warehouseId,$clientId,$asnNumber,$userId){

  $qry="update PreMRN_Upload WITH(TABLOCK) set IMFLAG='Y',IMENTRYON=getdate(),IMENTRYBY='$userId' WHERE WH='$warehouseId' AND Custid='$clientId' AND p_mrn_no='$asnNumber'";
  $qryExc =  \DB::update($qry);
  
  if( $qryExc){
    return true;
   }else{
    return false;
   }
   

}

### UPDATION FLAG #####
public function updationFlag($warehouseId,$clientId,$asnNumber,$userId){

  $qry="update tbl_Inward WITH(TABLOCK) set MRNACTIVITY='Y',MRNACTIVITYDATE=getdate(),MRNACTIVITYBY='$userId' WHERE WH='$warehouseId' AND Cust='$clientId' AND MRNNo='$asnNumber'";
  $qryExc =  \DB::update($qry);
  
  if( $qryExc){
    return true;
   }else{
    return false;
   }
   

}

#####OUT BOUND SEARCH ##################



 ##Search Received ASN ##

 public function OutboundSearchImages(Request $request){
 
   if(!empty($request->warehouseId) &&  !empty($request->clientId)) {
      //DN-MAA-0000008
  
       #Search Key Based on ASNNUMBER AND 
       if (!empty($request->searchScanValue)) 
       {
       $Rquery="select  MDN_No,CustInv,convert(date,MDN_Date) as MDNDate from tbl_mdn WITH(NOLOCK) where WH='$request->warehouseId' AND CustID='$request->clientId' AND mdnpost='Y' AND ISNULL(imflag,'')=''  AND ( MDN_No='$request->searchScanValue' OR CUSTINV='$request->searchScanValue') GROUP BY MDN_No,CustInv,convert(date,MDN_Date)   ORDER BY convert(date,MDN_Date) ASC";
       }else{
       $Rquery  = "SELECT  MDN_No,CustInv,convert(date,MDN_Date) as MDNDate from tbl_mdn WITH(NOLOCK) where WH='$request->warehouseId' AND CustID='$request->clientId' AND mdnpost='Y' AND ISNULL(imflag,'')='' GROUP BY MDN_No,CustInv,convert(date,MDN_Date) ORDER BY convert(date,MDN_Date) ASC";
       
       }
 
       $QData =  \DB::select($Rquery);
       $notices = $this->arrayPaginator($QData, $request->page,$request);
       //print_r($QData);
      // $Data = ['message'=>"Data Fetched Successfully","status"=>"200","data"=>$QData]; 
       return response()->json($notices);
 
    }
    else{
     $Data=['message'=>"Somethings went wrong","status"=>"401"];
   }
 }
 

 ######OUT BOUND  UPLOAD######

public function uploadsOutboundDocuments(Request $request){
  //images storing in minio //
   
  $data =$request->validate([
   'warehouseId' =>'required',
   'clientId' =>'required',
   'userId'=>'required',
   'invoicedocuments'=> 'required',
 
 ]);

 
 
   $warehouseId=   $request->warehouseId;
   $clientId=   $request->clientId;
   $userId=   $request->userId;
   $invoiceDocuments =  $request->invoicedocuments;
   $lrDocuments=  $request->lrdocuments;
   $otherDocuments = $request->otherdocuments;
   $asnNumber=$request->asnNumber;
 
 //    $fileUrl=$fileUrl;
 //    $fileName=$fileName;
 // $array=["warehouseId"=>$warehouseId,"clientId"=>$clientId,""]
 
       if(!empty($warehouseId) && !empty($clientId) &&
        !empty($userId)  && !empty($asnNumber) && !empty($invoiceDocuments) ){
 
       try{
 
  ##INVOICE DOCUMENT
       if($request->hasFile('invoicedocuments')){
 
       $invoiceDocuments=  $request->invoicedocuments;
       $invoiceDocName= $asnNumber.'-invoicedocuments-'.date('YmdHis').$invoiceDocuments->getClientOriginalExtension();

       $folder = public_path('/SWIM/Outward/Documents/Invoice/'.$asnNumber.'/');
      if (!Storage::exists($folder)) {
          Storage::makeDirectory($folder, 0775, true, true);
      }

       $invoiceDocuments-> move($folder, $invoiceDocName);
       $fileUrl=public_path($folder.$invoiceDocName);
       $fileName= $invoiceDocName;
 
       $docType='InvoiceDoc';
 
       $invCheck= $this->InsertOutwardsDocumentQry($asnNumber,$fileName,$fileUrl,$docType,$userId,$clientId,$warehouseId);
 
       }else{
       $invCheck=false;
       }
    
   ##LR DOCUMENT
       if($request->hasFile('lrdocuments')){
 
       $lrdocumentsImg=  $request->lrdocuments;
       $vsealImage= $asnNumber.'-lrdocuments-'.date('YmdHis').$lrdocumentsImg->getClientOriginalExtension();

       $folder = public_path('/SWIM/Outward/Documents/Invoice/'.$asnNumber.'/');
       if (!Storage::exists($folder)) {
           Storage::makeDirectory($folder, 0775, true, true);
       }

       
       $lrdocumentsImg-> move($folder, $vsealImage);
       $fileUrl=public_path($folder.$vsealImage);

       $fileName= $vsealImage;
       $docType='lrdocumentsImg';
 
       $lrcheck= $this->InsertOutwardsDocumentQry($asnNumber,$fileName,$fileUrl,$docType,$userId,$clientId,$warehouseId);
 
       }else{
       $lrcheck=false;
       }
     
  ##OTHERS DOCUMENT
     if($request->hasFile('otherdocuments')){
       $otherdocImg=  $request->otherdocuments;
       $vPlateName= $asnNumber.'-otherdocuments-'.date('YmdHis').$otherdocImg->getClientOriginalExtension();

       $folder = public_path('/SWIM/Outward/Documents/Invoice/'.$asnNumber.'/');
       if (!Storage::exists($folder)) {
           Storage::makeDirectory($folder, 0775, true, true);
       }

       $otherdocImg-> move($folder, $vPlateName);
       $fileUrl=public_path($folder.$vPlateName);
       $fileName= $vPlateName;
       $docType='otherdocImg';
     $otherimgcheck= $this->InsertOutwardsDocumentQry($asnNumber,$fileName,$fileUrl,$docType,$userId,$clientId,$warehouseId);
 
     }else{
     $otherimgcheck=false;
     }
   
     if($invCheck ==true  || $otherimgcheck==true || $lrcheck==true)
     {
   
     
      $flagUpt= $this->OutwardUpdationFlag($warehouseId,$clientId,$asnNumber,$userId) ;
     
      if($flagUpt == true){
       $jResponse=[ "success"=>"Sucessfully Uploaded",'status'=>'200'];
       return \Response::json($jResponse,200);
      }else{
       $jResponse=["error"=>"Somthings Went Wrong",'status'=>'401'];
       return \Response::json($jResponse,401);
      }

     }else{
     $jResponse=["error"=>"Somthings Went Wrong",'status'=>'401'];
     return \Response::json($jResponse,401);
     }
 
 }catch(Exception $ex){
 $msg=['error'=>'files are incompleted','status'=>'500'];
 return response()->json($msg);
 }
 
 }
 else{
 $msg=['error'=>'files are incompleted','status'=>'500'];
 return response()->json($msg);
 }
 
 
 }

 ### UPDATION FLAG #####
public function OutwardUpdationFlag($warehouseId,$clientId,$asnNumber,$userId){

  $date=date('Y-m-d');
  $qry= "UPDATE tbl_MDN WITH(TABLOCK) SET mdnactivity='Y',mdnactivityby='$userId',mdnactivitydate='$date' WHERE WH='$warehouseId' AND CustID='$clientId' AND MDN_No='$asnNumber'";

  
  $qryExc =  \DB::update($qry);

  if($qryExc){
    return true;
   }else{
    return false;
   }
   

}
 ####

public function InsertOutwardsDocumentQry($asnNumber,$fileName,$fileUrl,$docType,$userId,$clientId,$warehouseId){

  $createDateTime=date('Y-m-d H:i:s');
  //$docType='';
 $queryExc= \DB::table('gDrive_Data')->insert(['tranId'=> $asnNumber,'masterFolder' =>'SWIM','subFolder'=>'SKU MASTER' ,'fileName'=>$fileName ,'createTime'=>$createDateTime ,'flag'=>'POST' ,'file_url'=>$fileUrl,'docType'=>$docType,'userName'=>$userId,'WhID'=>$warehouseId,'custid'=>$clientId]);

  if( $queryExc){
  return true;
  }else{
  return false;
  }

}
#######OUT VEHICLE ####
public function VehicleOutInvoiceSearch(Request $request){

  if(!empty($request->warehouseId) &&  !empty($request->clientId)) {
     
   
    #Search Key Based on ASNNUMBER AND 
    if (!empty($request->searchScanValue)) 
    {
    $Rquery="select  MDN_No,CustInv,convert(date,MDN_Date) as MDNDate from tbl_mdn WITH(NOLOCK) where WH='$request->warehouseId' AND CustID='$request->clientId' AND mdnpost='Y' AND ISNULL(engname,'')=''  AND ( MDN_No='$request->searchScanValue' OR CUSTINV='$request->searchScanValue') and convert(date,mdn_date) > '2024-03-31' GROUP BY MDN_No,CustInv,convert(date,MDN_Date)   ORDER BY convert(date,MDN_Date) ASC";
    }else{
    $Rquery  = "select MDN_No,CustInv,convert(date,MDN_Date) as MDNDate from tbl_mdn WITH(NOLOCK) where WH='$request->warehouseId' AND CustID='$request->clientId' AND mdnpost='Y' AND ISNULL(engname,'')='' and convert(date,mdn_date) > '2024-03-31' GROUP BY MDN_No,CustInv,convert(date,MDN_Date) ORDER BY convert(date,MDN_Date) ASC";
    }

    $QData =  \DB::select($Rquery);
    
    $notices = $this->arrayPaginator($QData, $request->page,$request);
    //print_r($QData);
   // $Data = ['message'=>"Data Fetched Successfully","status"=>"200","data"=>$QData]; 
    return response()->json($notices);

 }
 else{
  $Data=['message'=>"Somethings went wrong","status"=>"401"];
}

}

##########
public function UploadsOutVehiclesDocuments(Request $request){

  
  //images storing in minio //

        $data =$request->validate([
          'warehouseId' =>'required',
          'clientId' =>'required',
          'userId'=>'required',
          'vehicleImage'=> 'required',
          'vehicleSealImage'=>'required',
          'vehicleNoPlateImage'=>'required',
          'materialLoadedone'=>'required',
          'materialLoadedtwo'=>'required',
          'materialLoadedthree'=>'required'
        ]);
  // print_r( $data);
  // die;

 $warehouseId=   $request->warehouseId;
 $clientId=   $request->clientId;
 $userId=   $request->userId;
 $vehicleImage =  $request->vehicleImage;
 $vehicleSealImage=  $request->vehicleSealImage;
 $vehicleNoPlateImage = $request->vehicleNoPlateImage;
 $materialLoadedone=  $request->materialLoadedone;
 $materialLoadedtwo=  $request->materialLoadedtwo;
 $materialLoadedthree=  $request->materialLoadedthree;
 $asnNumber=$request->asnNumber;
 
//    $fileUrl=$fileUrl;
//    $fileName=$fileName;
// $array=["warehouseId"=>$warehouseId,"clientId"=>$clientId,""]

if(!empty($warehouseId) && !empty($clientId) && !empty($userId)  && !empty($asnNumber) && !empty($materialLoadedthree) && !empty($materialLoadedtwo) && !empty($materialLoadedone)  && !empty($vehicleNoPlateImage)  && !empty($vehicleSealImage) && !empty($vehicleImage) ){
 
  try{
      if($request->hasFile('vehicleImage')){
        
          $vehicleImage=  $request->vehicleImage;
          $vehicleImagename= $asnNumber.'-vehicleImage-'.date('YmdHis').$vehicleImage->getClientOriginalExtension();


          $folder = public_path('/SWIM/Outward/Vehicle/'.$asnNumber.'/');
          if (!Storage::exists($folder)) {
              Storage::makeDirectory($folder, 0775, true, true);
          }
        

          $vehicleImage->move($folder, $vehicleImagename);
          $fileUrl=public_path($folder.$vehicleImagename);


          $fileName= $vehicleImagename;
       
          $docType='vehicleImage';

          $vehicleImagecheck= $this->OutVehicleImageQry($asnNumber,$fileName,$fileUrl,$docType,$userId,$clientId,$warehouseId);
        
      }else{
        $vehicleImagecheck=false;
      }
      
      if($request->hasFile('vehicleSealImage')){
        
          $vehicleSealImage=  $request->vehicleSealImage;
          $vsealImage= $asnNumber.'-vehicleSealImage-'.date('YmdHis').$vehicleSealImage->getClientOriginalExtension();

          $folder = public_path('/SWIM/Outward/Vehicle/'.$asnNumber.'/');
          if (!Storage::exists($folder)) {
              Storage::makeDirectory($folder, 0775, true, true);
          }


          $vehicleSealImage-> move( $folder, $vsealImage);
          $fileUrl=public_path($folder.$vsealImage);

          $fileName= $vsealImage;
          $docType='vehicleSealImage';
        $vehicleSealImagecheck= $this->OutVehicleImageQry($asnNumber,$fileName,$fileUrl,$docType,$userId,$clientId,$warehouseId);
       
      }else{
        $vehicleSealImagecheck=false;
      }
    
      if($request->hasFile('vehicleNoPlateImage')){
          $vehicleNoPlateImage=  $request->vehicleNoPlateImage;
          $vPlateName= $asnNumber.'-vehicleNoPlateImage-'.date('YmdHis').$vehicleNoPlateImage->getClientOriginalExtension();

          $folder = public_path('/SWIM/Outward/Vehicle/'.$asnNumber.'/');
          if (!Storage::exists($folder)) {
              Storage::makeDirectory($folder, 0775, true, true);
          }


          $vehicleNoPlateImage-> move( $folder, $vPlateName);
          $fileUrl=public_path($folder.$vPlateName);
          $fileName= $vPlateName;
          $docType='vehicleNoPlateImage';
        $vehicleNoPlateImagecheck= $this->OutVehicleImageQry($asnNumber,$fileName,$fileUrl,$docType,$userId,$clientId,$warehouseId);
      
      }else{
        $vehicleNoPlateImagecheck=false;
      }
  
    if($request->hasFile('materialLoadedone')){
          $materialLoadedone=  $request->materialLoadedone;
          $imageName= $asnNumber.'-materialLoadedone-'.date('YmdHis').$materialLoadedone->getClientOriginalExtension();

          $folder = public_path('/SWIM/Outward/Vehicle/'.$asnNumber.'/');
          if (!Storage::exists($folder)) {
              Storage::makeDirectory($folder, 0775, true, true);
          }


          $materialLoadedone-> move($folder, $imageName);
          $fileUrl=public_path($folder.$imageName);
          $fileName= $imageName;
          $docType='materialLoadedone';

        $checkMlone= $this->OutVehicleImageQry($asnNumber,$fileName,$fileUrl,$docType,$userId,$clientId,$warehouseId);
       
      }else{
        $checkMlone=false;
      }
  
      if($request->hasFile('materialLoadedtwo')){

        $materialLoadedtwo=  $request->materialLoadedtwo;
          $mtwoName= $asnNumber.'-materialLoadedtwo-'.date('YmdHis').$materialLoadedtwo->getClientOriginalExtension();

          $folder = public_path('/SWIM/Outward/Vehicle/'.$asnNumber.'/');
          if (!Storage::exists($folder)) {
              Storage::makeDirectory($folder, 0775, true, true);
          }


          $materialLoadedtwo-> move($folder, $mtwoName);
          $fileUrl=public_path($folder.$mtwoName);

          $fileName= $mtwoName;
          $docType='materialLoadedtwo';
        $checkMltwo= $this->OutVehicleImageQry($asnNumber,$fileName,$fileUrl,$docType,$userId,$clientId,$warehouseId);
      }else{
        $checkMltwo=false;
      }
  
  
     
      if($request->hasFile('materialLoadedthree')){
          $materialLoadedthree=  $request->materialLoadedthree;
          $materialthreeName= $asnNumber.'-materialLoadedthree-'.date('YmdHis').$materialLoadedthree->getClientOriginalExtension();

          $folder = public_path('/SWIM/Outward/Vehicle/'.$asnNumber.'/');
          if (!Storage::exists($folder)) {
              Storage::makeDirectory($folder, 0775, true, true);
          }


          $materialLoadedthree-> move( $folder, $materialthreeName);
          $fileUrl=public_path( $folder.$materialthreeName);
          $fileName= $materialthreeName;
          $docType='materialLoadedthree';
        $checkMlthree= $this->OutVehicleImageQry($asnNumber,$fileName,$fileUrl,$docType,$userId,$clientId,$warehouseId);
      
      }else{
        $checkMlthree=false;
      }

      if($checkMlone ==true &&  $checkMlthree == true && $checkMltwo == true && $vehicleImagecheck==true && $vehicleNoPlateImagecheck==true && $vehicleSealImagecheck==true)
      {
        
       $flagUppdated= $this->OutMDNVehicleupdationFlag($warehouseId,$clientId,$asnNumber);
       if($flagUppdated ==true){
        $jResponse=[ "success"=>"Sucessfully Uploaded",'status'=>'200'];
        return \Response::json($jResponse,200);
       }else{
        $jResponse=["error"=>"Somthings Went Wrong--1",'status'=>'401'];
        return \Response::json($jResponse,401);
       }
      
      }else{
          $jResponse=["error"=>"Somthings Went Wrong",'status'=>'401'];
          return \Response::json($jResponse,401);
      }

  }catch(Exception $ex){
   $msg=['error'=>'files are incompleted','status'=>'500'];
  return response()->json($msg);
  }

}
else{
  $msg=['error'=>'files are incompleted','status'=>'500'];
  return response()->json($msg);
}

 ## 
  // $jResponse= array();
  // $jResponse['status']=200;
  // $jResponse['message']='Files Uploaded Successfully !';
  // $path = \Storage::cloud()->put('files',$request->file('files'));
  // $url= \Storage::cloud()->temporaryUrl($path, \carbon\Carbon::now()->addMinutes(1));
  // $jResponse['data']=[ "url"=> $url, "path"=>$path ];
  // return \Response::json($jResponse,200);
  // $msg=['error'=>'files are incompleted','status'=>'404'];
  // return response()->json($msg);

}
#####

public function OutVehicleImageQry($asnNumber,$fileName,$fileUrl,$docType,$userId,$clientId,$warehouseId){

  $createDateTime=date('Y-m-d H:i:s');
  //$docType='';

 $queryExc= \DB::table('gDrive_Data')->insert(['tranId'=> $asnNumber,'masterFolder' =>'SWIM','subFolder'=>'Outward' ,'fileName'=>$fileName ,'createTime'=>$createDateTime ,'flag'=>'POST' ,'file_url'=>$fileUrl,'docType'=>$docType,'userName'=>$userId,'WhID'=>$warehouseId,'custid'=>$clientId]);
 if( $queryExc){
  return true;
 }else{
  return false;
 }
 
 

}

#######
public function OutMDNVehicleupdationFlag($warehouseId,$clientId,$mdnNumber){

  $qry="UPDATE tbl_MDN WITH(TABLOCK) SET VEHIMG='Y',VEHIMAGEBY='',VEHIMGON='' WHERE WH='$warehouseId' AND CustID='$clientId' AND MDN_No='$mdnNumber'";

  $qryExc =  \DB::update($qry);
  
  if( $qryExc){
    return true;
   }else{
    return false;
   }
   

}

## UPDATION FLAG #####
public function OutVehicleupdationFlag($warehouseId,$clientId,$mrnNumber){

 
  $qry="update PreMRN_Upload WITH(TABLOCK) set IMFLAG='Y' WHERE WH='$warehouseId' AND CustID='$clientId' AND P_MRN_No='$mrnNumber'";
  $qryExc =  \DB::update($qry);
  
  if( $qryExc){
    return true;
   }else{
    return false;
   }
   

}
 ### #### ##### ####

 
public function getBinContent(Request $request){

  $warehouseId =$request->warehouseId;
  $CustomerId= $request->clientId;
  $whBox =$request->whBox;
  $skuId= $request->skuName;

  
  $query = "select WHLOCATION ,SUM(BALQTY) AS BALQTY from Tbl_putway with(NOLOCK) WHERE WH='$warehouseId' AND CUST='$CustomerId' AND BALQTY>0 AND WHLOCATION<>'$whBox' AND SKU='$skuId' GROUP BY WHLOCATION";

  $results =  \DB::select($query);
  $pendingPickListData = $this->arrayPaginator($results, $request->page,$request);
         
  if(count($pendingPickListData)>0){
      return response()->json($pendingPickListData);
  }else{
      return response()->json(['error' => 'NorFound']);
  } 

  // if (count($pendingPickListData)>0)
  // {
  //   $jResponse=["success"=>"Data Fetched Succesfully",'status'=>'200','data'=>$pendingPickListData];
  // }else{
  //   $jResponse=["error"=>"Somthings Went Wrong",'status'=>'401']; 
  // }
  // return response()->json($jResponse);

}








}
