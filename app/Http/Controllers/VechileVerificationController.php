<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use DB;
use Carbon\Carbon;
use DateTime;
use DateTimeZone;
class VechileVerificationController extends Controller
{
    // Vechile verification start
    public function verifyVechileNumber(Request $request)
    {          
      $vehNumver =  $request->params['vNumber']; 
      $tokenValue = $this->getToken();    
        if($tokenValue){  
          $query = DB::table('tbl_Veh_validate')     
            ->select('VEH_NO')  
            ->where('VEH_NO','=', $vehNumver) 
            ->count();
            if($query == 0){
              $curl = curl_init();
              $vechileNumver = $request->params['vNumber'];          
              curl_setopt_array($curl, array(
              CURLOPT_URL => 'https://preproduction.signzy.tech/api/v2/patrons/65ee9900ebfd1f0024f15b56/vehicleregistrations',
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_ENCODING => '',
              CURLOPT_MAXREDIRS => 10,
              CURLOPT_TIMEOUT => 0,
              CURLOPT_FOLLOWLOCATION => true,
              CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
              CURLOPT_CUSTOMREQUEST => 'POST',
              CURLOPT_POSTFIELDS =>'{            
                "task":"detailedSearch",            
                "essentials":            
                {            
                    "vehicleNumber":"'.$vechileNumver.'",            
                    "blacklistCheck": "true"            
                }
            
            }',
              CURLOPT_HTTPHEADER => array(
                'Accept: */*',
                'Accept-Language: en-US,en;q=0.8',
                'content-type: application/json',
                'Authorization:'.$tokenValue
              ),
            ));
            
            $response = curl_exec($curl);            
            curl_close($curl);
            $decodedResponse = json_decode($response, true); 
            $entryOn =  Carbon::now();
            $vehNo = $decodedResponse['result']['regNo'] ;
            $vehStatus = $decodedResponse['result']['status'];             
            $vehMfYear = $decodedResponse['result']['vehicleManufacturingMonthYear'];
            $vehInsUpto = $decodedResponse['result']['vehicleInsuranceUpto'];
            $vehPuccUpTo =$decodedResponse['result']['puccUpto'];
            $vehBlackList = $decodedResponse['result']['blacklistStatus'];
            $vehJson = $decodedResponse;
            // $vehValiateOn = $entryOn ;
            $vehValiateBy = $request->params['UserId'];
            return response()->json(['VEH_NO' => $vehNo, 'VEH_STATUS' => $vehStatus,'VEH_MF_YEAR'=>$vehMfYear,'VEH_INS_UPTO'=>$vehInsUpto,'VEH_PUCC_UPTO'=> $vehPuccUpTo,'VEH_BLACKLIST'=>$vehBlackList, 'VEH_JSON'=> $vehJson,'fromValue'=>'api','VEH_VALIDATE_ON'=>$entryOn,'VEH_VALIDATE_BY'=>$vehValiateBy]); 
            }else{
            $query =  DB::table('tbl_Veh_validate')     
            ->select('VEH_NO','VEH_STATUS','VEH_MF_YEAR','VEH_INS_UPTO','VEH_PUCC_UPTO','VEH_BLACKLIST')  
            ->where('VEH_No','=', $vehNumver) 
            ->get(); 

              return response()->json(['VEH_NO' =>$query[0]->VEH_NO, 'VEH_STATUS' => $query[0]->VEH_STATUS,'VEH_MF_YEAR'=>$query[0]->VEH_MF_YEAR,'VEH_INS_UPTO'=>$query[0]->VEH_INS_UPTO,'VEH_PUCC_UPTO'=> $query[0]->VEH_PUCC_UPTO,'VEH_BLACKLIST'=> $query[0]->VEH_BLACKLIST,'fromValue'=>'database']);
          
            }                
            }else{
              $msg=['error'=>'Token not found','status'=>'500'];
              return response()->json($msg);
            }
    }// Vechile verification end

    // DL Verifiction Code start

    public function verifyDLNumber(Request $request){ 
        sleep(1);
        $tokenValue = $this->getToken();    
        if($tokenValue){   // if valide token          
          $dlNumver =  $request->params['dlNumber']; 
          $entryBy = $request->params['UserId'];
          $dob = $request->params['dob']; 
          $entryOn =  Carbon::now();
          $deal_date = date('d/m/Y', strtotime($dob)); 
          $query = DB::table('Tbl_DL_validate')     
          ->select('DL_NO')  
          ->where('DL_No','=', $dlNumver) 
          ->count();
           
           // if dl is not found in database  hit api 
          if($query == 0){
            
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://preproduction.signzy.tech/api/v2/patrons/65ee9900ebfd1f0024f15b56/drivingLicenceV2',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS =>'{
                    "task":"fetch",
                    "essentials":
                    {
                        "number": "'.$dlNumver.'",
                        "dob": "'.$deal_date.'"
                    }
                }',
                CURLOPT_HTTPHEADER => array(
                    'Accept: */*',
                    'Accept-Language: en-US,en;q=0.8',
                    'Authorization:'.$tokenValue,
                    'content-type: application/json'
                ),
            ));

            $response = curl_exec($curl);
            curl_close($curl);
            $decodedResponse = json_decode($response, true); 
             
            $dob =  $decodedResponse['essentials']['dob'];
            $dlNumverResponse = $decodedResponse['essentials']['number'];
            $vClass = $decodedResponse['result']['badgeDetails'][0]['classOfVehicle'];
            $statusOfDrivingLicence = $decodedResponse['result']['detailsOfDrivingLicence']['status'];
            $dlAddress = $decodedResponse['result']['detailsOfDrivingLicence']['addressList'][0]['completeAddress'];
                 
            //$valideUpto  = date("Y-m-d", strtotime($dlValidityFrom));
            $dlName = $decodedResponse['result']['detailsOfDrivingLicence']['name'];           
            $dlphoto =   $decodedResponse['result']['detailsOfDrivingLicence']['photo'];
            $dlValidityTo = $decodedResponse['result']['dlValidity']['nonTransport']['to'];               
            $dlValidityFrom = $decodedResponse['result']['dlValidity']['nonTransport']['from'];     
            $aa = explode("/",$dlValidityTo);                  
            $valideUpto  = $aa[2].'-'.$aa[1].'-'.$aa[0];
            $dlValidityFrom = date("Y-m-d", strtotime($dlValidityFrom));
            $date1 = new DateTime($valideUpto);
            $date2 = new DateTime($dlValidityFrom);               
            $interval = date_diff($date2,$date1);            
            $entryBy = $request->params['UserId'];         
            $entryOn =  Carbon::now();
            return response()->json(['DLStatus' => $statusOfDrivingLicence, 'validUpTo' => $valideUpto,'DLAddress'=>$dlAddress,'photo'=>$dlphoto,'name'=> $dlName,'vUptp'=> $interval,'fromValue'=>'api','dob'=>$dob,'DlNumber'=>$dlNumverResponse,'vehicleClass'=> $vClass,'DLJson'=>  $decodedResponse]);
           
          }else{
          
            $query =  DB::table('Tbl_DL_validate')     
            ->select('DL_NO','DL_STATUS','VALID_UPTO','DL_ADDRESS','DL_IMAGE','DL_NAME')  
            ->where('DL_No','=', $dlNumver) 
            ->get(); 
            
          if($query[0]->DL_STATUS == 'ACTIVE'){
            
              return response()->json(['DLStatus' =>$query[0]->DL_STATUS, 'validUpTo' => $query[0]->VALID_UPTO,'DLAddress'=>$query[0]->DL_ADDRESS,'photo'=>$query[0]->DL_IMAGE,'name'=> $query[0]->DL_NAME,'vUptp'=> $query[0]->VALID_UPTO,'fromValue'=>'database']);
          }else{
            return response()->json(['error' => 'Either DL is not active or driving licence valid period is less then 15 day\'s', 'code' => '404']);
          } 
          }

        }else{
            $msg=['error'=>'Token not found','status'=>'500'];
            return response()->json($msg);
            }
    }
