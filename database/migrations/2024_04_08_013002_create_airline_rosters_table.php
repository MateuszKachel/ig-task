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
        Schema::create('airline_rosters', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Airline::class)->index()->constrained();
            $table->string('system');
            $table->string('file_type');
            $table->string('hash')->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('airline_rosters');
    }
};
