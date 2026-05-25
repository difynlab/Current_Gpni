<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('technical_support_replies', function (Blueprint $table) {
            $table->id();

            $table->string('technical_support_id');
            $table->string('replied_by');
            $table->text('message');
            $table->date('date');
            $table->time('time');
            $table->enum('admin_viewed', [0, 1]);
            $table->enum('user_viewed', [0, 1]);
            $table->enum('status', [0, 1])->index();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('technical_support_replies');
    }
};
