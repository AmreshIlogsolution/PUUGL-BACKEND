<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;

class skuIdentificationController extends Controller
{
    public function findzoneforSku(Request $request)
    {
      
        try {
            if (empty($request->searchZoneScanValue)) {
                return response()->json(['error' => 'NotFound']);
            } else {
              
                sleep(1);
                $matchThese = ['Custid' => $request->clientId, 'WH' => $request->warehouseId];
                // DB::enableQueryLog();

                $invData = DB::table('WHLocation')
                    ->lock('WITH(NOLOCK)')
                    ->select('Zone', 'ZoneBarcode')
                    ->where($matchThese)
                    ->whereIn('ZONEBARCODE', $request->searchZoneScanValue)
                    ->groupBy('ZONE', 'ZoneBarcode')
                    ->get();
             
                //dd(DB::getQueryLog());
                // $queryZone = 'select Zone,  ZoneBarcode from WHLocation WITH(NOLOCK) WHERE WH="' . $request->warehouseId . '" AND Custid="' . $request->clientId . '" AND ZONEBARCODE IN ("' . $request->searchZoneScanValue . '") GROUP BY ZONE, ZoneBarcode';
                return response($invData);
                // $results = DB::select($queryZone);
                // return response($results);
            }
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function findaisleforSku(Request $request)
    {
        
        try {
            if (empty($request->aisleScanValue)) {
                return response()->json(['error' => 'NotFound']);
            } else {
                sleep(1);
                $matchThese = ['Custid' => $request->clientId, 'WH' => $request->warehouseId,'ZONEBARCODE'=>$request->zonebarcode,];
                // DB::enableQueryLog();,
                DB::enableQueryLog();
                $aisleData = DB::table('WHLocation')
                    ->lock('WITH(NOLOCK)')
                    ->select('AISLES', 'AISLESBARCODE')
                    ->where($matchThese)           
                    ->whereIn('AISLESBARCODE', $request->aisleScanValue)          
                    ->groupBy('AISLES', 'AISLESBARCODE')
                    ->get();
               
                return response($aisleData);                
            }
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function findBinforSku(Request $request)
    {       
        $str = "'".implode("','", $request->searchBinValues)."'";   
                
        try {
            if (empty($request->searchBinValues)) {
                return response()->json(['error' => 'NotFound']);
            } else {
                sleep(1);      
                //DB::enableQueryLog();
                $query ="SELECT WHLOCATION AS BIN, SKU,ISNULL(SUM(BALQTY),0) AS STOCKQTY,[DESC] AS DESCRIPTION ,AISLES FROM Tbl_putway P     
                WITH(NOLOCK) LEFT JOIN SKU_Master S WITH(NOLOCK) ON P.SKU=S.sku_Name AND P.CUST =S.SKU_Cust LEFT JOIN WHLocation W WITH(NOLOCK) ON W.WH=P.WH AND W.CUSTID=P.CUST AND W.Location=P.WHLOCATION WHERE P.WH='$request->warehouseId' AND CUST='$request->clientId' AND LOCATIONBARCODE IN ($str) GROUP BY WHLOCATION,SKU,[DESC],AISLES"; 
                // dd(DB::getQueryLog());
                $binquery = DB::select($query);
                return response($binquery); 
                //dd($binData);               
            }
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    
}
