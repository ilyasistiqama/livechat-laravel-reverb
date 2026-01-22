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
        Schema::table('chats', function (Blueprint $table) {
            $table->enum('status', ['sent', 'delivered', 'read'])
                ->default('sent')
                ->after('message');
            $table->boolean('finished')->default(false)->after('status');
            $table->timestamp('created_for_reset')->nullable()->after('finished');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('chats', function (Blueprint $table) {
            $table->dropColumn('created_for_reset');
            $table->dropColumn('finished');
            $table->dropColumn('status');
        });
    }
};
