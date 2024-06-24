<?php

namespace App\Http\Controllers;

use App\Models\Asn;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreAsnRequest;
use App\Http\Requests\UpdateAsnRequest;
use App\Traits\UserWarehousesTrait;
use Laravel\Sanctum\PersonalAccessToken;

class AsnController extends Controller
{

    public function findAsn(Request $request)
    {

        if (!empty($request->asnval)) {
            $warehouse = $this->fetchUserAssignWarehouse($request->uid);
            // function give user wise warehouse             
            $result =
                DB::table('asns')
                    ->rightJoin('asn_subs', 'asn_subs.asn_id', '=', 'asns.id')
                    ->rightJoin('clients', 'clients.id', '=', 'asns.client_id')
                    ->select([
                        'asn_subs.id as ids',
                        'asns.asn_no as asnNo',
                        'asns.invoiceNo as invoiceNo',
                        'asns.po_number as poNumber',
                        'asn_subs.qty as qty',
                        'clients.clientName as cName'
                    ])
                    ->where('asns.asn_no', 'LIKE', '%' . $request->asnval . '%')
                    ->whereIn('asns.warehouse_id', $warehouse)
                    ->paginate(2);
            return response()->json($result);
        }
    }

    public function asnDetial(Request $request, $id)
    {

        $result =
            DB::table('asns')
                ->rightJoin('asn_subs', 'asn_subs.asn_id', '=', 'asns.id')
                ->rightJoin('clients', 'clients.id', '=', 'asns.client_id')
                ->select([
                    'asn_subs.id as ids',
                    'asns.asn_no as asnNo',
                    'asns.invoiceNo as invoiceNo',
                    'asns.po_number as poNumber',
                    'asn_subs.qty as qty',
                    'clients.clientName as cName'
                ])
                // ->where('asns.asn_no','like',$request->nameval) 
                ->where('asn_subs.id ', $id)
                ->first();
        return response()->json($result);
    }


    // searchAsn function search asn


    public function searchAsn(Request $request)
    {

        $asnQuery = "SELECT COUNT(DISTINCT ITEMCODE) AS TOTSKUCODEV, p.CUSTINV as InvoiceNO,p.PONO,
        p.P_MRN_nO as ASN_NO,p.CustID,p.WH,isnull(sum(p.Qty),0) as ASNQTY,
        (select  isnull(sum(r.Qty),0) from tbl_retailscanning r 
        with(nolock) where r.wh=p.wh and r.custid=p.CUSTID and 
        r.P_MRN_No=p.P_MRN_NO and r.invoiceno=p.custinv)as ScanQty 
        FROM PREmrn_Upload p WITH(NOLOCK) 
        WHERE p.WH='" . $request->warehouseId . "' AND P.CUSTID='" . $request->clientId . "' 
        or (p.P_MRN_nO='" . $request->searchScanValue . "' OR p.CUSTINV='" . $request->searchScanValue . "')
        AND ISNULL(p.MRN_NO,'')='' and p.vehinid<>'' group 
        by p.CUSTINV,p.PONO,p.P_MRN_nO,p.CustID,p.WH ORDER BY p.CUSTINV";

        $asn = DB::select($asnQuery);

        $asnData = $this->arrayPaginator($asn, $request->page, $request);
        // return response()->json( $asn );
        if (!empty($asnData) && count($asnData) > 0) {
            return response()->json($asnData);
        } else {
            return response(['error' => 'Record not found'], 404);
        }

    }


    public function validateLPNBox(Request $request)
    {

        $lpn = "select DISTINCT WHLOCATION from Tbl_RetailScanning where WHLocation='" . $request->wLocation . "' and wh='" . $request->warehouseId . "' and CUSTID='" . $request->clientId . "' AND P_MRN_NO<>'" . $request->asn . "'";

        $lpndata = DB::select($lpn);

        if (count($lpndata) == 0) {
            return response()->json('ok');
        } else {
            return response(['error' => 'LPN Not Valid'], 404);
        }


    }
    // Find asn acan end


