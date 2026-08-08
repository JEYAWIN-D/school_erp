<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NotificationTemplate;
use App\Models\SchoolSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class SettingsController extends Controller
{
    public function index()
    {
        $school = SchoolSetting::first();
        return view('settings.index', compact('school'));
    }

    public function save(Request $request)
    {
        $request->validate([
            'school_name'    => 'required|string|max:200',
            'email'          => 'nullable|email|max:200',
            'phone'          => 'nullable|string|max:20',
            'pincode'        => 'nullable|string|max:10',
            'primary_color'  => 'nullable|string|max:7',
            'gstin'          => 'nullable|string|max:20',
            'pan'            => 'nullable|string|max:20',
        ]);

        $school = SchoolSetting::first();
        $fields = $request->only([
            'school_name', 'school_code', 'affiliation_no', 'board',
            'address', 'city', 'state', 'pincode', 'phone', 'email',
            'website', 'principal_name', 'medium', 'primary_color',
            'currency_symbol', 'date_format', 'timezone', 'gstin', 'pan',
        ]);

        if ($school) {
            $school->update($fields);
        } else {
            SchoolSetting::create($fields);
        }

        return back()->with('success', 'School settings saved.');
    }

    public function uploadSignature(Request $request)
    {
        $request->validate(['signature' => 'required|image|mimes:png,jpg,jpeg|max:1024']);
        $school = SchoolSetting::first();
        if ($school?->principal_signature) {
            Storage::disk('public')->delete($school->principal_signature);
        }
        $path = $request->file('signature')->store('signatures', 'public');
        SchoolSetting::query()->update(['principal_signature' => $path]);
        return back()->with('success', 'Principal signature uploaded.');
    }

    public function uploadStamp(Request $request)
    {
        $request->validate(['stamp' => 'required|image|mimes:png,jpg,jpeg|max:1024']);
        $school = SchoolSetting::first();
        if ($school?->school_stamp) {
            Storage::disk('public')->delete($school->school_stamp);
        }
        $path = $request->file('stamp')->store('stamps', 'public');
        SchoolSetting::query()->update(['school_stamp' => $path]);
        return back()->with('success', 'School stamp uploaded.');
    }

    public function deleteSignature()
    {
        $school = SchoolSetting::first();
        if ($school?->principal_signature) {
            Storage::disk('public')->delete($school->principal_signature);
            $school->update(['principal_signature' => null]);
        }
        return back()->with('success', 'Signature removed.');
    }

    public function deleteStamp()
    {
        $school = SchoolSetting::first();
        if ($school?->school_stamp) {
            Storage::disk('public')->delete($school->school_stamp);
            $school->update(['school_stamp' => null]);
        }
        return back()->with('success', 'Stamp removed.');
    }

    // ── Notification Templates ────────────────────────────

    public function notificationTemplates()
    {
        $templates   = NotificationTemplate::orderBy('event_type')->get()->keyBy(fn($t) => $t->event_type . '_' . $t->channel);
        $eventTypes  = NotificationTemplate::$eventTypes;
        $defaultBodies = NotificationTemplate::$defaultBodies;
        return view('settings.notification-templates', compact('templates', 'eventTypes', 'defaultBodies'));
    }

    public function saveNotificationTemplate(Request $request)
    {
        $request->validate([
            'event_type'   => 'required|string',
            'channel'      => 'required|in:email,sms,whatsapp',
            'subject'      => 'nullable|string|max:255',
            'body'         => 'required|string',
            'trigger_time' => 'nullable|date_format:H:i',
            'is_active'    => 'nullable',
        ]);

        NotificationTemplate::updateOrCreate(
            ['event_type' => $request->event_type, 'channel' => $request->channel],
            [
                'subject'      => $request->subject,
                'body'         => $request->body,
                'trigger_time' => $request->trigger_time,
                'is_active'    => $request->boolean('is_active'),
                'updated_by'   => Auth::id(),
            ]
        );

        return back()->with('success', 'Notification template saved.');
    }
}
