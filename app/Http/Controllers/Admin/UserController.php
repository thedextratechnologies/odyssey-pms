<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Role;
use App\Models\Territory;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function __construct(private UserService $userService)
    {
        $this->middleware('auth');
        $this->middleware('can.manage.users'); // Custom middleware
    }

    public function index(Request $request)
    {
        $query = User::with(['role', 'state', 'district', 'city', 'manager'])
            ->visibleTo(Auth::user());

        // Filters
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('email', 'like', "%{$s}%")
                  ->orWhere('employee_id', 'like', "%{$s}%")
                  ->orWhere('phone', 'like', "%{$s}%");
            });
        }

        if ($request->filled('role_id')) {
            $query->where('role_id', $request->role_id);
        }

        if ($request->filled('state_id')) {
            $query->where('state_id', $request->state_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $users  = $query->orderBy('name')->paginate(20)->withQueryString();
        $roles  = Role::orderBy('level', 'desc')->get();
        $states = Territory::states();

        return view('admin.users.index', compact('users', 'roles', 'states'));
    }

    public function create()
    {
        $roles   = Role::where('name', '!=', Role::SUPER_ADMIN)->orderBy('level', 'desc')->get();
        $states  = Territory::states();
        $managers = User::with('role')
            ->whereHas('role', fn($q) => $q->whereIn('name', [Role::BDM, Role::ZONE_MANAGER, Role::SALES_DIRECTOR]))
            ->active()
            ->orderBy('name')
            ->get();

        return view('admin.users.create', compact('roles', 'states', 'managers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'            => 'required|string|max:100',
            'email'           => 'required|email|unique:users,email',
            'phone'           => 'nullable|string|max:20',
            'employee_id'     => 'nullable|string|max:30|unique:users,employee_id',
            'role_id'         => 'required|exists:roles,id',
            'state_id'        => 'nullable|exists:territories,id',
            'district_id'     => 'nullable|exists:territories,id',
            'city_id'         => 'nullable|exists:territories,id',
            'manager_id'      => 'nullable|exists:users,id',
            'date_of_joining' => 'nullable|date',
            'status'          => 'required|in:active,inactive,suspended',
        ]);

        $user = $this->userService->createUser($validated);

        AuditLog::record('create', $user, [], $user->toArray());

        return redirect()->route('admin.users.index')
            ->with('success', "User {$user->name} created successfully. A welcome email has been sent.");
    }

    public function show(User $user)
    {
        $this->authorizeVisibility($user);
        $user->load(['role', 'state', 'district', 'city', 'manager', 'subordinates.role']);
        $recentLogs = $user->auditLogs()->latest()->limit(10)->get();

        return view('admin.users.show', compact('user', 'recentLogs'));
    }

    public function edit(User $user)
    {
        $this->authorizeVisibility($user);

        $roles    = Role::where('name', '!=', Role::SUPER_ADMIN)->orderBy('level', 'desc')->get();
        $states   = Territory::states();
        $districts = $user->state_id ? Territory::districtsFor($user->state_id) : collect();
        $cities   = $user->district_id ? Territory::citiesFor($user->district_id) : collect();
        $managers = User::with('role')
            ->whereHas('role', fn($q) => $q->whereIn('name', [Role::BDM, Role::ZONE_MANAGER, Role::SALES_DIRECTOR]))
            ->where('id', '!=', $user->id)
            ->active()
            ->orderBy('name')
            ->get();

        return view('admin.users.edit', compact('user', 'roles', 'states', 'districts', 'cities', 'managers'));
    }

    public function update(Request $request, User $user)
    {
        $this->authorizeVisibility($user);

        $validated = $request->validate([
            'name'            => 'required|string|max:100',
            'email'           => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'phone'           => 'nullable|string|max:20',
            'employee_id'     => ['nullable', 'string', 'max:30', Rule::unique('users')->ignore($user->id)],
            'role_id'         => 'required|exists:roles,id',
            'state_id'        => 'nullable|exists:territories,id',
            'district_id'     => 'nullable|exists:territories,id',
            'city_id'         => 'nullable|exists:territories,id',
            'manager_id'      => 'nullable|exists:users,id',
            'date_of_joining' => 'nullable|date',
            'status'          => 'required|in:active,inactive,suspended',
        ]);

        $old = $user->toArray();
        $user->update($validated);

        AuditLog::record('update', $user, $old, $user->fresh()->toArray());

        return redirect()->route('admin.users.show', $user)
            ->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        if ($user->id === Auth::id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        $name = $user->name;
        AuditLog::record('delete', $user, $user->toArray());
        $user->delete(); // soft delete

        return redirect()->route('admin.users.index')
            ->with('success', "{$name} has been removed from the system.");
    }

    public function resetPassword(User $user)
    {
        $this->authorizeVisibility($user);
        $this->userService->sendPasswordReset($user);

        AuditLog::record('password_reset_forced', $user);

        return back()->with('success', 'A password reset link has been sent to ' . $user->email);
    }

    public function getDistricts(Request $request)
    {
        $districts = Territory::districtsFor($request->state_id);
        return response()->json($districts);
    }

    public function getCities(Request $request)
    {
        $cities = Territory::citiesFor($request->district_id);
        return response()->json($cities);
    }

    // ── Helpers ───────────────────────────────────────────────────
    private function authorizeVisibility(User $user): void
    {
        $viewer = Auth::user();
        if (!$viewer->isSuperAdmin()) {
            // Non-super-admins cannot edit users at same or higher role level
            if ($user->role?->level >= $viewer->role?->level) {
                abort(403, 'You do not have permission to manage this user.');
            }
        }
    }
}
