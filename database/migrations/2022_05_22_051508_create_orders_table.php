<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admin_id')->constrained('admins')->onDelete('cascade');
            $table->string('name');
            $table->string('address');
            $table->string('country');
            $table->string('division');
            $table->string('district');
            $table->string('zip_code');
            $table->string('mobile');
            $table->string('shipping_charges');
            $table->string('order_status');
            $table->string('payment_method');
            $table->string('transaction_id')->nullable();
            $table->string('payment_status')->nullable();
            $table->double('grand_total');

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
        Schema::dropIfExists('orders');
    }
}
