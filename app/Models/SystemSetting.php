<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SystemSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'description',
    ];

    /**
     * Helper to quickly get a setting value with fallback.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        $setting = static::where('key', $key)->first();
        if (! $setting) {
            return $default;
        }

        $val = $setting->value;
        if ($val === 'true') {
            return true;
        }
        if ($val === 'false') {
            return false;
        }
        if (is_numeric($val)) {
            return str_contains($val, '.') ? (float) $val : (int) $val;
        }

        return $val;
    }

    /**
     * Helper to set a setting value.
     */
    public static function set(string $key, mixed $value, ?string $description = null): static
    {
        $stringVal = is_bool($value) ? ($value ? 'true' : 'false') : (string) $value;

        return static::updateOrCreate(
            ['key' => $key],
            ['value' => $stringVal, 'description' => $description]
        );
    }
}
