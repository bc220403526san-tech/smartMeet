<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Mail\RoleRequestSubmitted;
use App\Models\Meeting;
use App\Models\MeetingParticipant;
use App\Models\Notification;
use App\Models\RoleRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class SettingsController extends Controller
{
    // ── INDEX ──
    public function index()
    {
        $user = Auth::user();
        $totalMeetings = $this->getTotalMeetings($user);

        $pendingRequest = RoleRequest::where('user_id', $user->id)
            ->where('status', 'pending')
            ->latest()
            ->first();

        $lastRequest = null;
        if (!$pendingRequest) {
            $lastRequest = RoleRequest::where('user_id', $user->id)
                ->whereIn('status', ['approved', 'rejected'])
                ->latest()
                ->first();
        }

        return view('organizer.settings.index', [
            'user'           => $user,
            'totalMeetings'  => $totalMeetings,
            'pendingRequest' => $pendingRequest,
            'lastRequest'    => $lastRequest,
        ]);
    }

    /**
     * Role ke hisaab se sahi count nikalta hai:
     * - organizer: khud ne jitni meetings banai hain
     * - participant (ya koi aur role): jin meetings mein wo invited/participant hai
     */
    private function getTotalMeetings($user): int
    {
        if ($user->role === 'organizer') {
            return Meeting::where('organizer_id', $user->id)->count();
        }

        return MeetingParticipant::where('user_id', $user->id)
            ->distinct('meeting_id')
            ->count('meeting_id');
    }

    // ── PROFILE ──
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name'  => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:30'],
        ]);

        $user->forceFill($validated)->save();

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Profile updated successfully.']);
        }

        return back()->with('success', 'Profile updated successfully.');
    }

    // ── AVATAR ──
    public function updateAvatar(Request $request)
    {
        $request->validate([
            'avatar' => ['required', 'file', 'max:8192'], // 8MB
        ]);

        $file = $request->file('avatar');
        $mime = $file->getMimeType();
        $ext  = strtolower($file->getClientOriginalExtension());
        $allowedExt = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp', 'svg', 'heic', 'heif', 'avif'];

        if (!str_starts_with((string) $mime, 'image/') && !in_array($ext, $allowedExt, true)) {
            return $request->wantsJson()
                ? response()->json(['message' => 'That file does not look like an image.'], 422)
                : back()->withErrors(['avatar' => 'That file does not look like an image.']);
        }

        $user = Auth::user();

        // Purani avatar delete karo (agar hai)
        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
        }

        $path = $file->store('avatars', 'public');
        $user->forceFill(['avatar' => $path])->save();

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Profile photo updated.',
                'url'     => Storage::url($path),
            ]);
        }

        return back()->with('success', 'Profile photo updated.');
    }

    // ── PASSWORD ──
    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password'          => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()->symbols()],
        ]);

        $user->forceFill([
            'password' => Hash::make($request->password),
        ])->save();

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Password updated successfully.']);
        }

        return back()->with('success', 'Password updated successfully.');
    }

    // ── NOTIFICATIONS ──
    public function updateNotifications(Request $request)
    {
        $user = Auth::user();
        $data = [];

        foreach (['email_alerts', 'reminders_enabled', 'system_alerts'] as $key) {
            if ($request->has($key)) {
                $data[$key] = $request->boolean($key);
            }
        }

        if (empty($data)) {
            return $request->wantsJson()
                ? response()->json(['message' => 'No preference provided.'], 422)
                : back()->withErrors(['notifications' => 'No preference provided.']);
        }

        $user->forceFill($data)->save();

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Notification preferences saved.', 'data' => $data]);
        }

        return back()->with('success', 'Notification preferences saved.');
    }

    // ── DEACTIVATE ──
    public function deactivate(Request $request)
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = Auth::user();
        $user->forceFill(['is_active' => false])->save();

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login')->with('status', 'Your account has been deactivated.');
    }

    // ── FLASH (AJAX ke liye session message store karta hai) ──
    public function storeFlash(Request $request)
    {
        $request->validate([
            'message' => ['required', 'string'],
            'type'    => ['required', 'in:success,error'],
        ]);

        session()->flash($request->type, $request->message);

        return response()->json(['message' => 'Flash stored.']);
    }

    // ── ROLE CHANGE REQUEST (Organizer -> Participant) ──
    public function roleRequest(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'subject' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:1000'],
        ]);

        $alreadyPending = RoleRequest::where('user_id', $user->id)
            ->where('status', 'pending')
            ->exists();

        if ($alreadyPending) {
            return $request->wantsJson()
                ? response()->json(['message' => 'You already have a pending role change request.'], 422)
                : back()->with('error', 'You already have a pending role change request.');
        }

        $roleRequest = RoleRequest::create([
            'user_id'        => $user->id,
            'subject'        => $request->subject,
            'message'        => $request->message,
            'requested_role' => 'participant', // organizer sirf participant ban sakta hai
            'status'         => 'pending',
        ]);

        $admins = User::where('role', 'admin')->get();

        foreach ($admins as $admin) {
            Mail::to($admin->email)->send(new RoleRequestSubmitted($roleRequest));

            Notification::create([
                'user_id' => $admin->id,
                'title'   => 'New Role Change Request',
                'message' => $user->name . ' has requested to become a Participant',
                'link'    => route('admin.role-requests.index'),
            ]);
        }

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Your role change request has been submitted.']);
        }

        return back()->with('success', 'Your role change request has been submitted.');
    }
}
