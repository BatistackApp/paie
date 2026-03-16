<?php

use App\Models\Chantier;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('time_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Chantier::class)->constrained();
            $table->date('entry_date');
            $table->time('depart_depot')->nullable();
            $table->time('embauche_chantier');
            $table->time('debauche_chantier');
            $table->time('retour_depot')->nullable();
            $table->integer('break_duration_minute')->default(60);
            $table->boolean('has_meal')->default(true);
            $table->boolean('has_night')->default(false);
            $table->boolean('is_validated')->default(false);
            $table->dateTime('validated_at')->nullable();
            $table->decimal('work_duration', 8, 2)->default(0)->index();
            $table->decimal('travel_duration', 8, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('time_entries');
    }
};
