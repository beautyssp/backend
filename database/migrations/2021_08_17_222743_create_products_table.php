<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('ean');
            $table->string('sku_plu');
            $table->char('name', 100);
            $table->string('price');
            $table->integer('units');

            $table->foreignId('health_register_file_id')->nullable();
            $table->foreign('health_register_file_id')->references('id')->on('files')->onDelete('cascade');

            $table->foreignId('subcategory_id');
            $table->foreign('subcategory_id')->references('id')->on('subcategories')->onDelete('cascade');

            $table->foreignId('supplier_id');
            $table->foreign('supplier_id')->references('id')->on('suppliers')->onDelete('cascade');

            $table->foreignId('create_by');
            $table->foreign('create_by')->references('id')->on('users')->onDelete('cascade');

            $table->foreignId('last_update_by')->nullable();
            $table->foreign('last_update_by')->references('id')->on('users')->onDelete('cascade');

            $table->char('brand',100);
            //$table->foreignId('brand_id');
            //$table->foreign('brand_id')->references('id')->on('brands')->onDelete('cascade');

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
        Schema::dropIfExists('products');
    }
}
