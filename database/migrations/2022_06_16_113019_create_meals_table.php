<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMealsTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('meals', function (Blueprint $table) {
            $table->id();
            $table->string('unique_id');
            $table->string('user_id');
            $table->string('category')->nullable();
            $table->string('name');
            $table->string('thumbnail')->nullable();
            $table->longText('description')->nullable();
            $table->string('price')->default(0);
            $table->longText('images');
            $table->string('video')->nullable();
            $table->string('discount')->nullable();
            $table->string('tax')->nullable();
            $table->string('rating')->default(1);
            $table->string('availability');
            $table->string('promoted');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('meals');
    }
}
