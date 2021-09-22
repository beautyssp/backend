<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSuppliersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->char('name', 100);
            $table->string('nit', 250);
            $table->string('email', 250);
            $table->string('telephone', 50);
            $table->string('cellphone', 50);
            $table->string('address', 200);
            $table->string('city', 200);
            $table->string('country', 200);
            $table->string('legal_representative');
            $table->enum('type_person',['Natural','Juridica']);
            $table->string('economic_activity');
            $table->string('banco');
            $table->foreignId('bank_certificate')->nullable();
            $table->foreign('bank_certificate')->references('id')->on('files')->onDelete('cascade');

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
        Schema::dropIfExists('suppliers');
    }
}
