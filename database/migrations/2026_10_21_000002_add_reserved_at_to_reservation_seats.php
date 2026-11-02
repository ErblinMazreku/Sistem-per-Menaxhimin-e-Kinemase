<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reservation_seats', function (Blueprint $table) {
            $table->timestamp('reserved_at')->nullable()->after('seat_id')->index();
        });

        DB::table('reservation_seats')->whereNull('reserved_at')->update([
            'reserved_at' => DB::raw('created_at'),
        ]);
    }

    public function down(): void
    {
        Schema::table('reservation_seats', function (Blueprint $table) {
            $table->dropColumn('reserved_at');
        });
    }
};
