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
        Schema::create('chats', function (Blueprint $table) {
            $table->id();

            $table->string('room_code');

            $table->unsignedBigInteger('from_id');
            $table->string('from_type');

            $table->unsignedBigInteger('to_id');
            $table->string('to_type');

            $table->text('message');

            $table->enum('type', [
                'customer-to-admin',
                'customer-to-customer'
            ])->default('customer-to-admin');

            $table->enum('status', ['sent', 'delivered', 'read'])
                ->default('sent');

            $table->string('page')->default('global');
            $table->boolean('finished')->default(false);

            $table->timestamps();

            $table->index('room_code');
            $table->index(['to_id', 'to_type', 'finished']);
            $table->index(['from_id', 'from_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chats');
    }
};
