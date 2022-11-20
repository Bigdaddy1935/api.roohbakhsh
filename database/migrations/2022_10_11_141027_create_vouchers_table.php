<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create( 'vouchers', function ( Blueprint $table ) {
            $table->bigIncrements( 'id' );

            // The voucher code
            $table->string( 'code' )->nullable( );

            // The description of the voucher - Not necessary
            $table->text( 'description' )->nullable( );

            // The number of uses currently
            $table->integer( 'uses' )->unsigned( )->nullable( );

            // The max uses this voucher has
            $table->integer( 'max_uses' )->unsigned()->nullable( );

            // How many times a user can use this voucher.
            $table->integer( 'max_uses_user' )->unsigned( )->nullable( );

            // The amount to discount by (in pennies) in this example.
            $table->integer( 'discount_amount' );

            // You know what this is...
            $table->timestamps( );

            // We like to horde data.
            $table->softDeletes( );
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vouchers');
    }
};