    // Find Asn list 

    public function asnListValue(Request $request)
    {

        $asnList = "SELECT COUNT(DISTINCT ITEMCODE) AS TOTSKUCODEV, p.CUSTINV as InvoiceNO,p.PONO,
        p.P_MRN_nO as ASN_NO,p.CustID,p.WH,isnull(sum(p.Qty),0) as ASNQTY,
        (select  isnull(sum(r.Qty),0) from tbl_retailscanning r 
        with(nolock) where r.wh=p.wh and r.custid=p.CUSTID and 
        r.P_MRN_No=p.P_MRN_NO and r.invoiceno=p.custinv)as ScanQty 
        FROM PREmrn_Upload p WITH(NOLOCK) 
        WHERE p.WH='" . $request->warehouseId . "' AND P.CUSTID='" . $request->clientId . "'
        AND ISNULL(p.MRN_NO,'')='' and p.vehinid<>'' group 
        by p.CUSTINV,p.PONO,p.P_MRN_nO,p.CustID,p.WH ORDER BY p.CUSTINV";


        $results = DB::select($asnList);


        $notices = $this->arrayPaginator($results, $request->page, $request);

        return response()->json($notices);

        // $asn = DB::select($asnList);
        //return response()->json($results);
        // if(!empty($asn) && count($asn)>0){
        //     return response()->json( $asn);
        //    }else{
        //     return response(['error'=>'Record not found'],404);
        //    } 

    }


    public function arrayPaginator($array, $page, $request)
    {
        $page = $page;
        $perPage = 5;
        $offset = ($page * $perPage) - $perPage;
        return new \Illuminate\Pagination\LengthAwarePaginator(
            array_slice($array, $offset, $perPage, true),
            count($array),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );
    }

    //End find Asn List

    public function asnDetailValue(Request $request)
    {
         
        $asnQuery = "SELECT COUNT(DISTINCT ITEMCODE) AS TOTSKUCODEV, p.CUSTINV as InvoiceNO,p.PONO,
        p.P_MRN_nO as ASN_NO,p.CustID,p.WH,isnull(sum(p.Qty),0) as ASNQTY,
        (select  isnull(sum(r.Qty),0) from tbl_retailscanning r 
        with(nolock) where r.wh=p.wh and r.custid=p.CUSTID and 
        r.P_MRN_No=p.P_MRN_NO and r.invoiceno=p.custinv)as ScanQty 
        FROM PREmrn_Upload p WITH(NOLOCK) 
        WHERE p.WH='" . $request->warehouseId . "' AND P.CUSTID='" . $request->clientId . "' AND (p.P_MRN_nO='" . $request->searchScanValue . "' OR p.CUSTINV='" . $request->searchScanValue . "')
        AND ISNULL(p.MRN_NO,'')='' and p.vehinid<>'' group 
        by p.CUSTINV,p.PONO,p.P_MRN_nO,p.CustID,p.WH ORDER BY p.CUSTINV";

       
        $asn = DB::select($asnQuery);
        $count = count($asn);
        if ($count > 0) {
            return response()->json(['asn' => $asn], 200);
        } else {
            return response()->json(['error' => 'error'], 404);
        }

    }



    public function getWareHouseName()
    {
        // $warehoue = DB::table('tbl_whmaster')->select('WHid','WHname')->distinct('WHname')->get();

        $query = "select distinct WHid,WHname from tbl_whmaster with(nolock) where isnull(WHactive,'')<>'' order by WHname ASC";
        $warehoue = DB::select($query);
        return \response()->json($warehoue);
    }

