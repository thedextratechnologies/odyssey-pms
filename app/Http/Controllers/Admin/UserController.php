<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\{AuditLog, Role, Territory, User};
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UserController extends Controller {
    public function __construct(private UserService $userService) {}

    public function index(Request $request) {
        $query = User::with(['role','state','district','city','manager'])->visibleTo(Auth::user());
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q)=>$q->where('name','like',"%$s%")->orWhere('email','like',"%$s%")->orWhere('employee_id','like',"%$s%"));
        }
        if ($request->filled('role_id'))  $query->where('role_id', $request->role_id);
        if ($request->filled('state_id')) $query->where('state_id', $request->state_id);
        if ($request->filled('status'))   $query->where('status', $request->status);
        $users  = $query->orderBy('name')->paginate(20)->withQueryString();
        $roles  = Role::orderBy('level','desc')->get();
        $states = Territory::states();
        return view('admin.users.index', compact('users','roles','states'));
    }

    public function create() {
        $roles    = Role::where('name','!=',Role::SUPER_ADMIN)->orderBy('level','desc')->get();
        $states   = Territory::states();
        $managers = User::with('role')->whereHas('role',fn($q)=>$q->whereIn('name',[Role::BDM,Role::ZONE_MANAGER,Role::SALES_DIRECTOR]))->active()->orderBy('name')->get();
        return view('admin.users.create', compact('roles','states','managers'));
    }

    public function store(Request $request) {
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
        AuditLog::record('create', $user);
        return redirect()->route('admin.users.index')->with('success',"User {$user->name} created. Welcome email sent.");
    }

    public function show(User $user) {
        $user->load(['role','state','district','city','manager','subordinates.role']);
        $recentLogs = $user->auditLogs()->latest()->limit(10)->get();
        return view('admin.users.show', compact('user','recentLogs'));
    }

    public function edit(User $user) {
        $roles     = Role::where('name','!=',Role::SUPER_ADMIN)->orderBy('level','desc')->get();
        $states    = Territory::states();
        $districts = $user->state_id ? Territory::districtsFor($user->state_id) : collect();
        $cities    = $user->district_id ? Territory::citiesFor($user->district_id) : collect();
        $managers  = User::with('role')->whereHas('role',fn($q)=>$q->whereIn('name',[Role::BDM,Role::ZONE_MANAGER,Role::SALES_DIRECTOR]))->where('id','!=',$user->id)->active()->orderBy('name')->get();
        return view('admin.users.edit', compact('user','roles','states','districts','cities','managers'));
    }

    public function update(Request $request, User $user) {
        $validated = $request->validate([
            'name'            => 'required|string|max:100',
            'email'           => ['required','email',Rule::unique('users')->ignore($user->id)],
            'phone'           => 'nullable|string|max:20',
            'employee_id'     => ['nullable','string','max:30',Rule::unique('users')->ignore($user->id)],
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
        return redirect()->route('admin.users.show',$user)->with('success','User updated.');
    }

    public function destroy(User $user) {
        if ($user->id === Auth::id()) return back()->with('error','You cannot delete your own account.');
        AuditLog::record('delete', $user, $user->toArray());
        $user->delete();
        return redirect()->route('admin.users.index')->with('success',"{$user->name} removed.");
    }

    public function resetPassword(User $user) {
        $this->userService->sendPasswordReset($user);
        AuditLog::record('password_reset_forced', $user);
        return back()->with('success','Password reset link sent to '.$user->email);
    }

    public function getDistricts(Request $request) {
        return response()->json(Territory::districtsFor($request->state_id));
    }

    public function getCities(Request $request) {
        return response()->json(Territory::citiesFor($request->district_id));
    }
}
