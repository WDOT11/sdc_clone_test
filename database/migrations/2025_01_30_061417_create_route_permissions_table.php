<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('route_permissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('route_id')->default(1)->constrained('routes', 'id')->cascadeOnDelete()->cascadeOnUpdate(); // Foreign key
            $table->foreignId('role_id')->default(1)->constrained('roles')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('service_provider_id')->default(1)->constrained('service_providers')->cascadeOnDelete()->cascadeOnUpdate();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('route_permissions');
    }
};
