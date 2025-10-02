<?php declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('name')->nullable(false)->index();
            $table->string('url')->nullable(false)->index();
            $table->string('address')->nullable(false)->index();
            $table->foreignId('thumbnail_id')->nullable()->constrained('images');
            $table->text('description');
            $table->unsignedInteger('price_min')->nullable(false);
            $table->unsignedInteger('price_max')->nullable(false);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sites');
    }
};
