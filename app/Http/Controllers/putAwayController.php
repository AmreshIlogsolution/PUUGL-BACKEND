<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

// use Illuminate\Support\Facades\Auth; 
// use Laravel\Sanctum\PersonalAccessToken;

class putAwayController extends Controller
{

    public function findMrnInvoicewithid(Request $request)
    {
        $query = "SELECT MRNNo,InCustInvNo as Invoice,INfrom as PartyName,COnvert(date,EntryOn) as MRNOn from tbl_inward with(nolock) where ISNULL(ItemLocation,'')='' and wh='" . $request->warehouseId . "' and Cust='" . $request->clientId . "' and ( MRNNo='" . $request->mrnInvoice . "' or InCustInvNo='" . $request->mrnInvoice . "') group by MRNNo,InCustInvNo ,INfrom ,COnvert(date,EntryOn)";
        $results = DB::select($query);
        return response($results);
    }


    public function findMrnInvoice(Request $request)
    {
        if ($request->mrnInvoice == "") {
            $query = "SELECT MRNNo,InCustInvNo as Invoice,INfrom as PartyName,COnvert(date,EntryOn) as MRNOn from tbl_inward with(nolock) where ISNULL(ItemLocation,'')='' and wh='" . $request->warehouseId . "' and Cust='" . $request->clientId . "' group by MRNNo,InCustInvNo ,INfrom ,COnvert(date,EntryOn)";

            $results = DB::select($query);
            $data = $this->arrayPaginator($results, $request->page, $request);
            return response($results);
        } else {
            $query = "SELECT MRNNo,InCustInvNo as Invoice,INfrom as PartyName,COnvert(date,EntryOn) as MRNOn from tbl_inward with(nolock) where ISNULL(ItemLocation,'')='' and wh='" . $request->warehouseId . "' and Cust='" . $request->clientId . "' and ( MRNNo='" . $request->mrnInvoice . "' or InCustInvNo='" . $request->mrnInvoice . "') group by MRNNo,InCustInvNo ,INfrom ,COnvert(date,EntryOn)";

            $results = DB::select($query);
            $data = $this->arrayPaginator($results, $request->page, $request);
            return response($results);
        }
    }
    public function arrayPaginator($array, $page, $request)
    {
        $page = $page;
        $perPage = 1;
        $offset = ($page * $perPage) - $perPage;
        return new \Illuminate\Pagination\LengthAwarePaginator(
            array_slice($array, $offset, $perPage, true),
            count($array),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );
    }


    public function findbin(Request $request)
    {
        $query = "select distinct Location from whlocation with(nolock) where wh='" . $request->warehouseId . "' and custid='" . $request->clientId . "' AND Location='" . $request->bin . "'";

        $results = DB::select($query);
        $numrow = count($results);
        if ($numrow > 0) {
            return response($numrow);
        } else {
            return response(['error' => 'notFounds']);
        }
    }

