<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;

class PicklistController extends Controller
{
    public function PendingPickListData(Request $request)
    {
        if ($request->picklistcontrol == '') {
            $picklistQuery = "select distinct wh,custid,dnno,REF_NO ,sum(qty) As InvQty,isnull(sum(pickqty),0) as PickQTy from Tbl_PickingDataRetail with(nolock) where isnull(wmsin,'')='' AND wh='" . $request->warehouseId . "' and custid='" . $request->clientId . "'  group by wh,custid,dnno,REF_NO order by DNNO  asc";


        } else {
            $picklistQuery = "select distinct wh,custid,dnno,REF_NO ,sum(qty) As InvQty,isnull(sum(pickqty),0) as PickQTy from Tbl_PickingDataRetail with(nolock) where isnull(wmsin,'')='' AND wh='" . $request->warehouseId . "' and custid='" . $request->clientId . "' and ( REF_NO='" . $request->picklistcontrol . "' or dnno='" . $request->picklistcontrol . "')  group by wh,custid,dnno,REF_NO order by DNNO  asc";
            return response($picklistQuery);
        }

        $results = DB::select($picklistQuery);
        $pendingPickListData = $this->arrayPaginator($results, $request->page, $request);




        if (count($pendingPickListData) > 0) {
            return response()->json($pendingPickListData);
        } else {
            return response()->json(['error' => 'NorFound']);
        }
    }


    public function PendingPickListBoxData(Request $request)
    {

        $picklistQuery = "select distinct wh,custid,dnno,REF_NO ,sum(qty) As InvQty,isnull(sum(pickqty),0) as PickQTy from Tbl_PickingDataRetail with(nolock) where isnull(wmsin,'')='' AND wh='" . $request->warehouseId . "' and custid='" . $request->clientId . "' and REF_NO='" . $request->refNo . "'  group by wh,custid,dnno,REF_NO order by DNNO  asc";




        $zone = "select DISTINCT ZONE,ZONEBARCODE,ISNULL(SUM(QTY),0) AS TotalQty,ISNULL(SUM(PICKQTY),0) AS TotalScanned 
            from Tbl_PickingDataRetail c with(nolock) left join WHLocation w with(nolock) on w.wh=c.wh 
            and w.Location=c.WHLOCATION where c.wh='" . $request->warehouseId . "' AND C.CUSTID='" . $request->clientId . "' and REF_NO ='" . $request->refNo . "' group by zone,zonebarcode ORDER BY ZONE ASC";

        $results = DB::select($picklistQuery);
        $zoneResult = DB::select($zone);
        return response()->json(['picklist' => $results, 'zone' => $zoneResult]);
    }

    public function getAiles(Request $request)
    {
        $query = "select Aisles,ISNULL(SUM(QTY),0) AS TotalQty,ISNULL(SUM(PICKQTY),0) AS TotalScanned,case when ISNULL(SUM(PICKQTY),0)=ISNULL(SUM(QTY),0)  then 1 else 0 end  as chck from Tbl_PickingDataRetail c with(nolock) left join WHLocation w with(nolock) on w.wh=c.wh and w.Location=c.WHLOCATION where c.wh='" . $request->warehouseId . "' AND C.CUSTID='" . $request->clientId . "' and REF_NO='" . $request->REF_NO . "' AND ZoneBarcode='" . $request->zone . "' group by Aisles ORDER BY CHCK, Aisles ASC";

        $aisles = DB::select($query);
        $count = count($aisles);
        if ($count > 0) {
            return response()->json($aisles);
        } else {
            return response()->json(['error' => 'noReocrd']);
        }
    }

