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
        Schema::create('media', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->string('file_path');
            $table->string('type')->nullable();
            // $table->enum('type', ['video', 'image', 'document']);
            $table->enum('extension', [
                // Extensions d'image détaillées
                'jpg', 'jpeg', 'png', 'gif', 'bmp', 'svg', 'webp',

                // Liste détaillée des extensions vidéo en enum
                'mp4', 'mov', 'wmv', 'avi', 'flv', 'mkv', 'webm',

                // Liste détaillée des extensions de document en enum
                'pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt'
            ]);
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
            $table->boolean('deleted')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('media');
    }
};
