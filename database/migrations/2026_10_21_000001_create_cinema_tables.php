<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('genres', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
        });

        Schema::create('movies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('genre_id')->constrained()->restrictOnDelete();
            $table->string('title');
            $table->text('description');
            $table->unsignedSmallInteger('duration_minutes');
            $table->string('age_rating', 10)->default('PG-13');
            $table->string('poster_path')->nullable();
            $table->timestamps();
            $table->index('title');
        });

        Schema::create('halls', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->unsignedSmallInteger('capacity');
            $table->timestamps();
        });

        Schema::create('seats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hall_id')->constrained()->cascadeOnDelete();
            $table->string('row_label', 5);
            $table->unsignedSmallInteger('seat_number');
            $table->timestamps();
            $table->unique(['hall_id', 'row_label', 'seat_number']);
        });

        Schema::create('showtimes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('movie_id')->constrained()->restrictOnDelete();
            $table->foreignId('hall_id')->constrained()->restrictOnDelete();
            $table->date('show_date');
            $table->time('start_time');
            $table->decimal('ticket_price', 8, 2);
            $table->timestamps();
            $table->index(['show_date', 'hall_id']);
        });

        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('showtime_id')->constrained()->restrictOnDelete();
            $table->string('status')->default('active')->index();
            $table->string('booking_reference')->unique();
            $table->timestamps();
        });

        Schema::create('reservation_seats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reservation_id')->constrained()->cascadeOnDelete();
            $table->foreignId('showtime_id')->constrained()->restrictOnDelete();
            $table->foreignId('seat_id')->constrained()->restrictOnDelete();
            $table->timestamps();
            $table->unique(['reservation_id', 'seat_id']);
            $table->index(['showtime_id', 'seat_id']);
        });

        // This allocation table is the database-enforced guard against double booking.
        Schema::create('active_seat_allocations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('showtime_id')->constrained()->cascadeOnDelete();
            $table->foreignId('seat_id')->constrained()->cascadeOnDelete();
            $table->foreignId('reservation_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['showtime_id', 'seat_id']);
            $table->unique('reservation_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('active_seat_allocations');
        Schema::dropIfExists('reservation_seats');
        Schema::dropIfExists('reservations');
        Schema::dropIfExists('showtimes');
        Schema::dropIfExists('seats');
        Schema::dropIfExists('halls');
        Schema::dropIfExists('movies');
        Schema::dropIfExists('genres');
    }
};
