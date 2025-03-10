<?php

use App\Enums\CareerStatusEnum;
use App\Models\User;
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
        Schema::create('careers', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('subtitle')->nullable();
            $table->text('description');
            $table->string('location');
            $table->string('format'); // remote, onsite, hybrid
            $table->string('department')->nullable();
            $table->string('employment_type')->nullable(); // full-time, part-time, contract
            $table->decimal('salary_min', 10, 2)->nullable();
            $table->decimal('salary_max', 10, 2)->nullable();
            $table->string('currency', 3)->nullable();
            $table->string('application_email');
            $table->text('requirements')->nullable();
            $table->text('benefits')->nullable();
            $table->string('banner_image')->nullable();
            $table->string('status')->default(CareerStatusEnum::DRAFT->value);
            $table->timestamp('published_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->foreignIdFor(User::class)->constrained()->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('careers');
    }
};
