<?php

use App\Enums\SectionStatusEnum;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Hero Section
        Schema::create('hero_sections', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('subtitle')->nullable();
            $table->string('status')->default(SectionStatusEnum::DRAFT->value);
            $table->foreignIdFor(User::class)->constrained()->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('hero_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hero_section_id')->constrained()->onDelete('cascade');
            $table->string('image_path');
            $table->integer('order')->default(0);
            $table->timestamps();
        });

        // Service Section
        Schema::create('service_sections', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('title_short')->nullable();
            $table->text('summary');
            $table->string('summary_short')->nullable();
            $table->string('icon')->nullable();
            $table->string('image_path')->nullable();
            $table->integer('order')->default(0);
            $table->string('status')->default(SectionStatusEnum::DRAFT->value);
            $table->foreignIdFor(User::class)->constrained()->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });

        // About Section
        Schema::create('about_sections', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('summary');
            $table->string('image_path')->nullable();
            $table->string('status')->default(SectionStatusEnum::DRAFT->value);
            $table->foreignIdFor(User::class)->constrained()->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });

        // Product Section
        Schema::create('product_sections', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('summary');
            $table->string('image_path')->nullable();
            $table->integer('order')->default(0);
            $table->string('status')->default(SectionStatusEnum::DRAFT->value);
            $table->foreignIdFor(User::class)->constrained()->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });

        // Client Section
        Schema::create('client_sections', function (Blueprint $table) {
            $table->id();
            $table->string('company_name');
            $table->string('logo_path');
            $table->integer('order')->default(0);
            $table->string('status')->default(SectionStatusEnum::DRAFT->value);
            $table->foreignIdFor(User::class)->constrained()->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });

        // Case Study Section
        Schema::create('case_study_sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_section_id')->constrained()->onDelete('cascade');
            $table->string('banner_image')->nullable();
            $table->string('company_name');
            $table->string('subtitle')->nullable();
            $table->text('description');
            $table->text('challenge')->nullable();
            $table->text('solution')->nullable();
            $table->text('results')->nullable();
            $table->string('status')->default(SectionStatusEnum::DRAFT->value);
            $table->foreignIdFor(User::class)->constrained()->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('case_study_sections');
        Schema::dropIfExists('client_sections');
        Schema::dropIfExists('product_sections');
        Schema::dropIfExists('about_sections');
        Schema::dropIfExists('service_sections');
        Schema::dropIfExists('hero_images');
        Schema::dropIfExists('hero_sections');
    }
};