    public function scanSku(Request $request)
    {

        $result =
            DB::table('asns')
                ->rightJoin('asn_subs', 'asn_subs.asn_id', '=', 'asns.id')
                ->rightJoin('clients', 'clients.id', '=', 'asns.client_id')
                ->rightJoin('skus', 'skus.client_id', '=', 'asns.client_id')
                ->select([
                    'asn_subs.id as ids',
                    'asns.asn_no as asnNo',
                    'asns.invoiceNo as invoiceNo',
                    'asns.po_number as poNumber',
                    'skus.skuCode as skucode',
                    'asn_subs.qty as qty',
                    'clients.clientName as cName'
                ])
                // ->where('asns.asn_no','like',$request->nameval) 
                //  ->where('asn_subs.id ','3423423')           
                ->get();
        return response()->json($result);
    }

    public function getSKU(Request $request)
    {

        $query = " SELECT COUNT(DISTINCT ITEMCODE) as totalRows, ITEMCODE AS SKU,p.batchno,isnull(sum(p.Qty),0) as ASNQTY,(select  isnull(sum(r.Qty),0) 
       from Tbl_RetailScanning  r with(nolock) where r.wh=p.wh and r.custid=p.CUSTID and r.P_MRN_No=p.P_MRN_NO 
       and r.invoiceno=p.custinv  AND r.sku=p.ItemCode and r.batchno=p.batchno) as ScanQty, 
       (isnull(sum(p.Qty),0)- (select  isnull(sum(r.Qty),0) 
       from Tbl_RetailScanning  r with(nolock) where r.wh=p.wh and r.custid=p.CUSTID and r.P_MRN_No=p.P_MRN_NO 
       and  r.invoiceno=p.custinv  AND r.sku=p.ItemCode and r.batchno = p.batchno )) as BalanceFOrScan FROM PreMRN_Upload  p WITH(NOLOCK) 
       WHERE p.WH='" . $request->warehouseId . "' AND (CUSTINV='" . $request->cutInvoice . "') AND P.CUSTID='" . $request->clientId . "' AND ISNULL(p.MRN_NO,'')='' 
       and p.vehinid<>'' AND ItemCode='" . $request->skuCode . "' group by ItemCode,p.wh,p.custid,p.p_mrn_NO,p.custinv,p.batchno
       ";
        $sku = DB::select($query);
        $total = count($sku);

        $scanQty = " select isnull(sum(qty),0) scanqty from Tbl_RetailScanning with(nolock) 
       where wh='" . $request->warehouseId . "' and CUSTID ='" . $request->clientId . "' and INVOICENO ='" . $request->cutInvoice . "'";

        $scanQtyTotalvalues = DB::select($scanQty);