    public function getRack(Request $request)
    {

        $query = "select C.WHLOCATION,ISNULL(SUM(QTY),0) AS TotalQty,ISNULL(SUM(PICKQTY),0) AS TotalScanned, COUNT(DISTINCT SKU) AS TOTALSKU,case when ISNULL(SUM(PICKQTY),0)=ISNULL(SUM(QTY),0)  then 1 else 0 end  as chck from Tbl_PickingDataRetail c with(nolock) left join WHLocation w with(nolock) on w.wh=c.wh and w.Location=c.WHLOCATION where c.wh='" . $request->warehouseId . "' AND C.CUSTID='" . $request->clientId . "' and REF_NO='" . $request->refNo . "' AND ZoneBarcode='" . $request->zoneBarCode . "' AND Aisles='" . $request->ailes . "' AND isnull(CCSTATUS,'')='' group by WHLOCATION ORDER BY CHCK, WHLOCATION ASC";

        $rack = DB::select($query);
        $count = count($rack);

        if ($count > 0) {
            return response()->json($rack);
        } else {
            return response()->json(['error' => 'noReocrd']);
        }
    }

    public function getRackLocation(Request $request)
    {

        $query = "select  sku,batchno,skudesc,whlocation,dnno,sum(qty) As InvQty,sum(pickqty) as PickQTy from 
        tbl_pickingdataretail with(nolock) where isnull(picklistno,'')='' and wh='" . $request->warehouseId . "' and dnno='" . $request->dnno . "' 
        and whlocation='" . $request->location . "' group by sku,batchno,skudesc,whlocation,dnno ORDER BY DNNO";


        $results = DB::select($query);


        $rackData = $this->arrayPaginator($results, $request->page, $request);
        return response($rackData);
    }


    // Sku update Manual Function Start

    public function updateSkuQtyValue(Request $request)
    {

        try {

            $query = "select whlocation, ISNULL(PICKQTY,0) AS TotalScanned , isnull(qty,0) as qty,sku from Tbl_PickingDataRetail with(nolock)  WHERE wh='" . $request->warehouseId . "' and custid='" . $request->clientId . "' and dnno ='" . $request->dnno . "' and sku='" . $request->sku . "'  and batchno='" . $request->batchNo . "' and whlocation = '" . $request->wlocation . "'";


            $countRow = DB::select($query);

            if ($countRow > 0) {

                if ($request->skuRadioButton == 1) {
                    $quantityToUpdate = (int) $countRow[0]->TotalScanned + $request->qty;
                } else {
                    $quantityToUpdate = (int) $countRow[0]->TotalScanned - $request->qty;
                }


                if ($quantityToUpdate > (int) $countRow[0]->qty || $quantityToUpdate < 0) {
                    // return response()->json(['error'=>'Picking Data Retail Record could not be updated due to negative quantity Or quantity is greater']);

                    return response()->json(['error' => 'err']);
                }


                $queryUpdate = "update Tbl_PickingDataRetail WITH(TABLOCK)  set PickQTy = '" . $quantityToUpdate . "' WHERE wh='" . $request->warehouseId . "' and custid='" . $request->clientId . "' and dnno ='" . $request->dnno . "' and sku='" . $request->sku . "'  and batchno='" . $request->batchNo . "' and whlocation = '" . $request->wlocation . "'";

                $updSuccess = DB::update($queryUpdate);

                if ($request->skuRadioButton == 1) {
                    $quantityToinsert = '+' . $request->qty;
                } else {

                    $quantityToinsert = '-' . $request->qty;
                }

                $insertQuery = "Insert into Tbl_Pickingsub WITH(TABLOCK) (wh, custid, dnno, ref_no, sku, batchno, qty, whlocation, entryby, entryon)values('" . $request->warehouseId . "','" . $request->clientId . "','" . $request->dnno . "','" . $request->REF_NO . "', '" . $request->sku . "', '" . $request->batchNo . "', '" . $quantityToinsert . "', '" . $request->wlocation . "','" . $request->userid . "', GETDATE())";


                $instSuccess = DB::insert($insertQuery);

                if ($instSuccess || $updSuccess) {
                    $selectQry = "select  sku,batchno,skudesc,whlocation,dnno,sum(qty) As InvQty,sum(pickqty) as PickQTy from 
                tbl_pickingdataretail with(nolock) where isnull(picklistno,'')='' and wh='" . $request->warehouseId . "' and dnno='" . $request->dnno . "' and whlocation='" . $request->wlocation . "' group by sku,batchno,skudesc,whlocation,dnno ORDER BY DNNO";

                    $dataRows = DB::select($selectQry);
                    //return response($dataRows);
                    $countselectvalues = count($dataRows);
                    // return response($countselectvalues);
                    if ($countselectvalues > 0) {
                        $rackData = $this->arrayPaginator($dataRows, $request->page, $request);
                        return response($rackData);
                    } else {
                        return response()->json(['error' => 'noReocrd']);
                    }
                }

            }


        } catch (\PDOException $e) {
            // Woopsy
            return response()->json(['error' => 'err']);

        }
        // Sku update Manual Function End
    }

