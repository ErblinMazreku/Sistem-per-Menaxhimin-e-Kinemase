<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('reservation_seats')->whereNull('reserved_at')->orderBy('id')->each(function ($seat) {
            $reservedAt = $seat->created_at ?: DB::table('reservations')->where('id', $seat->reservation_id)->value('created_at');
            if ($reservedAt) {
                DB::table('reservation_seats')->where('id', $seat->id)->update(['reserved_at' => $reservedAt]);
            }
        });
    }

    public function down(): void
    {
        // Reservation timestamps are historical data and are intentionally retained on rollback.
    }
};
