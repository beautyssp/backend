<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBillsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bills', function (Blueprint $table) {
            $table->id();
            $table->string('total');
            $table->string('discounts');
            $table->string('observations');
            $table->string('subtotal');
            $table->foreignId('client_id');
            $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');
            $table->foreignId('warehouse_id');
            $table->foreign('warehouse_id')->references('id')->on('warehouses')->onDelete('cascade');

            $table->foreignId('create_by');
            $table->foreign('create_by')->references('id')->on('users')->onDelete('cascade');

            $table->foreignId('last_update_by')->nullable();
            $table->foreign('last_update_by')->references('id')->on('users')->onDelete('cascade');
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bills');
    }
}
