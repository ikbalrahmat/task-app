<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use App\Models\ActivityLog;
use App\Models\UnitKerja;
use App\Services\UserService;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct(private UserService $service) {}

    public function index(Request $request)
    {
        $this->authorize('viewAny', User::class);
        $filters = $request->only(['search', 'role']);
        $users = $this->service->paginate(10, $filters);
        return view('users.index', compact('users', 'filters'));
    }

    public function create()
    {
        $this->authorize('create', User::class);
        $unitKerjas = UnitKerja::where('is_active', true)->orderBy('name')->get();
        return view('users.create', compact('unitKerjas'));
    }

    public function store(StoreUserRequest $request)
    {
        $this->authorize('create', User::class);
        $data = $request->validated();

        // Kalau bukan Super Admin, otomatis assign ke unit kerja sendiri
        if (!auth()->user()->isSuperAdmin()) {
            $data['unit_kerja_id'] = auth()->user()->unit_kerja_id;
        }

        $user = $this->service->create($data);
        
        ActivityLogger::log('user.created', 'Admin membuat pengguna baru: ' . $user->email);
        
        return redirect()->route('users.index')->with('success', 'Pengguna berhasil ditambahkan.');
    }

    public function edit(int $id)
    {
        $user = $this->service->find($id);
        $this->authorize('update', $user);
        $unitKerjas = UnitKerja::where('is_active', true)->orderBy('name')->get();
        return view('users.edit', compact('user', 'unitKerjas'));
    }

    public function update(UpdateUserRequest $request, int $id)
    {
        $user = $this->service->find($id);
        $this->authorize('update', $user);

        $data = $request->validated();

        // Kalau bukan Super Admin, paksa unit kerja tetap sama
        if (!auth()->user()->isSuperAdmin()) {
            $data['unit_kerja_id'] = auth()->user()->unit_kerja_id;
        }
        
        $updatedUser = $this->service->update($id, $data);
        
        ActivityLogger::log('user.updated', 'Admin memperbarui data pengguna: ' . $updatedUser->email);
        
        return redirect()->route('users.index')->with('success', 'Pengguna berhasil diperbarui.');
    }

    public function destroy(int $id)
    {
        $user = $this->service->find($id);
        $this->authorize('delete', $user);
        
        if ($user->id === auth()->id()) {
            return back()->withErrors(['error' => 'Anda tidak bisa menghapus akun Anda sendiri.']);
        }
        
        $email = $user->email;
        $this->service->delete($id);
        
        ActivityLogger::log('user.deleted', 'Admin menghapus pengguna: ' . $email);
        
        return redirect()->route('users.index')->with('success', 'Pengguna berhasil dihapus.');
    }

    public function unlock(Request $request, int $id)
    {
        $this->authorize('viewAny', User::class); // Only admins/auth can access user management
        $user = $this->service->find($id);
        
        $user->is_locked = false;
        $user->login_attempts = 0;
        $user->save();

        ActivityLogger::log('user.unlocked', 'Admin mengaktifkan kembali akun pengguna: ' . $user->email, $user->id);

        return redirect()->route('users.index')->with('success', "Akun {$user->name} berhasil diaktifkan kembali.");
    }

    public function logs(Request $request)
    {
        $this->authorize('viewAny', User::class);

        $query = ActivityLog::with('user')->orderBy('created_at', 'desc');

        // Isolasi per unit kerja: Admin hanya lihat log dari user di unitnya
        if (!auth()->user()->isSuperAdmin()) {
            $unitKerjaId = auth()->user()->unit_kerja_id;
            $query->where(function ($q) use ($unitKerjaId) {
                // Log dari user yang sama unit kerjanya
                $q->whereHas('user', fn($uq) => $uq->where('unit_kerja_id', $unitKerjaId))
                  // Atau log dari user yang belum login (null user_id) — skip
                  ->orWhereNull('user_id');
            });
            // Tapi yang null user_id jangan ikut (bisa log dari unit lain saat belum login)
            $query->whereNotNull('user_id')
                  ->whereHas('user', fn($uq) => $uq->where('unit_kerja_id', $unitKerjaId));
        }
        
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('event_type', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('ip_address', 'like', "%{$search}%")
                  ->orWhereHas('user', function($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }
        
        $logs = $query->paginate(20)->withQueryString();
        
        return view('users.logs', compact('logs'));
    }
}
