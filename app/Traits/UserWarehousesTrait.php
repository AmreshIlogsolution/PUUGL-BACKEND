<?php
namespace App\Traits;

use Throwable;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;




trait UserWarehousesTrait {


    
    public function getUserWarehouses() {

       
        try {
        // Validate the value...
        $userId=Auth::user()->id;
        //(Auth::user()->isAdmin() == 'Administrator');
        // Fetch user assign Warehouses   from the 'warehouses' table.
        if (DB::table('warehouses')->where('status', 1)->exists()) 
        {
            $userWarehouses =  DB::table('warehouses')->select('warehouses.id as warehouse_id','warehouses.whId')->join('user_warehouse','user_warehouse.warehouse_id','=','warehouses.id')->where('user_warehouse.user_id',Auth::user()->id)->where('warehouses.status',1)->get()->toArray();
        
        if($userWarehouses==null){

            return false;

            }else{
                foreach($userWarehouses as $userwarehouse){
                $warehouse_id[]=$userwarehouse->warehouse_id;
                }
           
    
            if(!empty($warehouse_id)){
                return $warehouse_id;
            }else{
                return false;
            }



        }
              
            
        }else{

            redirect('warehouse')->with('error','You dont have any warehouse please create')->send();
        }


         


        } catch (Throwable $e) {
            redirect('warehouse')->with('error','You dont have any warehouse please create')->send();
        }


     

      
       
       

    }
}