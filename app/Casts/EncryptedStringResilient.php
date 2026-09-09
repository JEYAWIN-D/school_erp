<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

class EncryptedStringResilient implements CastsAttributes
{
    /**
     * Cast the given value from database storage.
     * Decrypts encrypted strings, or returns raw string if legacy plaintext (zero breakage).
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        if ($value === null || $value === '') {
            return $value;
        }

        try {
            return Crypt::decryptString($value);
        } catch (DecryptException $e) {
            // Legacy plaintext fallback: return plaintext so existing rows never crash
            return $value;
        } catch (\Throwable $e) {
            return $value;
        }
    }

    /**
     * Prepare the given value for storage.
     * Always stores values securely encrypted at rest.
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        if ($value === null || $value === '') {
            return $value;
        }

        // If already encrypted (e.g. re-setting decrypted value), verify before double-encrypting
        try {
            Crypt::decryptString($value);
            return $value; // Already encrypted payload
        } catch (\Throwable $e) {
            return Crypt::encryptString((string) $value);
        }
    }
}
