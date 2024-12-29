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
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->date('start_register_date');
            $table->date('end_register_date');
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('status',['Open','Closed']);
            $table->foreignId('teacher_id')->constrained()->onDelete('cascade');
            $table->foreignId('category_id')->constrained('categories','id')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