    public function searchbinsku(Request $request)
    {
        $query = "select SUM(CAST(OK as int)) as qty ,sku,batchno,'OK' AS SEGTYPE,(select ISNULL(sum(CAST(QTY AS INT)),0) from Tbl_PutawayTemp tpt with(nolock) where tpt.CUSTID =i.cust and tpt.wh=i.wh and tpt.CUSTINV =i.InCustInvNo  and sloc='ok' and tpt.BATCHNO =i.batchno and tpt.sku=i.sku ) as scanqty from tbl_inward i with(nolock) where WH='" . $request->warehouseId . "' AND Cust='" . $request->clientId . "' AND  MRNNo='" . $request->mrnNo . "' AND SKU='" . $request->sku . "' and ok>0 GROUP BY SKU,BatchNo,I.Cust,WH ,I.InCustInvNo  
        union ALL select SUM(CAST(Pcs_Damage as int)) as qty , sku,batchno,'PD' AS SEGTYPE ,(select ISNULL(sum(CAST(QTY AS INT)),0)from Tbl_PutawayTemp tpt with(nolock) where tpt.CUSTID =i.cust and tpt.wh=i.wh and tpt.CUSTINV=i.InCustInvNo and sloc='PD' and tpt.BATCHNO =i.batchno and tpt.sku=i.sku ) as scanqty from tbl_inward i with(nolock) where WH='" . $request->warehouseId . "' AND Cust='" . $request->clientId . "' AND  MRNNo='" . $request->mrnNo . "' AND SKU='" . $request->sku . "'  and Pcs_Damage>0 GROUP BY SKU,BatchNo  ,I.Cust  ,WH ,I.InCustInvNo         
        union ALL select SUM(CAST(Carton_Damage as int)) as qty , sku,batchno,'CD' AS SEGTYPE,(select ISNULL(sum(CAST(QTY AS INT)),0) from Tbl_PutawayTemp tpt with(nolock) where tpt.CUSTID =i.cust and tpt.wh=i.wh and tpt.CUSTINV=i.InCustInvNo and sloc='CD' and tpt.BATCHNO =i.batchno and tpt.sku=i.sku ) as scanqty from tbl_inward i with(nolock) where WH='" . $request->warehouseId . "' AND Cust='" . $request->clientId . "' AND  MRNNo='" . $request->mrnNo . "' AND SKU='" . $request->sku . "'  and Carton_Damage>0 GROUP BY SKU,BatchNo  ,I.Cust   ,WH  ,I.InCustInvNo       
        union ALL select SUM(CAST(Repckd as int)) as qty , sku,batchno,'RP' AS SEGTYPE,(select ISNULL(sum(CAST(QTY AS INT)),0)  from Tbl_PutawayTemp tpt with(nolock) where tpt.CUSTID =i.cust and tpt.wh=i.wh and tpt.CUSTINV=i.InCustInvNo and sloc='RP' and tpt.BATCHNO =i.batchno and tpt.sku=i.sku ) as scanqty from tbl_inward i with(nolock) where WH='" . $request->warehouseId . "' AND Cust='" . $request->clientId . "' AND  MRNNo='" . $request->mrnNo . "' AND SKU='" . $request->sku . "'  and Repckd>0 GROUP BY SKU,BatchNo      ,I.Cust   ,WH  ,I.InCustInvNo  
        union ALL select SUM(CAST(QC_Chk as int)) as qty , sku,batchno,'QC' AS SEGTYPE,(select ISNULL(sum(CAST(QTY AS INT)),0) from Tbl_PutawayTemp tpt with(nolock) where tpt.CUSTID =i.cust and tpt.wh=i.wh and tpt.CUSTINV=i.InCustInvNo and sloc='QC' and tpt.BATCHNO =i.batchno and tpt.sku=i.sku ) as scanqty from tbl_inward i with(nolock) where WH='" . $request->warehouseId . "' AND Cust='" . $request->clientId . "' AND  MRNNo='" . $request->mrnNo . "' AND SKU='" . $request->sku . "'  and QC_Chk>0 GROUP BY SKU,BatchNo,I.Cust ,WH  ,I.InCustInvNo";
        $data = DB::select($query);

        if (!empty($data) && count($data) > 0) {
            return response()->json($data);
        } else {
            return response(['error' => 'notFound']);
        }

    }
    ##########         ###############
    public function updateSkuPutAway(Request $request)
    {
        $validateSkuQty = "SELECT isnull(sum(CAST(qty as int)),0)  as qty FROM Tbl_PutawayTemp WITH(NOLOCK) WHERE CUSTINV='" . $request->invoice . "' AND SKU='" . $request->sku . "' AND BATCHNO='" . $request->batchVal . "' and wh='" . $request->warehouseId . "' and CUSTID='" . $request->clientId . "' and sloc='" . $request->sloc . "'";

        $row = DB::select($validateSkuQty);
        if ($row) {
            if (count($row) > 0) {

                $itemChek = "SELECT WH,QTY as qty,SKU FROM Tbl_PutawayTemp WHERE CUSTINV='" . $request->invoice . "' AND SKU='" . $request->sku . "' AND BATCHNO='" . $request->batchVal . "' and wh='" . $request->warehouseId . "' and CUSTID='" . $request->clientId . "' and sloc='" . $request->sloc . "' ";

                $check = DB::select($itemChek);

                if ($request->skuRadio == 1) {
                    if (count($check) > 0) {
                        if (((int) $check[0]->qty + (int) $request->skuinputvalue) > $request->skuQty) {
                            $success = 3; //LEss Qty of input SKU  (INPUT value is greater than of qty)
                        } else {
                            $removeQty = (int) $check[0]->qty + (int) $request->skuinputvalue;
                            $updates = "update  Tbl_PutawayTemp with(tablock) set QTY =  '" . $removeQty . "', ENTRYBY = '" . $request->ENTRYBY . "' , ENTRYON =GETDATE() where wh='" . $request->warehouseId . "' and CUSTID='" . $request->clientId . "' and CUSTINV ='" . $request->invoice . "' and location='" . $request->location . "' and sku = '" . $request->sku . "' and BATCHNO = '" . $request->batchVal . "'    and sloc ='" . $request->sloc . "'";
                            DB::update($updates);
                            $success = 1;
                        }
                    } else {
                        $insert = "insert into Tbl_PutawayTemp with(tablock) 
                    (wh,CUSTID,CUSTINV,BOXID,LOCATION,SKU,BATCHNO,QTY,ENTRYBY,ENTRYON,sloc) 
                    values('" . $request->warehouseId . "','" . $request->clientId . "','" . $request->invoice . "','" . $request->location . "','" . $request->location . "','" . $request->sku . "','" . $request->batchVal . "','" . $request->skuinputvalue . "','" . $request->ENTRYBY . "',GETDATE(),'" . $request->sloc . "')";

                        $status = DB::insert($insert);

                        $success = 1;
                    }

                } else {

                    // return response([$request->skuQty,$check[0]->qty,$request->skuinputvalue,$request->skuinputvalue,$check[0]->qty]);
                    if (($request->skuQty >= ((int) $request->skuinputvalue)) && ($request->skuinputvalue <= $check[0]->qty)) {

                        //$success =3; //LEss Qty of input SKU  (INPUT value is greater than of qty)
                        $removeQty = (int) $check[0]->qty - (int) $request->skuinputvalue;

                        if ($removeQty >= 0) {

                            $updates = "update  Tbl_PutawayTemp with(tablock) set QTY =  '" . $removeQty . "', ENTRYBY = '" . $request->ENTRYBY . "' , ENTRYON =GETDATE() where wh='" . $request->warehouseId . "' and CUSTID='" . $request->clientId . "' and CUSTINV ='" . $request->invoice . "' and location='" . $request->location . "' and sku = '" . $request->sku . "' and BATCHNO = '" . $request->batchVal . "'    and sloc ='" . $request->sloc . "'";

                            DB::update($updates);
                            $success = 2;
                        } else {
                            $success = 3;
                        }

                    } else {
                        //return response($request->skuinputvalue);
                        $success = 3;

                    }
                }

            }

            // else{
            //     if(( (int)$request->skuinputvalue) > $request->skuQty ){ 
            //         $success =3; //LEss Qty of input SKU  (INPUT value is greater than of qty)
            //     }else{

            //         $insert ="insert into Tbl_PutawayTemp with(tablock) 
            //         (wh,CUSTID,CUSTINV,BOXID,LOCATION,SKU,BATCHNO,QTY,ENTRYBY,ENTRYON,sloc) 
            //         values('".$request->warehouseId."','".$request->clientId."','".$request->invoice."','".$request->location."','".$request->location."','".$request->sku."','".$request->batchVal."','".$request->skuinputvalue."','".$request->ENTRYBY."',GETDATE(),'".$request->sloc."')";
            //         return response($insert);
            //         $status = DB::insert($insert); 

            //         $success =1;

            //      }

            // }
        }

        $query = "select SUM(CAST(OK as int)) as qty ,sku,batchno,'OK' AS SEGTYPE,(select ISNULL(sum(CAST(QTY AS INT)),0) from Tbl_PutawayTemp tpt with(nolock) where tpt.CUSTID =i.cust and tpt.wh=i.wh and tpt.CUSTINV =i.InCustInvNo  and sloc='ok' and tpt.BATCHNO =i.batchno and tpt.sku=i.sku ) as scanqty from tbl_inward i with(nolock) where WH='" . $request->warehouseId . "' AND Cust='" . $request->clientId . "' AND  MRNNo='" . $request->mrnNo . "' AND SKU='" . $request->sku . "' and ok>0 GROUP BY SKU,BatchNo,I.Cust,WH ,I.InCustInvNo  
        union ALL select SUM(CAST(Pcs_Damage as int)) as qty , sku,batchno,'PD' AS SEGTYPE ,(select ISNULL(sum(CAST(QTY AS INT)),0)from Tbl_PutawayTemp tpt with(nolock) where tpt.CUSTID =i.cust and tpt.wh=i.wh and tpt.CUSTINV=i.InCustInvNo and sloc='PD' and tpt.BATCHNO =i.batchno and tpt.sku=i.sku ) as scanqty from tbl_inward i with(nolock) where WH='" . $request->warehouseId . "' AND Cust='" . $request->clientId . "' AND  MRNNo='" . $request->mrnNo . "' AND SKU='" . $request->sku . "'  and Pcs_Damage>0 GROUP BY SKU,BatchNo  ,I.Cust  ,WH ,I.InCustInvNo         
        union ALL select SUM(CAST(Carton_Damage as int)) as qty , sku,batchno,'CD' AS SEGTYPE,(select ISNULL(sum(CAST(QTY AS INT)),0) from Tbl_PutawayTemp tpt with(nolock) where tpt.CUSTID =i.cust and tpt.wh=i.wh and tpt.CUSTINV=i.InCustInvNo and sloc='CD' and tpt.BATCHNO =i.batchno and tpt.sku=i.sku ) as scanqty from tbl_inward i with(nolock) where WH='" . $request->warehouseId . "' AND Cust='" . $request->clientId . "' AND  MRNNo='" . $request->mrnNo . "' AND SKU='" . $request->sku . "'  and Carton_Damage>0 GROUP BY SKU,BatchNo  ,I.Cust   ,WH  ,I.InCustInvNo       
        union ALL select SUM(CAST(Repckd as int)) as qty , sku,batchno,'RP' AS SEGTYPE,(select ISNULL(sum(CAST(QTY AS INT)),0)  from Tbl_PutawayTemp tpt with(nolock) where tpt.CUSTID =i.cust and tpt.wh=i.wh and tpt.CUSTINV=i.InCustInvNo and sloc='RP' and tpt.BATCHNO =i.batchno and tpt.sku=i.sku ) as scanqty from tbl_inward i with(nolock) where WH='" . $request->warehouseId . "' AND Cust='" . $request->clientId . "' AND  MRNNo='" . $request->mrnNo . "' AND SKU='" . $request->sku . "'  and Repckd>0 GROUP BY SKU,BatchNo      ,I.Cust   ,WH  ,I.InCustInvNo  
        union ALL select SUM(CAST(QC_Chk as int)) as qty , sku,batchno,'QC' AS SEGTYPE,(select ISNULL(sum(CAST(QTY AS INT)),0) from Tbl_PutawayTemp tpt with(nolock) where tpt.CUSTID =i.cust and tpt.wh=i.wh and tpt.CUSTINV=i.InCustInvNo and sloc='QC' and tpt.BATCHNO =i.batchno and tpt.sku=i.sku ) as scanqty from tbl_inward i with(nolock) where WH='" . $request->warehouseId . "' AND Cust='" . $request->clientId . "' AND  MRNNo='" . $request->mrnNo . "' AND SKU='" . $request->sku . "'  and QC_Chk>0 GROUP BY SKU,BatchNo,I.Cust ,WH  ,I.InCustInvNo";
        $data = DB::select($query);
        return response(['success' => $success, 'putawaydata' => $data]);
    }
}
