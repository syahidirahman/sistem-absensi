<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->decimal('office_latitude', 10, 8)->nullable()->after('radius_default');
            $table->decimal('office_longitude', 11, 8)->nullable()->after('office_latitude');
        });
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn(['office_latitude', 'office_longitude']);
        });
    }
};  