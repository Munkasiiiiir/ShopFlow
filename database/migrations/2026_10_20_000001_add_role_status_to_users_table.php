<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone', 20)->nullable()->after('email');
            $table->enum('role', ['admin', 'manager', 'cashier'])->default('cashier')->after('password');
            $table->enum('status', ['pending', 'approved', 'rejected', 'inactive'])->default('pending')->after('role');
            $table->foreignId('approved_by')->nullable()->after('status')->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable()->after('approved_by');
            $table->index(['role', 'status']);
        });
    }

    public function down(): void
    {
        Schema::table('users', fn (Blueprint $t) => $t->dropConstrainedForeignId('approved_by'));
        Schema::table('users', fn (Blueprint $t) => $t->dropColumn(['phone', 'role', 'status', 'approved_at']));
    }
};