        if ($total > 0) {
            return response()->json(['sku' => $sku, 'total' => $total, 'scanQtyTotalvalues' => $scanQtyTotalvalues, 'success' => 'success']);
        } else {
            return response()->json(['error' => 'error']);
        }

    }

    // public function fetchUserAssignWarehouse($userid){
    //     // get warehouse user wise
    //     $query = DB::table('users')
    //         ->join('user_warehouse','user_warehouse.user_id','=','users.id')
    //         ->where('users.id','=',$userid)
    //         ->select('user_warehouse.warehouse_id')
    //         ->get();                
    //     $arrQuery = array();
    //     foreach($query as $key => $q){
    //         $arrQuery[$key] = $q->warehouse_id;
    //     }
    //     return $arrQuery;
    // }

    public function getCleintName(Request $request)
    {
        
        $rows = "SELECT DISTINCT custname AS ClientName,C.custid as Clientid FROM tbl_customer  C WITH(NOLOCK) LEFT JOIN tbl_WHCustmapping W WITH(NOLOCK) ON C.custid=W.CUSTID

        WHERE ISNULL(custactive,'')='' AND W.WHID='".$request->warehouseId."' order by custname ASC";
        $query = DB::select($rows);
        return response($query);
    }

    // public function findClientWarehouseWise(Request $request){   
    //     $bearerToken = $request->Token;         
    //     $token = PersonalAccessToken::findToken($bearerToken); 
    //     $user = $token->tokenable;         
    //     if (!$token) {
    //        return response(['message'=>'Token not found'],200);
    //     }  

    //     $clientData= DB::table('clients')->select('clients.id','clients.clientName','clients.clientCode','clients.created_at')
    //     ->join('warehouse_client_mappings','warehouse_client_mappings.client_id','=','clients.id')
    //     ->where('warehouse_client_mappings.warehouse_id',$request->warehouseId)
    //     ->where('clients.status',1)
    //     ->get();

    //     if($clientData){
    //         return response()->json($clientData);      
    //     }else{
    //         return response(['error'=>'No Client Found'],401);
    //     }           
    // }


    // public function findClienUserWise(Request $request){

    //     $clientsUserWise= DB::table('user_warehouse') 
    //     ->join('warehouse_client_mappings','warehouse_client_mappings.warehouse_id','=','user_warehouse.warehouse_id')
    //     ->join('clients', 'clients.id','=','warehouse_client_mappings.client_id')
    //     ->where('user_warehouse.user_id',$request->userId)         
    //     ->get(); 

    //     if($clientsUserWise){
    //         return response()->json($clientsUserWise);      
    //     }else{
    //         return response(['error'=>'No Client Found'],401);
    //     }      

    // }


    public function skuInserUpdateValues(Request $request)
    {

        $row = " SELECT * FROM tbl_retailscanning with(nolock) WHERE WH='" . $request->warehouseId . "' AND CUSTID='" . $request->clientId . "' AND INVOICENO='" . $request->invoice . "' AND SKU='" . $request->skuNumber . "' AND WHLOCATION='" . $request->wLocation . "' and batchNo='" . $request->skuBatch . "'";


        $query = DB::select($row);
        $numrow = count($query);

        if ($numrow > 0) {
            $valdate = "select isnull(sum(Qty),0) as qty,(SELECT ISNULL(SUM(QTY),0) AS ScanQTy from Tbl_RetailScanning r with(nolock) where r.wh=p.wh and r.CUSTID=p.CustID and r.sku=p.ItemCode and r.BATCHNO=p.BatchNo ) as Scanqty 
        from PreMRN_Upload p with(nolock) where wh='" . $request->warehouseId . "' and CustID='" . $request->clientId . "' and CustInv='" . $request->invoice . "' and ItemCode='" . $request->skuNumber . "' and BatchNo='" . $request->skuBatch . "'        
        GROUP BY P.WH,P.CustID,P.ItemCode,P.BatchNo,P.CustInv";


            $validateQuery = DB::select($valdate);
            $totalQty = $request->skuqtyval + $validateQuery[0]->Scanqty;
            if ($totalQty > $validateQuery[0]->qty) {
                return response()->json(['error' => 'greaterQty']);
            }


            $updateQuery = "UPDATE tbl_retailscanning with(tablock) SET QTY=ISNULL(QTY,0)+'" . $request->skuqtyval . "' WHERE WH='" . $request->warehouseId . "' AND CUSTID='" . $request->clientId . "' AND INVOICENO='" . $request->invoice . "' AND SKU='" . $request->skuNumber . "' AND WHLOCATION='" . $request->wLocation . "' and batchno='" . $request->skuBatch . "'";


            $status = DB::update($updateQuery);

            if ($status == 1) {

                $query = " SELECT COUNT(DISTINCT ITEMCODE) as totalRows, ITEMCODE AS SKU,p.batchno,isnull(sum(p.Qty),0) as ASNQTY,(select  isnull(sum(r.Qty),0) 
            from Tbl_RetailScanning  r with(nolock) where r.wh=p.wh and r.custid=p.CUSTID and r.P_MRN_No=p.P_MRN_NO 
            and r.invoiceno=p.custinv  AND r.sku=p.ItemCode and r.batchno=p.batchno) as ScanQty, 
            (isnull(sum(p.Qty),0)- (select  isnull(sum(r.Qty),0) 
            from Tbl_RetailScanning  r with(nolock) where r.wh=p.wh and r.custid=p.CUSTID and r.P_MRN_No=p.P_MRN_NO 
            and  r.invoiceno=p.custinv  AND r.sku=p.ItemCode and r.batchno = p.batchno )) as BalanceFOrScan FROM PreMRN_Upload  p WITH(NOLOCK) 
            WHERE p.WH='" . $request->warehouseId . "' AND (CUSTINV='" . $request->invoice . "') AND P.CUSTID='" . $request->clientId . "' AND ISNULL(p.MRN_NO,'')='' 
            and p.vehinid<>'' AND ItemCode='" . $request->skuNumber . "' group by ItemCode,p.wh,p.custid,p.p_mrn_NO,p.custinv,p.batchno";
                $sku = DB::select($query);
                $total = count($sku);

                $scanQty = " select isnull(sum(qty),0) scanqty from Tbl_RetailScanning with(nolock) 
            where wh='" . $request->warehouseId . "' and CUSTID ='" . $request->clientId . "' and INVOICENO ='" . $request->invoice . "'";

                $scanQtyTotalvalues = DB::select($scanQty);


                return response()->json(['sku' => $sku, 'total' => $total, 'scanQtyTotalvalues' => $scanQtyTotalvalues, 'success' => 3]);
            } else {
                return response(4);
            }

        } else {

            $insertQuery = "insert into tbl_retailscanning with(tablock)(wh,custid,invoiceno,sku,whlocation,qty,entryby,ENTRYON,p_mrn_No,batchno)values('" . $request->warehouseId . "','" . $request->clientId . "','" . $request->invoice . "','" . $request->skuNumber . "','" . $request->wLocation . "','" . $request->skuqtyval . "','" . $request->enteryBy . "',GETDATE(),'" . $request->asn . "','" . $request->skuBatch . "')";




            $status = DB::insert($insertQuery);
            if ($status == 1) {
                $query = " SELECT COUNT(DISTINCT ITEMCODE) as totalRows, ITEMCODE AS SKU,p.batchno,isnull(sum(p.Qty),0) as ASNQTY,(select  isnull(sum(r.Qty),0) 
            from Tbl_RetailScanning  r with(nolock) where r.wh=p.wh and r.custid=p.CUSTID and r.P_MRN_No=p.P_MRN_NO 
            and r.invoiceno=p.custinv  AND r.sku=p.ItemCode and r.batchno=p.batchno) as ScanQty, 
            (isnull(sum(p.Qty),0)- (select  isnull(sum(r.Qty),0) 
            from Tbl_RetailScanning  r with(nolock) where r.wh=p.wh and r.custid=p.CUSTID and r.P_MRN_No=p.P_MRN_NO 
            and  r.invoiceno=p.custinv  AND r.sku=p.ItemCode and r.batchno = p.batchno )) as BalanceFOrScan FROM PreMRN_Upload  p WITH(NOLOCK) 
            WHERE p.WH='" . $request->warehouseId . "' AND (CUSTINV='" . $request->invoice . "') AND P.CUSTID='" . $request->clientId . "' AND ISNULL(p.MRN_NO,'')='' 
            and p.vehinid<>'' AND ItemCode='" . $request->skuNumber . "' group by ItemCode,p.wh,p.custid,p.p_mrn_NO,p.custinv,p.batchno
            ";


                $sku = DB::select($query);
                $total = count($sku);

                $scanQty = " select isnull(sum(qty),0) scanqty from Tbl_RetailScanning with(nolock) 
            where wh='" . $request->warehouseId . "' and CUSTID ='" . $request->clientId . "' and INVOICENO ='" . $request->invoice . "'";
                $scanQtyTotal = DB::select($scanQty);

                return response()->json(['sku' => $sku, 'total' => $total, 'scanQtyTotal' => $scanQtyTotal, 'success' => 1]);
            } else {
                return response(0);
            }
            return response($status);
        }

        return response($numrow);
    }



}
