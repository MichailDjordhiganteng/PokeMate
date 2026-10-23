<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class AdminAccountController extends Controller
{
    public function index(Request $request)
    {
        $request->validate(['search' => ['nullable', 'string', 'max:100']]);
        $admins = User::where('role', 'admin')->withCount('processedRentals')
            ->when($request->filled('search'), fn ($query) => $query->where(fn ($query) => $query
                ->where('full_name', 'like', '%'.$request->search.'%')
                ->orWhere('email', 'like', '%'.$request->search.'%')))
            ->orderBy('full_name')->paginate(10)->withQueryString();

        return view('admin.admins', compact('admins'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);
        User::create($data + ['role' => 'admin']);

        return redirect()->route('admin.admins.index')->with('success', 'Akun admin berhasil ditambahkan.');
    }

    public function update(Request $request, User $admin)
    {
        abort_unless($admin->isAdmin(), 404);
        $data = $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($admin->id)],
            'password' => ['nullable', 'confirmed', Password::min(8)],
            'current_password' => $admin->is($request->user()) && $request->filled('password')
                ? ['required', 'current_password'] : ['nullable'],
        ]);
        $admin->update(collect($data)->only(['full_name', 'email', 'password'])
            ->reject(fn ($value, $key) => $key === 'password' && empty($value))->all());

        return back()->with('success', 'Akun admin berhasil diperbarui.');
    }

    public function destroy(Request $request, User $admin)
    {
        abort_unless($admin->isAdmin(), 404);
        DB::transaction(function () use ($request, $admin) {
            $admin = User::whereKey($admin->id)->lockForUpdate()->firstOrFail();
            if ($admin->is($request->user())) {
                throw ValidationException::withMessages(['admin' => 'Akun yang sedang digunakan tidak dapat dihapus.']);
            }
            if ($admin->processedRentals()->exists()) {
                throw ValidationException::withMessages(['admin' => 'Admin memiliki riwayat pemrosesan rental dan tidak dapat dihapus.']);
            }
            $admin->delete();
        });

        return back()->with('success', 'Akun admin berhasil dihapus.');
    }
}
