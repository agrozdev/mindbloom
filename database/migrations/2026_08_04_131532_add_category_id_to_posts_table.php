<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->foreignId('category_id')->nullable()->after('category')->constrained()->nullOnDelete();
        });

        $categoryIdsByName = [];

        foreach (DB::table('posts')->whereNotNull('category')->where('category', '!=', '')->get() as $post) {
            $name = trim($post->category);

            if ($name === '') {
                continue;
            }

            if (! isset($categoryIdsByName[$name])) {
                $slug = Str::slug($name);
                $uniqueSlug = $slug;
                $suffix = 1;

                while (DB::table('categories')->where('slug', $uniqueSlug)->exists()) {
                    $uniqueSlug = $slug.'-'.$suffix++;
                }

                $categoryIdsByName[$name] = DB::table('categories')->insertGetId([
                    'name' => $name,
                    'slug' => $uniqueSlug,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            DB::table('posts')->where('id', $post->id)->update([
                'category_id' => $categoryIdsByName[$name],
            ]);
        }

        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn('category');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->string('category')->nullable()->after('featured_image');
        });

        foreach (DB::table('posts')->whereNotNull('category_id')->get() as $post) {
            $category = DB::table('categories')->find($post->category_id);

            if ($category) {
                DB::table('posts')->where('id', $post->id)->update(['category' => $category->name]);
            }
        }

        Schema::table('posts', function (Blueprint $table) {
            $table->dropConstrainedForeignId('category_id');
        });
    }
};
