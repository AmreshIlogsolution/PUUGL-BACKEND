<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
 
use Illuminate\Support\Facades\Storage;
use DB;
class ImpageUploadOutwardDocumentController extends Controller
{     
    public function uploadsOutboundDocumentsAzure(Request $request){           
        
        // try {
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
            // Generate a unique filename for the uploaded image
          
        if($request->hasFile('invoicedocuments')){            
              
            $vehicleImage=  $request->invoicedocuments;
            $vehicleImagename= date('YmdHis').'.'.$vehicleImage->getClientOriginalExtension();       
            $size = ['900x300'];
            $docType='Invoice';
            $path = $clientId.'/'.$asnNumber.'/';
          //  $uploadedImageName = $this->resizeAndSaveToAzureBlob($vehicleImage,$size,$vehicleImagename,$clientId,$asnNumber,$docType);
            Storage::disk('azure')->put($path.$vehicleImagename, file_get_contents($vehicleImage));
            $fileUrl = env('AZURE_STORAGE_URL').env('AZURE_STORAGE_CONTAINER').'/'.$clientId.'/'.$asnNumber.'/'.$vehicleImagename;
            $vehicleImageCheck= $this->InsertImageQry($asnNumber,$vehicleImagename,$fileUrl,$docType,$userId,$clientId,$warehouseId);
        } 
            
        if($request->hasFile('lrdocuments')){               
                $vehicleImage=  $request->lrdocuments;
                $vehicleImagename= date('YmdHis').'.'.$vehicleImage->getClientOriginalExtension();       
                $size = ['900x300'];
                $docType='LR';
                $path = $clientId.'/'.$asnNumber.'/';
                //$uploadedImageName = $this->resizeAndSaveToAzureBlob($vehicleImage,$size,$vehicleImagename,$clientId,$asnNumber,$docType);
                Storage::disk('azure')->put($path.$vehicleImagename, file_get_contents($vehicleImage));
                $fileUrl = env('AZURE_STORAGE_URL').env('AZURE_STORAGE_CONTAINER').'/'.$clientId.'/'.$asnNumber.'/'.$vehicleImagename;
                $vehicleImageCheck= $this->InsertImageQry($asnNumber,$vehicleImagename,$fileUrl,$docType,$userId,$clientId,$warehouseId);
        } 
    
        if($request->hasFile('otherdocuments')){               
                $vehicleImage=  $request->otherdocuments;
                $vehicleImagename= date('YmdHis').'.'.$vehicleImage->getClientOriginalExtension();       
                $size = ['900x300'];
                $docType='Others';
                $path = $clientId.'/'.$asnNumber.'/';
              //  $uploadedImageName = $this->resizeAndSaveToAzureBlob($vehicleImage,$size,$vehicleImagename,$clientId,$asnNumber,$docType);
              Storage::disk('azure')->put($path.$vehicleImagename, file_get_contents($vehicleImage));
                $fileUrl = env('AZURE_STORAGE_URL').env('AZURE_STORAGE_CONTAINER').'/'.$clientId.'/'.$asnNumber.'/'.$vehicleImagename;
                $vehicleImageCheck= $this->InsertImageQry($asnNumber,$vehicleImagename,$fileUrl,$docType,$userId,$clientId,$warehouseId);
        }  
        $queryUpdate = "update tbl_mdn with(tablock) set imflag ='Y' WHERE wh='" . $request->warehouseId . "' and custid='" . $request->clientId . "' and mdn_no ='" . $request->asnNumber . "'";
    
        $updSuccess = DB::update($queryUpdate);

        if($updSuccess){
                return response()->json(['success' => 200]);
        }else{
                return response()->json(['error' => 'noReocrd']);
        }
            
        // } catch (\Exception $e) {
        //     // Log the message locally OR use a tool like Bugsnag/Flare to log the error
        //     //Log::debug($e->getMessage());
        //     return response()->json(['error' => 'There was an error creating the user.'], 500);
            
        // } 
                
    }

    function resizeAndSaveToAzureBlob($file, $size, $imgName,$clientId,$asnNumber,$docType)
    {     
      
            $width=500;
            $height=300;
            $sourceImage = imagecreatefromstring(file_get_contents($file));
         
            // Get the original image dimensions
            $sourceWidth = imagesx($sourceImage);
            $sourceHeight = imagesy($sourceImage);
         
            // Calculate the aspect ratios of both images
            $sourceAspectRatio = $sourceWidth / $sourceHeight;
            $targetAspectRatio = $width / $height;
         
            // Calculate the new dimensions for the resized image
            if ($sourceAspectRatio > $targetAspectRatio) {
                $resizeWidth = $width;
                $resizeHeight = $width / $sourceAspectRatio;
            } else {
                $resizeHeight = $height;
                $resizeWidth = $height * $sourceAspectRatio;
            }
 
            // Creating white background
            $targetImage = imagecreatetruecolor($width, $height);
            $whiteColor = imagecolorallocate($targetImage, 255, 255, 255);
            imagefill($targetImage, 0, 0, $whiteColor);
         
            // Calculate the center position for the resized image on the new background
            $centerX = ($width - $resizeWidth) / 2;
            $centerY = ($height - $resizeHeight) / 2;
            // Resize the source image to the calculated dimensions
            imagecopyresampled(
                $targetImage,
                $sourceImage,
                $centerX,
                $centerY,
                0,
                0,
                $resizeWidth,
                $resizeHeight,
                $sourceWidth,
                $sourceHeight
            );
            $outputImagePath = 'resized_image.png';       

            imagepng($targetImage, $outputImagePath);     

            // Generate a unique filename for the resized image
            $resizedFilename = $docType. $imgName;   
            $path = $clientId.'/'.$asnNumber.'/';
            // Save the resized image to Azure Blob Storage
          Storage::disk('azure')->put($path.$resizedFilename, file_get_contents($outputImagePath));
          return $resizedFilename;
    }

    public function InsertImageQry($asnNumber,$fileName,$fileUrl,$docType,$userId,$clientId,$warehouseId)
    {
        $createDateTime=date('Y-m-d H:i:s');            
       
       $queryExc=  DB::table('gDrive_Data')->insert(['tranid'=> $asnNumber,'masterFolder' =>'SWIM','subFolder'=>'OUTWARD' ,'fileName'=>$fileName ,'createTime'=>$createDateTime ,'flag'=>'POST' ,'file_url'=>$fileUrl,'docType'=>$docType,'userName'=>$userId,'WhID'=>$warehouseId,'custid'=>$clientId]);
 
       if( $queryExc){
        return true;
       }else{
        return false;
       }
       
    }
}
