<?php
namespace App\Traits;

use App\Models\User;
use App\Models\Warehouse;
use App\Models\Client;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Traits\UserWarehousesTrait;

trait UserClientsTrait
{

    public function getUserClients()
    {
        $userId = Auth::user()->id;
        $WareHousesId = $this->getUserWarehouses();

        if (empty($WareHousesId) && $WareHousesId == false) {
            redirect('clients')->with('error', 'You dont have any warehouse please create')->send();
        } else {

            $clientData = DB::table('clients')->select('clients.id', 'clients.clientCode', 'clients.created_at')->join('warehouse_client_mappings', 'warehouse_client_mappings.client_id', '=', 'clients.id')->whereIn('warehouse_client_mappings.warehouse_id', $WareHousesId)->where('clients.status', 1)->get();

            if (empty($clientData)) {
                redirect('clients')->with('error', 'You dont have any warehouse please create')->send();

            } else {

                foreach ($clientData as $clients) {
                    $client_id[] = $clients->id;
                }
                if (!empty($client_id)) {
                    return $client_id;
                } else {
                    return false;
                }

            }

        }
    }
}