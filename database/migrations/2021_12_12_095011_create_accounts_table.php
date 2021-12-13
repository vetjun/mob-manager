<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('accounts', function (Blueprint $table) {
            // Not Known If some fields are absolutely number or UUID. So used varchar type
            $table->id();
            $table->string('device_uid');
            $table->string('app_id');
            $table->string('language');
            $table->string('operation_system');
            $table->index('language');
            $table->index('operation_system');
            $table->string('client_token')->unique();
            $table->unique(array('device_uid', 'app_id'));
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
        Schema::dropIfExists('accounts');
    }
}
