<?php

use App\Models\Airline;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('airline_events', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Airline::class)->index()->constrained();
            $table->date('date')->index();
            $table->string('dc')->default('');
            $table->dateTime('check_in_time_utc')->nullable();
            $table->dateTime('check_out_time_utc')->nullable();
            $table->char('activity_type', 3)->index();
            $table->string('activity')->index();
            $table->string('activity_remark')->default('');
            $table->string('departure_airport')->index();
            $table->dateTime('departure_time_utc')->nullable();
            $table->string('arrival_airport')->index();
            $table->dateTime('arrival_time_utc')->nullable();
            $table->string('ac_hotel')->default('');
            $table->string('block_hours')->default('');
            $table->string('flight_time')->default('');
            $table->string('night_time')->default('');
            $table->string('duration')->default('');
            $table->string('ext')->default('');
            $table->string('pax_booked')->default('');
            $table->string('tail_number')->default('');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('airline_events');
    }
};
