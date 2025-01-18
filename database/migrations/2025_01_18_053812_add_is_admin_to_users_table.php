<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_admin')->default(false)->after('id');
            $table->string('phone')->nullable()->after('email');
            $table->unsignedTinyInteger('status')->default('0')->comment('1=> Active , 0 => Inactive')->after('remember_token');
        });
    }
    
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('is_admin');
            $table->dropColumn('phone');
            $table->dropColumn('status');
        });
    }
    
};