// DL Verifiction Code End 


// DL add to master 


public function dlAddToMaster(Request $request){
    sleep(1);
    $entryOn =  Carbon::now();
    $dob  = date("Y-m-d", strtotime($request->params['dob']));
    $vechileclass  = implode(",",$request->params['vehicleClass']);
    $dlJson = $json = json_encode($request->params['DLJson']);
     DB::table('Tbl_DL_validate')->insert(
        [
            'DL_NO' => $request->params['dlNumber'], 
            'DOB' => $dob,
            'VEHICLE_CLASS'=>$vechileclass,
            'DL_JSON'=> $dlJson,
            'VALID_UPTO'=> $request->params['validUpTo'],
            'DL_STATUS'=>$request->params['DLStatus'],
            'ENTRYBY'=>$request->params['UserId'],
            'ENTRYON'=>$entryOn,
            'DL_ADDRESS'=>$request->params['DLAddress'],
            'DL_IMAGE'=>$request->params['photo'],
            'DL_NAME'=>$request->params['name']   
        ]
    );
    return response()->json(['success' => 'Data added to master', 'code' => '200']);

}


public function vhAddToMaster(Request $request){
    $entryOn =  Carbon::now();
    $newDate = 
     
    $VEH_INS_UPTO  = \Carbon\Carbon::createFromFormat('d/m/Y', $request->params['VEH_INS_UPTO'])
    ->format('Y-m-d');
    $VEH_PUCC_UPTO =   \Carbon\Carbon::createFromFormat('d/m/Y',$request->params['VEH_PUCC_UPTO'])
    ->format('Y-m-d');

    $vhJson = $json = json_encode($request->params['VEH_JSON']);
    DB::table('tbl_Veh_validate')->insert(
        [
            'VEH_NO' => $request->params['VEH_NO'], 
            'VEH_STATUS'=>$request->params['VEH_STATUS'],
            'VEH_MF_YEAR'=> $request->params['VEH_MF_YEAR'],
            'VEH_INS_UPTO'=>$VEH_INS_UPTO,
            'VEH_PUCC_UPTO'=> $VEH_PUCC_UPTO,
            'VEH_BLACKLIST' => $request->params['VEH_BLACKLIST'],
            'VEH_JSON' =>$vhJson,
            'VEH_VALIDATE_BY'=>$request->params['UserId'],
            'VEH_VALIDATE_ON'=>$entryOn,
            
        ]
    );
    return response()->json(['success' => 'Data added to master', 'code' => '200']);
}

// Token for both DL varification and Driver varification start
    public function getToken(){
        $curl = curl_init();
        curl_setopt_array($curl, array(
          CURLOPT_URL => 'https://preproduction.signzy.tech/api/v2/patrons/login',
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'POST',
          CURLOPT_POSTFIELDS =>'{        
            "username": "awlindia_preprod_v2",        
            "password": "a5ZsqvbWns"        
        }',
          CURLOPT_HTTPHEADER => array(
            'Accept: */*',
            'Accept-Language: en-US,en;q=0.8',
            'content-type: application/json'
          ),
        ));
        
        $response = curl_exec($curl);        
        $decoded = json_decode($response, true);
        return $decoded['id'];  
    }
// Token for both DL varification and Driver varification End    
    
}
