<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('buku')) {
            return;
        }

        Schema::table('buku', function (Blueprint $table) {
            if (!Schema::hasColumn('buku', 'cover')) {
                if (Schema::hasColumn('buku', 'stok')) {
                    $table->string('cover')->nullable()->after('stok');
                } else {
                    $table->string('cover')->nullable();
                }
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('buku')) {
            return;
        }

        Schema::table('buku', function (Blueprint $table) {
            if (Schema::hasColumn('buku', 'cover')) {
                $table->dropColumn('cover');
            }
        });
    }
};