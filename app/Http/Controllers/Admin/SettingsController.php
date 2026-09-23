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
        $school            = SchoolSetting::first();
        $colorPresets      = SchoolSetting::colorPresets();
        $fontSizeOptions   = SchoolSetting::fontSizeOptions();
        $fontFamilyOptions = SchoolSetting::fontFamilyOptions();
        $densityOptions    = SchoolSetting::densityOptions();
        $sidebarOptions    = SchoolSetting::sidebarOptions();

        $schoolLogosList   = $school ? $school->getLogosList() : (new SchoolSetting)->getLogosList();
        $schoolSealsList   = $school ? $school->getSealsList() : (new SchoolSetting)->getSealsList();

        return view('settings.index', compact(
            'school',
            'colorPresets',
            'fontSizeOptions',
            'fontFamilyOptions',
            'densityOptions',
            'sidebarOptions',
            'schoolLogosList',
            'schoolSealsList'
        ));
    }

    public function save(Request $request)
    {
        $request->validate([
            'school_name'        => 'required|string|max:200',
            'email'              => 'nullable|email|max:200',
            'phone'              => 'nullable|string|max:20',
            'pincode'            => 'nullable|string|max:10',
            'primary_color'      => ['nullable', 'string', 'regex:/^#[a-fA-F0-9]{6}$/'],
            'font_size'          => 'nullable|string|in:small,default,large,xlarge',
            'font_family'        => 'nullable|string|in:default,poppins,plus-jakarta,system',
            'theme_mode'          => 'nullable|string|in:light,dark,system',
            'ui_density'         => 'nullable|string|in:comfortable,compact',
            'sidebar_preference' => 'nullable|string|in:expanded,collapsed',
            'gstin'              => 'nullable|string|max:20',
            'pan'                => 'nullable|string|max:20',
        ]);

        $school = SchoolSetting::first();
        $fields = $request->only([
            'school_name', 'school_code', 'affiliation_no', 'board',
            'address', 'city', 'state', 'pincode', 'phone', 'email',
            'website', 'principal_name', 'medium', 'primary_color',
            'font_size', 'font_family', 'theme_mode', 'ui_density', 'sidebar_preference',
            'currency_symbol', 'date_format', 'timezone', 'gstin', 'pan',
        ]);

        if ($school) {
            $school->update($fields);
        } else {
            SchoolSetting::create($fields);
        }

        return back()->with('success', 'School settings saved.');
    }

    public function resetTheme()
    {
        $school = SchoolSetting::first();
        if ($school) {
            $school->update([
                'primary_color'      => '#4F46E5',
                'font_size'          => 'default',
                'font_family'        => 'default',
                'theme_mode'          => 'light',
                'ui_density'         => 'comfortable',
                'sidebar_preference' => 'expanded',
            ]);
        }
        return back()->with('success', 'Theme and appearance settings reset to defaults.');
    }

    public function uploadLogo(Request $request)
    {
        $request->validate([
            'slot' => 'required|in:1,2',
            'logo' => ['required', 'file', 'mimes:png', 'mimetypes:image/png', 'max:2048'],
        ], [
            'logo.required'  => 'Please select a PNG logo file to upload.',
            'logo.mimes'     => 'Only PNG format is allowed for school logos (must be a .png file).',
            'logo.mimetypes' => 'Only PNG format is allowed for school logos.',
            'logo.max'       => 'School logo file size must not exceed 2MB.',
        ]);

        $slot = (int) $request->input('slot', 1);
        $column = $slot === 2 ? 'logo_2' : 'logo';

        $school = SchoolSetting::first();
        if (!$school) {
            $school = SchoolSetting::create(['school_name' => 'DASA EduERP']);
        }

        // Remove previous file from public storage if it was stored in 'logos/'
        $oldFile = $school->getAttribute($column);
        if ($oldFile && Storage::disk('public')->exists($oldFile)) {
            Storage::disk('public')->delete($oldFile);
        }

        $path = $request->file('logo')->store('logos', 'public');
        $school->update([$column => $path]);

        $slotName = $slot === 1 ? 'Primary School Logo' : 'Secondary Logo / Crest';
        return back()->with('success', "{$slotName} uploaded successfully.")->with('active_tab', 'branding');
    }

    public function deleteLogo(int $slot)
    {
        if (!in_array($slot, [1, 2])) {
            return back()->withErrors(['error' => 'Invalid logo slot specified.'])->with('active_tab', 'branding');
        }

        $column = $slot === 2 ? 'logo_2' : 'logo';
        $school = SchoolSetting::first();

        if ($school) {
            $oldFile = $school->getAttribute($column);
            if ($oldFile && Storage::disk('public')->exists($oldFile)) {
                Storage::disk('public')->delete($oldFile);
            }
            $school->update([$column => null]);
        }

        $slotName = $slot === 1 ? 'Primary School Logo' : 'Secondary Logo / Crest';
        return back()->with('success', "{$slotName} removed.")->with('active_tab', 'branding');
    }

    public function uploadSeal(Request $request)
    {
        $request->validate([
            'slot'  => 'required|integer|between:1,6',
            'seal'  => ['required', 'file', 'mimes:png', 'mimetypes:image/png', 'max:2048'],
            'label' => 'nullable|string|max:100',
        ], [
            'seal.required'  => 'Please select a PNG seal file to upload.',
            'seal.mimes'     => 'Only PNG format is allowed for institutional seals (must be a .png file).',
            'seal.mimetypes' => 'Only PNG format is allowed for institutional seals.',
            'seal.max'       => 'Institutional seal file size must not exceed 2MB.',
        ]);

        $slot = (int) $request->input('slot');
        $column = "seal_{$slot}";

        $school = SchoolSetting::first();
        if (!$school) {
            $school = SchoolSetting::create(['school_name' => 'DASA EduERP']);
        }

        // Remove old file from storage if present
        $oldFile = $school->getAttribute($column);
        if ($oldFile && Storage::disk('public')->exists($oldFile)) {
            Storage::disk('public')->delete($oldFile);
        }

        $path = $request->file('seal')->store('seals', 'public');
        $updateData = [$column => $path];

        // Keep legacy school_stamp in sync with seal_1
        if ($slot === 1) {
            $updateData['school_stamp'] = $path;
        }

        // Custom label update if provided
        if ($request->filled('label')) {
            $meta = $school->seals_meta ?? [];
            $meta[$slot] = trim($request->input('label'));
            $updateData['seals_meta'] = $meta;
        }

        $school->update($updateData);

        $label = $school->getSealLabel($slot);
        return back()->with('success', "Seal Slot {$slot} ({$label}) uploaded successfully.")->with('active_tab', 'branding');
    }

    public function deleteSeal(int $slot)
    {
        if ($slot < 1 || $slot > 6) {
            return back()->withErrors(['error' => 'Invalid seal slot specified.'])->with('active_tab', 'branding');
        }

        $column = "seal_{$slot}";
        $school = SchoolSetting::first();

        if ($school) {
            $oldFile = $school->getAttribute($column);
            if ($oldFile && Storage::disk('public')->exists($oldFile)) {
                Storage::disk('public')->delete($oldFile);
            }
            $updateData = [$column => null];
            if ($slot === 1) {
                if ($school->school_stamp && Storage::disk('public')->exists($school->school_stamp)) {
                    Storage::disk('public')->delete($school->school_stamp);
                }
                $updateData['school_stamp'] = null;
            }
            $school->update($updateData);
        }

        return back()->with('success', "Seal Slot {$slot} removed.")->with('active_tab', 'branding');
    }

    public function saveSealLabel(Request $request)
    {
        $request->validate([
            'slot'  => 'required|integer|between:1,6',
            'label' => 'nullable|string|max:100',
        ]);

        $slot = (int) $request->input('slot');
        $school = SchoolSetting::first();
        if ($school) {
            $meta = $school->seals_meta ?? [];
            $label = trim($request->input('label', ''));
            if ($label !== '') {
                $meta[$slot] = $label;
            } else {
                unset($meta[$slot]);
            }
            $school->update(['seals_meta' => $meta]);
        }

        return back()->with('success', "Seal Slot {$slot} label updated.")->with('active_tab', 'branding');
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
        return back()->with('success', 'Principal signature uploaded.')->with('active_tab', 'branding');
    }

    public function uploadStamp(Request $request)
    {
        $request->validate(['stamp' => 'required|image|mimes:png,jpg,jpeg|max:1024']);
        $school = SchoolSetting::first();
        if ($school?->school_stamp) {
            Storage::disk('public')->delete($school->school_stamp);
        }
        $path = $request->file('stamp')->store('stamps', 'public');
        SchoolSetting::query()->update(['school_stamp' => $path, 'seal_1' => $path]);
        return back()->with('success', 'School stamp uploaded.')->with('active_tab', 'branding');
    }

    public function deleteSignature()
    {
        $school = SchoolSetting::first();
        if ($school?->principal_signature) {
            Storage::disk('public')->delete($school->principal_signature);
            $school->update(['principal_signature' => null]);
        }
        return back()->with('success', 'Signature removed.')->with('active_tab', 'branding');
    }

    public function deleteStamp()
    {
        $school = SchoolSetting::first();
        if ($school?->school_stamp) {
            Storage::disk('public')->delete($school->school_stamp);
            $school->update(['school_stamp' => null, 'seal_1' => null]);
        }
        return back()->with('success', 'Stamp removed.')->with('active_tab', 'branding');
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
