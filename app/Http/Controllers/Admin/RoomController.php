<?php
// app/Http/Controllers/Admin/RoomController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRoomRequest;
use App\Http\Requests\UpdateRoomRequest;
use App\Models\Room;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;

class RoomController extends Controller
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
        $query = Room::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $isActive = $request->status === 'active';
            $query->where('is_active', $isActive);
        }

        $rooms = $query->orderBy('name')->paginate(10);

        return view('admin.rooms.index', compact('rooms'));
    }

    public function create()
    {
        return view('admin.rooms.create');
    }

    public function store(StoreRoomRequest $request)
    {
        try {
            $room = Room::create($request->validated());

            $this->activityLogService->log('created', $room, null, $room->toArray());

            return redirect()->route('admin.rooms.show', $room)
                           ->with('success', 'Ruangan berhasil ditambahkan.');
        } catch (\Exception $e) {
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function show(Room $room)
    {
        $room->load(['bookings' => function ($query) {
            $query->with('user')->orderBy('booking_date', 'desc')->limit(10);
        }]);

        return view('admin.rooms.show', compact('room'));
    }

    public function edit(Room $room)
    {
        return view('admin.rooms.edit', compact('room'));
    }

    public function update(UpdateRoomRequest $request, Room $room)
    {
        try {
            $oldData = $room->toArray();
            $room->update($request->validated());

            $this->activityLogService->log('updated', $room, $oldData, $room->fresh()->toArray());

            return redirect()->route('admin.rooms.show', $room)
                           ->with('success', 'Ruangan berhasil diperbarui.');
        } catch (\Exception $e) {
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function destroy(Room $room)
    {
        // Cek apakah ada booking aktif
        $hasActiveBookings = $room->bookings()
                                 ->whereIn('status', ['pending', 'approved', 'ongoing'])
                                 ->exists();

        if ($hasActiveBookings) {
            return redirect()->back()
                           ->with('error', 'Ruangan tidak dapat dihapus karena masih ada pemesanan aktif.');
        }

        try {
            $this->activityLogService->log('deleted', $room, $room->toArray(), null);
            $room->delete();

            return redirect()->route('admin.rooms.index')
                           ->with('success', 'Ruangan berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->back()
                           ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function toggle(Room $room)
    {
        try {
            $oldData = $room->toArray();
            $room->update(['is_active' => !$room->is_active]);

            $action = $room->is_active ? 'activated' : 'deactivated';
            $this->activityLogService->log($action, $room, $oldData, $room->fresh()->toArray());

            $status = $room->is_active ? 'diaktifkan' : 'dinonaktifkan';
            return redirect()->back()
                           ->with('success', "Ruangan berhasil {$status}.");
        } catch (\Exception $e) {
            return redirect()->back()
                           ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}