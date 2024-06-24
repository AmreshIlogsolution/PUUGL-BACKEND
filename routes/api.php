<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AsnController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\putAwayController;
use App\Http\Controllers\PicklistController;
use App\Http\Controllers\RfidSearchController;
use App\Http\Controllers\BinMovementController;
use App\Http\Controllers\ImageUploadController;
use App\Http\Controllers\RFIDOprationController;
use App\Http\Controllers\PlantController;
use App\Http\Controllers\skuIdentificationController;
use App\Http\Controllers\ImageUploadAzureController;
use App\Http\Controllers\VechileVerificationController;
use App\Http\Controllers\ImageUploadOutboundAzureController;
use App\Http\Controllers\ImpageUploadOutwardDocumentController;
use App\Http\Controllers\UploadInvoiceDocumentAzureController;
use App\Http\Controllers\UploadInvoiceDocument;
 
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::post('/logout', [AuthController::class, 'logout']);
    // Route::resource('/survey',[App\Http\Controllers\SurveyController::class]);

});


Route::post('importRoom', [PlantController::class, 'import']);


Route::get('get-client-warehousewise', [App\Http\Controllers\AsnController::class, 'findClientWarehouseWise']);

Route::get('get-client-userwise', [App\Http\Controllers\AsnController::class, 'findClienUserWise']);

Route::post('asn-find', [AsnController::class, 'findAsn']);
Route::get('search-asn', [AsnController::class, 'searchAsn']);


Route::get('get-warehouse-name', [AsnController::class, 'getWareHouseName']);
Route::get('get-client-name', [AsnController::class, 'getCleintName']);
Route::get('get-asnDetails', [AsnController::class, 'getAsnDetails']);


Route::post('uploads-received-documents', [ImageUploadController::class, 'UploadsReceivedDocuments']);


Route::post('uploads-received-documents-azure', [ImageUploadAzureController::class, 'UploadsReceivedDocumentsAzure']);


Route::get('asn-search-images', [ImageUploadController::class, 'asnSearchImages']);

Route::post('uploads-invoice-documents', [UploadInvoiceDocumentAzureController::class, 'uploadsInvoiceDocuments']);





Route::get('asn-inward-receiving', [ImageUploadController::class, 'asnInwardReceiving']);

// 16-5-2024
Route::get('outbound-serach-images', [ImageUploadController::class, 'OutboundSearchImages']);

//Route::post('uploads-outbound-documents', [ImageUploadController::class, 'uploadsOutboundDocuments']);

Route::post('uploads-outbound-documents', [ImpageUploadOutwardDocumentController::class, 'uploadsOutboundDocumentsAzure']);
                                                                                          
Route::get('vehicle-out-invoice-search', [ImageUploadController::class, 'VehicleOutInvoiceSearch']);

Route::post('uploads-outvehicles-documents', [ImageUploadController::class, 'UploadsOutVehiclesDocuments']);

Route::post('uploads-outvehicles-documents-azure', [ImageUploadOutboundAzureController::class, 'UploadsOutVehiclesDocumentsAzure']);
Route::get('asn-list', [AsnController::class, 'asnListValue']);

Route::get('get-skudetails-bins', [ImageUploadController::class, 'getBinContent']);


Route::get('asn-detail-values/', [AsnController::class, 'asnDetailValue']);
Route::get('sku-insert-update-values/', [AsnController::class, 'skuInserUpdateValues']);

Route::get('sku-insert-update-values-multiple/', [AsnController::class, 'skuInserUpdateValuesMultiple']);


Route::get('get-warehouse-name', [AsnController::class, 'getWareHouseName']);
Route::get('get-client-name', [AsnController::class, 'getCleintName']);
Route::get('/search-SKU', [AsnController::class, 'getSKU']);

Route::get('search-lpn-box', [AsnController::class, 'validateLPNBox']);

