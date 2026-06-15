<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Notification;
use App\Services\SmsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class SettingsController extends Controller
{
    public function smsSettings()
    {
        return view('admin.settings.sms');
    }

    public function updateSmsSettings(Request $request)
    {
        $validated = $request->validate([
            'meseji_api_key' => 'required|string',
            'meseji_sender_id' => 'required|string|max:11',
        ]);

        foreach ($validated as $key => $value) {
            if (!is_null($value)) {
                setting()->set($key, $value);
            }
        }
        setting()->save();

        return back()->with('success', 'SMS settings updated successfully.');
    }

    public function testSms(Request $request, SmsService $smsService)
    {
        $request->validate([
            'test_phone' => 'required|string|max:20',
        ]);

        $phone = $request->test_phone;

        $sent = $smsService->send($phone, 'Test SMS from Debit Management System - ' . now()->format('Y-m-d H:i:s'));

        if ($sent) {
            return back()->with('success', 'Test SMS sent successfully to ' . $phone);
        }

        return back()->with('error', 'Test SMS failed. Check the log file for details.');
    }

    public function users()
    {
        $users = User::orderByRaw("FIELD(role, 'admin', 'staff')")->orderBy('id')->paginate(15);
        return view('admin.settings.users', compact('users'));
    }

    public function createUser(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|string|min:8',
            'role' => 'required|in:admin,staff',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        User::create($validated);

        return redirect()->route('admin.settings.users')
            ->with('success', 'User created successfully.');
    }

    public function updateUser(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'phone' => 'nullable|string|max:20',
            'role' => 'required|in:admin,staff',
            'password' => 'nullable|string|min:8',
        ]);

        if ($validated['password']) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);
        return redirect()->route('admin.settings.users')
            ->with('success', 'User updated successfully.');
    }

    public function deleteUser(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete yourself.');
        }
        if ($user->loans()->exists() || $user->payments()->exists()) {
            return back()->with('error', 'Cannot delete user with associated records.');
        }
        $user->delete();
        return redirect()->route('admin.settings.users')
            ->with('success', 'User deleted successfully.');
    }

    public function notifications()
    {
        $notifications = auth()->user()->notifications()->latest()->paginate(20);
        return view('admin.notifications.index', compact('notifications'));
    }

    public function markRead($id)
    {
        $notification = Notification::findOrFail($id);
        $notification->update(['read_status' => true]);
        return back();
    }

    public function markAllRead()
    {
        auth()->user()->unreadNotifications()->update(['read_status' => true]);
        return back()->with('success', 'All notifications marked as read.');
    }
}
