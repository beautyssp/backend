<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHistoryChangeProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('history_change_products', function (Blueprint $table) {
            $table->id();

            $table->foreignId('product_id');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');

            $table->foreignId('warehouse_from')->nullable();
            $table->foreign('warehouse_from')->references('id')->on('warehouses')->onDelete('cascade');

            $table->foreignId('warehouse_to')->nullable();
            $table->foreign('warehouse_to')->references('id')->on('warehouses')->onDelete('cascade');

            $table->integer('quantity');

            $table->foreignId('create_by');
            $table->foreign('create_by')->references('id')->on('users')->onDelete('cascade');

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
        Schema::dropIfExists('history_change_products');
    }
}
