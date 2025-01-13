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
        Schema::create('task_user', function (Blueprint $table) {
            $table->id();
            $table->string('file_path')->nullable();
            $table->date('summation_date')->nullable();
            $table->foreignId('student_id')->constrained('users');
            $table->foreignId('task_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->text('note')->nullable();
            $table->tinyInteger('grade')->nullable()->unsigned()->check('grade >= 0 AND grade <= 10');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_user');
    }
};
