<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDispatchOrder extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dispatch_orders', function (Blueprint $table) {
           
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('seller_id')->nullable();
            $table->unsignedInteger('responsible_id')->nullable();
            $table->uuid('external_id');
            $table->unsignedInteger('establishment_id');
            $table->json('establishment');
            $table->char('soap_type_id', 2);
            $table->unsignedInteger('dispatch_order_state_id')->default(1);
            $table->char('state_type_id', 2);   
            $table->char('prefix', 4); 
            $table->string('number', 8);
            $table->date('date_of_issue');
            $table->time('time_of_issue');
            $table->date('date_of_due')->nullable();
            $table->date('delivery_date')->nullable();
            $table->unsignedInteger('customer_id');
            $table->unsignedInteger('sale_note_id');
            $table->json('customer');
            $table->text('shipping_address')->nullable();
            $table->string('currency_type_id');
            $table->char('payment_method_type_id', 2)->nullable();
            $table->decimal('exchange_rate_sale', 13, 3);
            $table->decimal('total_prepayment', 12, 2)->default(0);
            $table->decimal('total_charge', 12, 2)->default(0);
            $table->decimal('total_discount', 12, 2)->default(0);
            $table->decimal('total_exportation', 12, 2)->default(0);
            $table->decimal('total_free', 12, 2)->default(0);
            $table->decimal('total_taxed', 12, 2)->default(0);
            $table->decimal('total_unaffected', 12, 2)->default(0);
            $table->decimal('total_exonerated', 12, 2)->default(0);
            $table->decimal('total_igv', 12, 2)->default(0);
            $table->decimal('total_igv_free', 12, 2)->default(0);
            $table->decimal('total_base_isc', 12, 2)->default(0);
            $table->decimal('total_isc', 12, 2)->default(0);
            $table->decimal('total_base_other_taxes', 12, 2)->default(0);
            $table->decimal('total_other_taxes', 12, 2)->default(0);
            $table->decimal('total_taxes', 12, 2)->default(0);
            $table->decimal('total_value', 12, 2)->default(0);
            $table->decimal('total', 12, 2);

            $table->json('charges')->nullable();
            $table->json('discounts')->nullable();
            $table->json('prepayments')->nullable();
            $table->json('guides')->nullable();
            $table->json('related')->nullable();
            $table->json('perception')->nullable();
            $table->json('detraction')->nullable();
            $table->json('legends')->nullable();
            $table->json('additional_data')->nullable();
            $table->string('filename')->nullable(); 
            $table->text('observation')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('seller_id')->references('id')->on('users');
            $table->foreign('responsible_id')->references('id')->on('users');
            $table->foreign('establishment_id')->references('id')->on('establishments');
            $table->foreign('customer_id')->references('id')->on('persons');
            $table->foreign('dispatch_order_state_id')->references('id')->on('state_dispatch_orders');
            $table->foreign('sale_note_id')->references('id')->on('sale_notes');
            $table->foreign('soap_type_id')->references('id')->on('soap_types');
            $table->foreign('state_type_id')->references('id')->on('state_types');  
            $table->foreign('currency_type_id')->references('id')->on('cat_currency_types');
            $table->foreign('payment_method_type_id')->references('id')->on('payment_method_types');
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('dispatch_orders');        
    }
}
