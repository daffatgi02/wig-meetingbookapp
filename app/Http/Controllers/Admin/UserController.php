<?php
// app/Http/Controllers/Admin/UserController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    protected $activityLogService;

    public function __construct(ActivityLogService $activityLogService)
    {
        $this->activityLogService = $activityLogService;
        $this->middleware('auth');
        $this->middleware('admin');
    }

    public function index(Request $request)
    {
        $query = User::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('status')) {
            $isActive = $request->status === 'active';
            $query->where('is_active', $isActive);
        }

        $users = $query->orderBy('name')->paginate(15);

        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(StoreUserRequest $request)
    {
        try {
            $data = $request->validated();
            $data['password'] = Hash::make($data['password']);
            $data['email_verified_at'] = now();

            $user = User::create($data);

            $this->activityLogService->log('created', $user, null, $user->toArray());

            return redirect()->route('admin.users.show', $user)
                           ->with('success', 'User berhasil ditambahkan.');
        } catch (\Exception $e) {
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function show(User $user)
    {
        $user->load(['bookings' => function ($query) {
            $query->with('room')->orderBy('booking_date', 'desc')->limit(10);
        }]);

        $bookingStats = [
            'total' => $user->bookings()->count(),
            'pending' => $user->bookings()->where('status', 'pending')->count(),
            'approved' => $user->bookings()->where('status', 'approved')->count(),
            'completed' => $user->bookings()->where('status', 'completed')->count(),
        ];

        return view('admin.users.show', compact('user', 'bookingStats'));
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        try {
            $oldData = $user->toArray();
            $data = $request->validated();

            // Update password jika diisi
            if (!empty($data['password'])) {
                $data['password'] = Hash::make($data['password']);
            } else {
                unset($data['password']);
            }

            $user->update($data);

            $this->activityLogService->log('updated', $user, $oldData, $user->fresh()->toArray());

            return redirect()->route('admin.users.show', $user)
                           ->with('success', 'User berhasil diperbarui.');
        } catch (\Exception $e) {
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function destroy(User $user)
    {
        // Tidak bisa hapus diri sendiri
        if ($user->id === auth()->id()) {
            return redirect()->back()
                           ->with('error', 'Tidak dapat menghapus akun sendiri.');
        }

        // Cek apakah ada booking aktif
        $hasActiveBookings = $user->bookings()
                                 ->whereIn('status', ['pending', 'approved', 'ongoing'])
                                 ->exists();

        if ($hasActiveBookings) {
            return redirect()->back()
                           ->with('error', 'User tidak dapat dihapus karena masih ada pemesanan aktif.');
        }

        try {
            $this->activityLogService->log('deleted', $user, $user->toArray(), null);
            $user->delete();

            return redirect()->route('admin.users.index')
                           ->with('success', 'User berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->back()
                           ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function toggle(User $user)
    {
        // Tidak bisa nonaktifkan diri sendiri
        if ($user->id === auth()->id()) {
            return redirect()->back()
                           ->with('error', 'Tidak dapat mengubah status akun sendiri.');
        }

        try {
            $oldData = $user->toArray();
            $user->update(['is_active' => !$user->is_active]);

            $action = $user->is_active ? 'activated' : 'deactivated';
            $this->activityLogService->log($action, $user, $oldData, $user->fresh()->toArray());

            $status = $user->is_active ? 'diaktifkan' : 'dinonaktifkan';
            return redirect()->back()
                           ->with('success', "User berhasil {$status}.");
        } catch (\Exception $e) {
            return redirect()->back()
                           ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}