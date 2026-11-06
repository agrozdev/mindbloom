<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Redirect extends Model
{
    protected $fillable = [
        'from_path',
        'to_path',
        'hits',
    ];

    /**
     * Register old -> new path, with the same bookkeeping as `content:reslug`:
     * redirects that pointed at the old path move to the new one, and a row
     * whose source is the new path (it would loop) is dropped.
     */
    public static function record(string $oldPath, string $newPath): void
    {
        if ($oldPath === $newPath) {
            return;
        }

        static::where('to_path', $oldPath)->update(['to_path' => $newPath]);
        static::where('from_path', $newPath)->delete();
        static::updateOrCreate(['from_path' => $oldPath], ['to_path' => $newPath]);
    }
}
