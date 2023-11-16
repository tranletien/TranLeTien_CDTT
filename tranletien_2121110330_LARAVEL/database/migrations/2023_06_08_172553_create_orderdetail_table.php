<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   
    public function up():void
    {
        Schema::create('db_orderdetail', function (Blueprint $table) {
            $table->id(); //id
            $table->unsignedInteger('order_id')->default(1);
            $table->unsignedInteger('product_id')->default(1);
            $table->unsignedInteger('price');
            $table->unsignedInteger('qty')->default(1);
            $table->unsignedInteger('amount')->default(1);



       
        });
    }


    public function down()
    {
        Schema::dropIfExists('db_orderdetail');
    }
};