    // SKU update using HHT Device Start

    public function updateSkuQtyValueHHT(Request $request)
    {

        try {
            // DB::beginTransaction();
            $query = "select whlocation, ISNULL(PICKQTY,0) AS TotalScanned , isnull(qty,0) as qty,sku from Tbl_PickingDataRetail with(nolock)  WHERE wh='" . $request->warehouseId . "' and custid='" . $request->clientId . "' and dnno ='" . $request->dnno . "' and sku='" . $request->sku . "'  and batchno='" . $request->batchNo . "' and whlocation = '" . $request->wlocation . "'";

            $countRow = DB::select($query);
            $numrow = count($countRow);

            if ($numrow > 0) {

                $quantityToUpdate = (int) $countRow[0]->TotalScanned + $request->qty;
                if ($quantityToUpdate > (int) $countRow[0]->qty || $quantityToUpdate < 0) {
                    // return response()->json(['error'=>'Picking Data Retail Record could not be updated due to negative quantity Or quantity is greater']);
                    return response()->json(['error' => 'err']);
                }
                $queryUpdates = "update Tbl_PickingDataRetail set PickQTy = " . $quantityToUpdate . " WHERE wh='" . $request->warehouseId . "' and custid='" . $request->clientId . "' and dnno ='" . $request->dnno . "' and sku='" . $request->sku . "'  and batchno='" . $request->batchNo . "' and whlocation = '" . $request->wlocation . "'";
                $updateQry = DB::update($queryUpdates);
                $quantityToinsert = '+' . $request->qty;
                $insertQuery = "Insert into Tbl_Pickingsub (wh, custid, dnno, ref_no, sku, batchno, qty, whlocation, entryby, entryon)values('" . $request->warehouseId . "','" . $request->clientId . "','" . $request->dnno . "','" . $request->REF_NO . "', '" . $request->sku . "', '" . $request->batchNo . "', '" . $quantityToinsert . "', '" . $request->wlocation . "','" . $request->userid . "', GETDATE())";

                $insertQry = DB::insert($insertQuery);
                if ($insertQry || $updateQry) {
                    $selectQry = "select  sku,batchno,skudesc,whlocation,dnno,sum(qty) As InvQty,sum(pickqty) as PickQTy from tbl_pickingdataretail with(nolock) where isnull(picklistno,'')='' and wh='" . $request->warehouseId . "' and dnno='" . $request->dnno . "' and whlocation='" . $request->wlocation . "' group by sku,batchno,skudesc,whlocation,dnno ORDER BY DNNO";

                    //return response($selectQry);
                    $dataRows = DB::select($selectQry);
                    //return response($dataRows);
                    $countselectvalues = count($dataRows);
                    // return response($countselectvalues);
                    if ($countselectvalues > 0) {
                        $rackData = $this->arrayPaginator($dataRows, $request->page, $request);
                        return response($rackData);
                    } else {
                        return response()->json(['error' => 'noReocrd']);
                    }
                }

                //return response()->json(['success'=>'success','skuupdateval'=>$skuupdateval]);
            }


            //DB::commit();
        } catch (\PDOException $e) {
            // Woopsy
            DB::rollBack();
        }

    }

    // SKU update using HHT Device End 


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


}
