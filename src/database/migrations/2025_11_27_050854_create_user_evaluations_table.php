<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserEvaluationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_evaluations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('targeter_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('evaluator_id')->nullable()->constrained('users')->nullOnDelete();
            $table->integer('score');
            $table->timestamp('created_at')->useCurrent()->nullable();
            $table->timestamp('updated_at')->useCurrent()->nullable();

            $table->unique(['targeter_id', 'evaluator_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_evaluations');
    }
}
