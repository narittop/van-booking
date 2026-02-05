<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Van;
use App\Models\User;
use App\Services\LineNotifyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    /**
     * Display admin dashboard.
     */
    public function dashboard()
    {
        $user = Auth::user();
        
        // Base query - filter by department for department admins
        $bookingsQuery = Booking::query();
        if (!$user->isSuperAdmin() && $user->isDepartmentAdmin()) {
            $bookingsQuery->where('requested_department', $user->getAdminDepartment());
        }
        
        $stats = [
            'total_vans' => Van::count(),
            'active_vans' => Van::where('status', 'active')->count(),
            'pending_bookings' => (clone $bookingsQuery)->where('status', 'pending')->count(),
            'approved_today' => (clone $bookingsQuery)->where('status', 'approved')
                ->whereDate('start_date', today())
                ->count(),
            'total_users' => User::where('role', 'user')->count(),
        ];

        $pendingBookings = (clone $bookingsQuery)->with(['user', 'van'])
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        $todayBookings = (clone $bookingsQuery)->with(['user', 'van'])
            ->where('status', 'approved')
            ->where('start_date', '<=', today())
            ->where('end_date', '>=', today())
            ->orderBy('start_time')
            ->get();

        return view('admin.dashboard', compact('stats', 'pendingBookings', 'todayBookings'));
    }

    /**
     * Display all bookings.
     */
    public function bookings(Request $request)
    {
        $user = Auth::user();
        $query = Booking::with(['user', 'van']);

        // Filter by department for department admins
        if (!$user->isSuperAdmin() && $user->isDepartmentAdmin()) {
            $query->where('requested_department', $user->getAdminDepartment());
        }

        // Filter by status
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Filter by date
        if ($request->has('date')) {
            $query->whereDate('start_date', $request->date);
        }

        // Order by status priority: pending (รอรับเรื่อง) -> received (รออนุมัติ) -> approved -> completed -> others
        $bookings = $query->orderByRaw("FIELD(status, 'pending', 'received', 'approved', 'completed', 'rejected') ASC")
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.bookings.index', compact('bookings'));
    }

    /**
     * Show booking details.
     */
    public function showBooking(Booking $booking)
    {
        // Check department access
        $user = Auth::user();
        if (!$user->isSuperAdmin() && $user->isDepartmentAdmin()) {
            if ($booking->requested_department !== $user->getAdminDepartment()) {
                abort(403, 'ไม่มีสิทธิ์ดูคำขอของหน่วยงานนี้');
            }
        }

        $booking->load(['user', 'van', 'driver', 'passengers', 'approver']);
        
        // Filter vans and drivers by the booking's requested department
        $requestedDept = $booking->requested_department;
        
        $vans = Van::where('status', 'active')
            ->where('owner_department', $requestedDept)
            ->get();
            
        $drivers = User::where('role', 'driver')
            ->where('department', $requestedDept)
            ->orderBy('name')
            ->get();

        // Get conflicting bookings (approved bookings with overlapping date range in the same department)
        $conflictingBookings = Booking::with(['van', 'user', 'driver'])
            ->where('id', '!=', $booking->id)
            ->where('status', 'approved')
            ->where('requested_department', $requestedDept)
            ->where(function ($query) use ($booking) {
                // Check for date range overlap
                $query->where(function ($q) use ($booking) {
                    $q->where('start_date', '<=', $booking->end_date)
                      ->where('end_date', '>=', $booking->start_date);
                });
            })
            ->orderBy('start_date')
            ->get();

        return view('admin.bookings.show', compact('booking', 'vans', 'drivers', 'conflictingBookings'));
    }

    /**
     * Receive a booking (รับเรื่อง - pending -> received).
     */
    public function receive(Request $request, Booking $booking)
    {
        // Check department access
        $user = Auth::user();
        if (!$user->isSuperAdmin() && $user->isDepartmentAdmin()) {
            if ($booking->requested_department !== $user->getAdminDepartment()) {
                abort(403, 'ไม่มีสิทธิ์รับเรื่องคำขอของหน่วยงานนี้');
            }
        }

        $validated = $request->validate([
            'van_id' => 'required|exists:vans,id',
            'driver_id' => 'nullable|exists:users,id',
            'admin_notes' => 'nullable|string',
        ]);

        // Check van availability
        $van = Van::findOrFail($validated['van_id']);
        $availableSeats = $van->getAvailableSeats($booking->start_date);

        if ($availableSeats < $booking->seats_requested) {
            return back()->with('error', 'ที่นั่งไม่เพียงพอ (ว่าง: ' . $availableSeats . ' ที่นั่ง)');
        }

        $booking->update([
            'van_id' => $validated['van_id'],
            'driver_id' => $validated['driver_id'] ?? null,
            'status' => 'received',
            'admin_notes' => $validated['admin_notes'],
            'received_by' => Auth::id(),
            'received_at' => now(),
        ]);

        // Send LINE Notify notification
        $booking->load(['user', 'van', 'driver']);
        (new LineNotifyService())->notifyBookingReceived($booking);

        return redirect()->route('admin.bookings')
            ->with('success', 'รับเรื่องการจองเรียบร้อยแล้ว');
    }

    /**
     * Reject a booking (ไม่รับเรื่อง).
     */
    public function reject(Request $request, Booking $booking)
    {
        // Check department access
        $user = Auth::user();
        if (!$user->isSuperAdmin() && $user->isDepartmentAdmin()) {
            if ($booking->requested_department !== $user->getAdminDepartment()) {
                abort(403, 'ไม่มีสิทธิ์ไม่รับเรื่องคำขอของหน่วยงานนี้');
            }
        }

        $validated = $request->validate([
            'admin_notes' => 'required|string',
        ]);

        $booking->update([
            'status' => 'rejected',
            'admin_notes' => $validated['admin_notes'],
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        // Send LINE Notify notification
        $booking->load('user');
        (new LineNotifyService())->notifyBookingRejected($booking);

        return redirect()->route('admin.bookings')
            ->with('success', 'ปฏิเสธการจองเรียบร้อยแล้ว');
    }

    /**
     * Mark booking as completed.
     */
    public function complete(Booking $booking)
    {
        // Check department access
        $user = Auth::user();
        if (!$user->isSuperAdmin() && $user->isDepartmentAdmin()) {
            if ($booking->requested_department !== $user->getAdminDepartment()) {
                abort(403, 'ไม่มีสิทธิ์ดำเนินการกับคำขอของหน่วยงานนี้');
            }
        }

        $booking->update(['status' => 'completed']);

        // Send LINE Notify notification
        $booking->load(['user', 'van']);
        (new LineNotifyService())->notifyBookingCompleted($booking);

        return back()->with('success', 'บันทึกการเดินทางเสร็จสิ้นแล้ว');
    }
}