Route::get('/scan-sku', [AsnController::class, 'scanSku']);

Route::post('/register', [AuthController::class, 'register']);

Route::post('/login', [AuthController::class, 'login']);
Route::middleware(['check.date'])->group(function () {
   
 
});



Route::get('from-bin-search', [BinMovementController::class, 'fromBinSearch']);
Route::get('scan-to-transfer', [BinMovementController::class, 'scanToBinTransfer']);
Route::post('bin-to-bin-transfer-save', [BinMovementController::class, 'binToBinTransferSave']);

Route::get('from-bin-location-search', [BinMovementController::class, 'binToLocationSearch']);
Route::get('scan-sku-bin-transfer', [BinMovementController::class, 'scanSkuBinTransfer']);
Route::get('get-tobin-location-search', [BinMovementController::class, 'getToBinLocationSearch']);
Route::post('bin-to-location-transfer', [BinMovementController::class, 'binToLocationTransferSave']);

// HHD@password1234

// HHD@password123

// Alpha_9090()

// sudo apt install mysql-server

// sudo apt-get install -y php8.1-cli php8.1-json php8.1-common php8.1-mysql php8.1-zip php8.1-gd php8.1-mbstring php8.1-curl php8.1-xml php8.1-bcmath
Route::get('update-sku-put-away/', [putAwayController::class, 'updateSkuPutAway']);
// Put away Route End            

// pick list url
Route::get('/pending-pick-list', [PicklistController::class, 'PendingPickListData']);
Route::get('/pending-pick-list-box', [PicklistController::class, 'PendingPickListBoxData']);

Route::get('/get-ailes', [PicklistController::class, 'getAiles']);

Route::get('/get-rack', [PicklistController::class, 'getRack']);

Route::get('/get-rack-location', [PicklistController::class, 'getRackLocation']);


Route::get('/update-sku-qty-value', [PicklistController::class, 'updateSkuQtyValue']);
Route::get('/update-sku-qty-value-hht', [PicklistController::class, 'updateSkuQtyValueHHT']);

Route::get('/update-skuqty-value', [PicklistController::class, 'updateSkuQtyValue']);
// end pick list url

// Put Away Route Start
Route::get('search-Mrn-Invoice/', [putAwayController::class, 'findMrnInvoice']);
Route::get('search-Mrn-Invoice-withid/', [putAwayController::class, 'findMrnInvoicewithid']);
Route::get('search-bin/', [putAwayController::class, 'findbin']);

Route::get('search-bin-sku', [putAwayController::class, 'searchbinsku']);

Route::get('update-sku-put-away/', [putAwayController::class, 'updateSkuPutAway']);
// Put away Route End   

Route::post('rfid-operationsearch', [RFIDOprationController::class, 'RfidOprationSearch']);
route::post('/rfid-search', [RfidSearchController::class, 'rfidSearch']);

route::post('/rfid-search-child', [RFIDOprationController::class, 'rfidSearchchild']);

route::post('/rfid-search-inventorybypack', [RfidSearchController::class, 'rfidSearchInventoryByPack']);

route::post('/rfid-search-inventoryalldata', [RfidSearchController::class, 'inventoryAllData']);



// SKU identification


Route::get('search-zone/', [skuIdentificationController::class, 'findzoneforSku']);

Route::get('search-ailse', [skuIdentificationController::class, 'findaisleforSku']);

Route::get('search-bin', [skuIdentificationController::class, 'findBinforSku']);


// Vechile verifiction

 
route::post('/vechile-verification', [VechileVerificationController::class, 'verifyVechileNumber']);


//Driving Lincence varification

route::post('/dl-verification', [VechileVerificationController::class, 'verifyDLNumber']);


// DL add to master


route::post('/dl-addToMaster', [VechileVerificationController::class, 'dlAddToMaster']);
route::post('/vh-addToMaster', [VechileVerificationController::class, 'vhAddToMaster']);