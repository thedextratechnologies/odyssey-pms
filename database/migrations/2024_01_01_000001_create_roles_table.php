<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // super_admin, sales_director, zone_manager, bdm, bde
            $table->string('display_name');
            $table->text('description')->nullable();
            $table->integer('level')->default(0); // 5=super_admin,4=sd,3=zm,2=bdm,1=bde
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
