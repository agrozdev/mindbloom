<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement('ALTER TABLE events MODIFY excerpt TEXT NULL');
        DB::statement('ALTER TABLE posts MODIFY excerpt TEXT NULL');
        DB::statement('ALTER TABLE services MODIFY excerpt TEXT NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('ALTER TABLE events MODIFY excerpt VARCHAR(255) NULL');
        DB::statement('ALTER TABLE posts MODIFY excerpt VARCHAR(255) NULL');
        DB::statement('ALTER TABLE services MODIFY excerpt VARCHAR(255) NULL');
    }
};
