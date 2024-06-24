<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Asn extends Model
{
    use HasFactory;

    protected $fillable = [
        'warehouse_id',
        'client_id',
        'asn_no',
        'po_number',
        'fromCity',
        'stnRefNo',
        'invoiceNo',
        'invoiceDate',
        'amount',
        'currency',
        'billLandingNo',
        'containerNo',
        'vehicle',
        'lrNo',
        'vendor_id',
        'transactionType',
        'status',
        'createdById',
        'computerName',
        'computerIp',
        'updatedId',
        'supplier_id',
        'vendorInvoiceNo',
        'financialYear'
    ];
}
