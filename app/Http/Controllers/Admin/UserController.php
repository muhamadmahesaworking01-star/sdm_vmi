<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Employee;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->query('q'));
        $sort = in_array($request->query('sort'), ['login_id', 'name', 'email', 'role', 'status_akun'], true) ? $request->query('sort') : 'name';
        $direction = $request->query('direction') === 'desc' ? 'desc' : 'asc';
        $users = User::query()
            ->when(request('role'), fn ($query, $role) => $query->where('role', $role))
            ->when($search, fn ($query) => $query->where(fn ($q) => $q->where('login_id', 'like', "%{$search}%")->orWhere('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%")))
            ->orderBy($sort, $direction)
            ->paginate(10)
            ->withQueryString();

        return view('admin.users.index', compact('users', 'search', 'sort', 'direction'));
    }

    public function create()
    {
        $employees = Employee::query()
            ->whereDoesntHave('user')
            ->orderBy('nama')
            ->get(['id', 'nama', 'nip', 'email', 'peran']);
        $selectedEmployeeId = request()->query('employee_id');

        return view('admin.users.create', compact('employees', 'selectedEmployeeId'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => ['required', 'integer', 'exists:employees,id'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', Rule::in(User::ROLES)],
        ]);

        $employee = Employee::findOrFail($validated['employee_id']);
        if ($employee->user()->exists()) {
            return back()->withInput()->withErrors(['employee_id' => 'Karyawan tersebut sudah memiliki akun login.']);
        }

        $user = DB::transaction(function () use ($validated, $employee) {
            return User::create([
            'employee_id' => $employee->id,
            'login_id' => $validated['role'] === User::ROLE_SUPER_ADMIN ? User::rekomendasiLoginId($validated['role']) : $employee->nip,
            'name' => $employee->nama,
            'email' => $employee->email,
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            ]);
        });

        ActivityLog::create(['user_id' => $request->user()->id, 'action' => 'create', 'route' => $request->route()->getName(), 'method' => 'POST', 'ip_address' => $request->ip(), 'description' => 'Membuat akun '.$user->name.' dengan role '.$user->roleLabel()]);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Akun login baru berhasil dibuat.');
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user)],
            'role' => ['required', Rule::in(User::ROLES)],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        $user->fill([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
        ]);

        if (! empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        if ($user->employee && in_array($user->role, [User::ROLE_KARYAWAN, User::ROLE_PENGAJAR, User::ROLE_KARYAWAN_PENGAJAR, User::ROLE_DIREKSI], true)) {
            $user->employee->update(['nama' => $user->name, 'email' => $user->email, 'peran' => match ($user->role) { User::ROLE_PENGAJAR => 'pengajar', User::ROLE_DIREKSI => 'direksi', default => 'karyawan' }]);
        }
        ActivityLog::create(['user_id' => $request->user()->id, 'action' => 'update', 'route' => $request->route()->getName(), 'method' => 'PUT', 'ip_address' => $request->ip(), 'description' => 'Memperbarui akun '.$user->name]);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Data akun berhasil diperbarui.');
    }

    public function updateRole(Request $request, User $user)
    {
        $validated = $request->validate([
            'role' => ['required', Rule::in(User::ROLES)],
        ]);

        $user->update($validated);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Hak akses akun berhasil diubah.');
    }

    public function resetPassword(Request $request, User $user)
    {
        $validated = $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Password akun berhasil direset.');
    }

    public function impersonate(Request $request, User $user)
    {
        abort_if($user->is($request->user()), 403, 'Admin tidak dapat masuk sebagai akun sendiri.');
        abort_if(($user->status_akun ?? 'aktif') === 'suspend', 422, 'Akun target sedang disuspend.');

        $request->session()->put('impersonator_id', $request->user()->id);
        ActivityLog::create([
            'user_id' => $request->user()->id,
            'action' => 'impersonate',
            'route' => $request->route()->getName(),
            'method' => 'POST',
            'ip_address' => $request->ip(),
            'description' => 'Masuk sebagai user '.$user->name.' ('.$user->roleLabel().')',
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route($user->homeRoute());
    }

    public function stopImpersonating(Request $request)
    {
        $admin = User::find($request->session()->pull('impersonator_id'));
        if (! $admin || ! $admin->hasRole(User::ROLE_SUPER_ADMIN)) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login');
        }

        ActivityLog::create([
            'user_id' => $admin->id,
            'action' => 'stop_impersonate',
            'route' => $request->route()->getName(),
            'method' => 'POST',
            'ip_address' => $request->ip(),
            'description' => 'Mengakhiri akses sebagai user '.$request->user()->name,
        ]);

        Auth::login($admin);
        $request->session()->regenerate();

        return redirect()->route('admin.users.index');
    }

    public function toggleSuspend(User $user)
    {
        $user->update([
            'status_akun' => $user->status_akun === 'suspend' ? 'aktif' : 'suspend',
        ]);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Status akun berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        abort_if($user->is(request()->user()), 403, 'Akun yang sedang digunakan tidak dapat dihapus.');
        $name = $user->name;
        ActivityLog::create(['user_id' => request()->user()->id, 'action' => 'delete', 'route' => request()->route()->getName(), 'method' => 'DELETE', 'ip_address' => request()->ip(), 'description' => 'Menghapus akun '.$name]);
        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'Akun login dan aksesnya berhasil dihapus.');
    }
}
