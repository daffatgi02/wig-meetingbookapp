<?php
// database/seeders/DefaultSettingsSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;

class DefaultSettingsSeeder extends Seeder
{
    public function run()
    {
        $settings = [
            [
                'key' => 'operating_hours_start',
                'value' => '08:00',
                'type' => 'string',
                'description' => 'Jam operasional mulai'
            ],
            [
                'key' => 'operating_hours_end',
                'value' => '18:00',
                'type' => 'string',
                'description' => 'Jam operasional selesai'
            ],
            [
                'key' => 'minimum_booking_duration',
                'value' => '30',
                'type' => 'integer',
                'description' => 'Durasi minimum pemesanan (dalam menit)'
            ],
            [
                'key' => 'maximum_booking_duration',
                'value' => '180',
                'type' => 'integer',
                'description' => 'Durasi maksimum pemesanan (dalam menit)'
            ],
            [
                'key' => 'require_approval',
                'value' => '1',
                'type' => 'boolean',
                'description' => 'Apakah pemesanan memerlukan persetujuan admin'
            ],
            [
                'key' => 'auto_approve_admin_bookings',
                'value' => '1',
                'type' => 'boolean',
                'description' => 'Otomatis setujui pemesanan yang dibuat admin'
            ],
            [
                'key' => 'require_reapproval_on_edit',
                'value' => '1',
                'type' => 'boolean',
                'description' => 'Memerlukan persetujuan ulang saat edit pemesanan'
            ],
            [
                'key' => 'notification_website_enabled',
                'value' => '1',
                'type' => 'boolean',
                'description' => 'Aktifkan notifikasi website'
            ],
            [
                'key' => 'notification_whatsapp_enabled',
                'value' => '0',
                'type' => 'boolean',
                'description' => 'Aktifkan notifikasi WhatsApp'
            ],
            [
                'key' => 'notification_webhook_enabled',
                'value' => '0',
                'type' => 'boolean',
                'description' => 'Aktifkan notifikasi webhook'
            ],
            [
                'key' => 'whatsapp_api_url',
                'value' => '',
                'type' => 'string',
                'description' => 'URL API WhatsApp'
            ],
            [
                'key' => 'webhook_url',
                'value' => '',
                'type' => 'string',
                'description' => 'URL Webhook (Discord, dll)'
            ],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}