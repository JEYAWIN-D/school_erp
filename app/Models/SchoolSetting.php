<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SchoolSetting extends Model
{
    protected $fillable = [
        'school_name', 'school_code', 'affiliation_no', 'board',
        'address', 'city', 'state', 'pincode', 'phone', 'email',
        'website', 'logo', 'principal_name', 'principal_signature',
        'school_stamp', 'gstin', 'pan', 'primary_color',
        'currency_symbol', 'date_format', 'timezone',
        'academic_year_format', 'medium',
        'sms_enabled', 'whatsapp_enabled', 'online_payment_enabled',
        'school_dispersal_time',
        // ID card template
        'id_card_header_color', 'id_card_text_color', 'id_card_bg_color',
        'id_card_header_text', 'id_card_footer_text',
        'id_card_show_blood_group', 'id_card_show_dob',
        'id_card_show_qr', 'id_card_show_mobile', 'id_card_show_address',
        'id_card_show_photo',
    ];

    protected $casts = [
        'sms_enabled'            => 'boolean',
        'whatsapp_enabled'       => 'boolean',
        'online_payment_enabled' => 'boolean',
        'id_card_show_blood_group' => 'boolean',
        'id_card_show_dob'       => 'boolean',
        'id_card_show_qr'        => 'boolean',
        'id_card_show_mobile'    => 'boolean',
        'id_card_show_address'   => 'boolean',
        'id_card_show_photo'     => 'boolean',
    ];

    public static function get(string $key, mixed $default = null): mixed
    {
        try {
            $setting = static::first();
            if (!$setting) return $default;
            $val = $setting->getAttribute($key);
            return $val !== null ? $val : $default;
        } catch (\Exception $e) {
            return $default;
        }
    }

    public static function set(string $key, mixed $value): void
    {
        try {
            $setting = static::first();
            if ($setting && in_array($key, $setting->getFillable())) {
                $setting->update([$key => $value]);
            }
        } catch (\Exception $e) {}
    }

    public static function getAll(): ?self
    {
        try {
            return static::first();
        } catch (\Exception $e) {
            return null;
        }
    }
}
