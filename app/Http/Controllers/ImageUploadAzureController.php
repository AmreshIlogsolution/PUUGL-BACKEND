<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use DB;
class ImageUploadAzureController extends Controller
{
    
    public function UploadsReceivedDocumentsAzure(Request $request){           
        
       
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
     
        $asnNumber=$request->asnNumber;
        $warehouseId=   $request->warehouseId;
        $clientId=   $request->clientId;
        $userId=   $request->userId;
        // Generate a unique filename for the uploaded image
        if($request->hasFile('vehicleImage')){               
            $vehicleImage=  $request->vehicleImage;
            $vehicleImagename= date('YmdHis').'.'.$vehicleImage->getClientOriginalExtension();       
            $size = ['900x300'];
            $docType='Image1';
            $path = $clientId.'/'.$asnNumber.'/';
            Storage::disk('azure')->put($path.$vehicleImagename, file_get_contents($vehicleImage));
            // $uploadedImageName = $this->resizeAndSaveToAzureBlob($vehicleImage,$size,$vehicleImagename,$clientId,$asnNumber,$docType);
            $fileUrl = env('AZURE_STORAGE_URL').env('AZURE_STORAGE_CONTAINER').'/'.$clientId.'/'.$asnNumber.'/'.$vehicleImagename;
            $vehicleImageCheck= $this->InsertImageQry($asnNumber,$vehicleImagename,$fileUrl,$docType,$userId,$clientId,$warehouseId);
        } 
            
        if($request->hasFile('vehicleSealImage')){               
            $vehicleSealImage=  $request->vehicleSealImage;
          
            $vehicleSealImagename= date('YmdHis').'.'.$vehicleSealImage->getClientOriginalExtension();       
             
            $size = ['900x300'];
            $docType='Image2';
            $path = $clientId.'/'.$asnNumber.'/';
            Storage::disk('azure')->put($path.$vehicleSealImagename, file_get_contents($vehicleSealImage));
            $fileUrl = env('AZURE_STORAGE_URL').env('AZURE_STORAGE_CONTAINER').'/'.$clientId.'/'.$asnNumber.'/'.$vehicleSealImagename;
            $vehicleImageCheck= $this->InsertImageQry($asnNumber,$vehicleSealImagename,$fileUrl,$docType,$userId,$clientId,$warehouseId);
        } 
    
        if($request->hasFile('vehicleNoPlateImage')){               
            $vehicleNoPlateImage=  $request->vehicleNoPlateImage;
            $vehicleNoPlateImagename= date('YmdHis').'.'.$vehicleNoPlateImage->getClientOriginalExtension();       
            $size = ['900x300'];
            $docType='Image3';
            $path = $clientId.'/'.$asnNumber.'/';
            Storage::disk('azure')->put($path.$vehicleNoPlateImagename, file_get_contents($vehicleNoPlateImage));
            $fileUrl = env('AZURE_STORAGE_URL').env('AZURE_STORAGE_CONTAINER').'/'.$clientId.'/'.$asnNumber.'/'.$vehicleNoPlateImagename;
            $vehicleImageCheck= $this->InsertImageQry($asnNumber,$vehicleNoPlateImagename,$fileUrl,$docType,$userId,$clientId,$warehouseId);
        } 
        if($request->hasFile('materialLoadedone')){               
            $materialLoadedoneImage=  $request->materialLoadedone;
            $materialLoadedonename= date('YmdHis').'.'.$materialLoadedoneImage->getClientOriginalExtension();       
            $size = ['900x300'];
            $docType='Image4';
            $path = $clientId.'/'.$asnNumber.'/';
            Storage::disk('azure')->put($path.$materialLoadedonename, file_get_contents($materialLoadedoneImage));               
            $fileUrl = env('AZURE_STORAGE_URL').env('AZURE_STORAGE_CONTAINER').'/'.$clientId.'/'.$asnNumber.'/'.$materialLoadedonename;
            $vehicleImageCheck= $this->InsertImageQry($asnNumber,$materialLoadedonename,$fileUrl,$docType,$userId,$clientId,$warehouseId);
        } 
        if($request->hasFile('materialLoadedtwo')){               
            $materialLoadedtwoImage=  $request->materialLoadedtwo;
            $materialLoadedtwoname= date('YmdHis').'.'.$materialLoadedtwoImage->getClientOriginalExtension();       
            $size = ['900x300'];
            $docType='Image5';
            $path = $clientId.'/'.$asnNumber.'/';
            Storage::disk('azure')->put($path.$materialLoadedtwoname, file_get_contents($materialLoadedtwoImage));                  
            $fileUrl = env('AZURE_STORAGE_URL').env('AZURE_STORAGE_CONTAINER').'/'.$clientId.'/'.$asnNumber.'/'.$materialLoadedtwoname;
            $vehicleImageCheck= $this->InsertImageQry($asnNumber,$materialLoadedtwoname,$fileUrl,$docType,$userId,$clientId,$warehouseId);
        } 
    
        if($request->hasFile('materialLoadedthree')){               
            $materialLoadedthreeImage=  $request->materialLoadedthree;
            $materialLoadedthreename= date('YmdHis').'.'.$materialLoadedthreeImage->getClientOriginalExtension();       
            $size = ['900x300'];
            $docType='Image6';
            $path = $clientId.'/'.$asnNumber.'/';
            Storage::disk('azure')->put($path.$materialLoadedthreename, file_get_contents($materialLoadedthreeImage));
            $fileUrl = env('AZURE_STORAGE_URL').env('AZURE_STORAGE_CONTAINER').'/'.$clientId.'/'.$asnNumber.'/'.$materialLoadedthreename;
            $vehicleImageCheck= $this->InsertImageQry($asnNumber,$materialLoadedthreename,$fileUrl,$docType,$userId,$clientId,$warehouseId);
        } 
            
        $queryUpdate = "update premrn_upload with(tablock) set im_upload='Y' WHERE wh='" . $request->warehouseId . "' and custid='" . $request->clientId . "' and P_MRN_NO ='" . $request->asnNumber . "'";
    
        $updSuccess = DB::update($queryUpdate);

        if($updSuccess){
            return response()->json(['success' => 200]);
        }else{
            return response()->json(['error' => 'noReocrd']);
        } 
                
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

            $aa =imagepng($targetImage, $outputImagePath);     
            return $aa;
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
       
       $queryExc=  DB::table('gDrive_Data')->insert(['asn_no'=> $asnNumber,'masterFolder' =>'SWIM','subFolder'=>'INWARD' ,'fileName'=>$fileName ,'createTime'=>$createDateTime ,'flag'=>'POST' ,'file_url'=>$fileUrl,'docType'=>$docType,'userName'=>$userId,'WhID'=>$warehouseId,'custid'=>$clientId]);
 
       if( $queryExc){
        return true;
       }else{
        return false;
       }
       
    }
}
