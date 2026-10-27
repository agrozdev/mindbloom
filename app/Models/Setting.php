<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

/**
 * Key/value settings edited from the „Настройки“ admin page. Missing keys
 * fall back to the defaults below.
 */
class Setting extends Model
{
    protected $primaryKey = 'key';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = ['key', 'value'];

    private const CACHE_KEY = 'settings.all';

    public const DEFAULTS = [
        'mail_from_name' => null,
        'mail_from_address' => null,
        'mail_reply_to' => null,
        'read_opened_seconds' => 15,
        'read_min_seconds' => 60,
        'read_min_scroll' => 70,
        'gate_visible_percent' => 35,
        'privacy_policy_url' => null,
        'turnstile_site_key' => null,
        'turnstile_secret_key' => null,
    ];

    /**
     * Settings that fall back to a config value (set in .env) when left
     * empty in the admin.
     */
    private const CONFIG_FALLBACKS = [
        'turnstile_site_key' => 'services.turnstile.site_key',
        'turnstile_secret_key' => 'services.turnstile.secret_key',
    ];

    public static function get(string $key, mixed $default = null): mixed
    {
        $all = Cache::rememberForever(self::CACHE_KEY, fn () => static::query()->pluck('value', 'key')->all());

        $value = $all[$key] ?? null;

        if (filled($value)) {
            return $value;
        }

        if (isset(self::CONFIG_FALLBACKS[$key]) && filled($fromConfig = config(self::CONFIG_FALLBACKS[$key]))) {
            return $fromConfig;
        }

        return $default ?? self::DEFAULTS[$key] ?? null;
    }

    /**
     * @param  array<string, mixed>  $values
     */
    public static function putMany(array $values): void
    {
        foreach ($values as $key => $value) {
            static::query()->updateOrCreate(['key' => $key], ['value' => $value]);
        }

        Cache::forget(self::CACHE_KEY);
    }
}
