<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('payer');
            $table->foreign('payer')->references('id')->on('users')
            ->onUpdate('cascade')
            ->onDelete('cascade');
            
            $table->unsignedBigInteger('payee');
            $table->foreign('payee')->references('id')->on('users')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->decimal('value',9,2);
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
        Schema::table('payments', function (Blueprint $table){
            $table->dropForeign(['payer']);
            $table->dropForeign(['payee']);

        });
        Schema::dropIfExists('payments');
    }
}
