<?php
// app/Http/Controllers/Admin/SettingController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    protected $activityLogService;

    public function __construct(ActivityLogService $activityLogService)
    {
        $this->activityLogService = $activityLogService;
        $this->middleware('auth');
        $this->middleware('admin');
    }

    public function index()
    {
        $settings = [
            'general' => [
                'operating_hours_start' => Setting::get('operating_hours_start', '08:00'),
                'operating_hours_end' => Setting::get('operating_hours_end', '18:00'),
                'minimum_booking_duration' => Setting::get('minimum_booking_duration', 30),
                'maximum_booking_duration' => Setting::get('maximum_booking_duration', 180),
                'require_approval' => Setting::get('require_approval', true),
                'auto_approve_admin_bookings' => Setting::get('auto_approve_admin_bookings', true),
                'require_reapproval_on_edit' => Setting::get('require_reapproval_on_edit', true),
            ],
            'notifications' => [
                'notification_website_enabled' => Setting::get('notification_website_enabled', true),
                'notification_whatsapp_enabled' => Setting::get('notification_whatsapp_enabled', false),
                'notification_webhook_enabled' => Setting::get('notification_webhook_enabled', false),
                'whatsapp_api_url' => Setting::get('whatsapp_api_url', ''),
                'webhook_url' => Setting::get('webhook_url', ''),
            ]
        ];

        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'operating_hours_start' => 'required|date_format:H:i',
            'operating_hours_end' => 'required|date_format:H:i|after:operating_hours_start',
            'minimum_booking_duration' => 'required|integer|min:15|max:480',
            'maximum_booking_duration' => 'required|integer|min:30|max:1440',
            'require_approval' => 'boolean',
            'auto_approve_admin_bookings' => 'boolean',
            'require_reapproval_on_edit' => 'boolean',
            'notification_website_enabled' => 'boolean',
            'notification_whatsapp_enabled' => 'boolean',
            'notification_webhook_enabled' => 'boolean',
            'whatsapp_api_url' => 'nullable|url',
            'webhook_url' => 'nullable|url',
        ]);

        try {
            $oldSettings = [];
            $newSettings = [];

            foreach ($request->all() as $key => $value) {
                if (in_array($key, ['_token', '_method'])) {
                    continue;
                }

                $oldValue = Setting::get($key);
                $oldSettings[$key] = $oldValue;
                $newSettings[$key] = $value;

                $type = 'string';
                if (is_bool($value) || in_array($key, ['require_approval', 'auto_approve_admin_bookings', 'require_reapproval_on_edit', 'notification_website_enabled', 'notification_whatsapp_enabled', 'notification_webhook_enabled'])) {
                    $type = 'boolean';
                    $value = $value ? '1' : '0';
                } elseif (is_numeric($value)) {
                    $type = 'integer';
                }

                Setting::set($key, $value, $type);
            }

            // Log activity
            $this->activityLogService->log('settings_updated', new Setting(), $oldSettings, $newSettings);

            return redirect()->back()
                           ->with('success', 'Pengaturan berhasil disimpan.');
        } catch (\Exception $e) {
            return redirect()->back()
                           ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function testNotification(Request $request)
    {
        $request->validate([
            'type' => 'required|in:whatsapp,webhook',
        ]);

        try {
            if ($request->type === 'whatsapp') {
                // Placeholder untuk test WhatsApp
                // Implementasi akan ditambahkan nanti
                return response()->json([
                    'success' => false,
                    'message' => 'Fitur WhatsApp belum diimplementasikan'
                ]);
            } elseif ($request->type === 'webhook') {
                // Placeholder untuk test Webhook
                // Implementasi akan ditambahkan nanti
                return response()->json([
                    'success' => false,
                    'message' => 'Fitur Webhook belum diimplementasikan'
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }
}