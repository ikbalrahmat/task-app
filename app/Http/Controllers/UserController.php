<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use App\Services\UserService;
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
        return view('users.create');
    }

    public function store(StoreUserRequest $request)
    {
        $this->authorize('create', User::class);
        $this->service->create($request->validated());
        return redirect()->route('users.index')->with('success', 'Pengguna berhasil ditambahkan.');
    }

    public function edit(int $id)
    {
        $user = $this->service->find($id);
        $this->authorize('update', $user);
        return view('users.edit', compact('user'));
    }

    public function update(UpdateUserRequest $request, int $id)
    {
        $user = $this->service->find($id);
        $this->authorize('update', $user);
        $this->service->update($id, $request->validated());
        return redirect()->route('users.index')->with('success', 'Pengguna berhasil diperbarui.');
    }

    public function destroy(int $id)
    {
        $user = $this->service->find($id);
        $this->authorize('delete', $user);
        if ($user->id === auth()->id()) {
            return back()->withErrors(['error' => 'Anda tidak bisa menghapus akun Anda sendiri.']);
        }
        $this->service->delete($id);
        return redirect()->route('users.index')->with('success', 'Pengguna berhasil dihapus.');
    }
}
