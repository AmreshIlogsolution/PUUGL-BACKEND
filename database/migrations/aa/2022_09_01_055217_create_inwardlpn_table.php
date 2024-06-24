<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInwardlpnTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inwardlpn', function (Blueprint $table) {
            $table->id();           
            $table->uuid('warehouse_id');
            $table->uuid('client_id',1000);
            $table->string('invoiceNo',225);
            $table->string('asnNo',225);
            $table->string('skuCode',225);
            $table->string('batchNo',225);
            $table->string('lpnNo',225);
            $table->string('qty',225);
            $table->string('enteryBy',225);
            $table->string('enteryOn',225);
            $table->string('scanflag')->nullable();
            $table->timestamps();
           
           
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('inwardlpn');
    }
}
