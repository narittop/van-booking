<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Van;
use App\Services\LineNotifyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DirectorController extends Controller
{
    /**
     * Display director dashboard.
     */
    public function dashboard()
    {
        $user = Auth::user();
        
        // Super admin can see all departments
        if ($user->isSuperAdmin()) {
            $departments = array_keys(Van::DEPARTMENT_LABELS);
        } else {
            $departments = $user->getDirectorDepartments();
        }

        if (empty($departments)) {
            return view('director.dashboard', [
                'stats' => [
                    'pending_approval' => 0,
                    'approved_today' => 0,
                    'total_received' => 0,
                ],
                'pendingBookings' => collect(),
                'todayBookings' => collect(),
            ]);
        }

        $bookingsQuery = Booking::whereIn('requested_department', $departments);

        $stats = [
            'pending_approval' => (clone $bookingsQuery)->where('status', 'received')->count(),
            'approved_today' => (clone $bookingsQuery)
                ->where('status', 'approved')
                ->whereDate('approved_at', today())
                ->count(),
            'total_received' => (clone $bookingsQuery)
                ->whereIn('status', ['received', 'approved', 'completed'])
                ->count(),
        ];

        $pendingBookings = (clone $bookingsQuery)
            ->with(['user', 'van', 'driver', 'receiver'])
            ->where('status', 'received')
            ->orderBy('received_at', 'asc')
            ->take(10)
            ->get();

        $todayBookings = (clone $bookingsQuery)
            ->with(['user', 'van', 'driver'])
            ->where('status', 'approved')
            ->where('start_date', '<=', today())
            ->where('end_date', '>=', today())
            ->orderBy('start_time')
            ->get();

        return view('director.dashboard', compact('stats', 'pendingBookings', 'todayBookings'));
    }

    /**
     * Display all bookings for director's departments.
     */
    public function bookings(Request $request)
    {
        $user = Auth::user();
        
        // Super admin can see all departments
        if ($user->isSuperAdmin()) {
            $departments = array_keys(Van::DEPARTMENT_LABELS);
        } else {
            $departments = $user->getDirectorDepartments();
        }

        $query = Booking::with(['user', 'van', 'receiver', 'approver'])
            ->whereIn('requested_department', $departments);

        // Filter by status
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Filter by department
        if ($request->has('department') && $request->department !== 'all') {
            $query->where('requested_department', $request->department);
        }

        // Filter by date
        if ($request->has('date')) {
            $query->whereDate('start_date', $request->date);
        }

        // Order by status priority: received (รอรับเรื่อง) -> approved -> completed -> others
        $bookings = $query->orderByRaw("FIELD(status, 'received', 'approved', 'completed', 'rejected', 'pending') ASC")
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('director.bookings.index', compact('bookings', 'departments'));
    }

    /**
     * Show booking details.
     */
    public function showBooking(Booking $booking)
    {
        $user = Auth::user();
        
        // Super admin can access all departments
        if (!$user->isSuperAdmin() && !$user->canDirectDepartment($booking->requested_department)) {
            abort(403, 'ไม่มีสิทธิ์ดูคำขอของหน่วยงานนี้');
        }

        $booking->load(['user', 'van', 'driver', 'passengers', 'receiver', 'approver']);

        return view('director.bookings.show', compact('booking'));
    }

    /**
     * Approve a booking (received -> approved).
     */
    public function approve(Request $request, Booking $booking)
    {
        $user = Auth::user();
        
        // Super admin can access all departments
        if (!$user->isSuperAdmin() && !$user->canDirectDepartment($booking->requested_department)) {
            abort(403, 'ไม่มีสิทธิ์อนุมัติคำขอของหน่วยงานนี้');
        }

        // Only received bookings can be approved by director
        if ($booking->status !== 'received') {
            return back()->with('error', 'สามารถอนุมัติได้เฉพาะรายการที่รับเรื่องแล้วเท่านั้น');
        }

        $validated = $request->validate([
            'director_notes' => 'nullable|string',
        ]);

        $booking->update([
            'status' => 'approved',
            'admin_notes' => $booking->admin_notes . ($validated['director_notes'] ? "\n[ผู้อำนวยการ] " . $validated['director_notes'] : ''),
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        // Send LINE Notify notification
        $booking->load(['user', 'van', 'driver']);
        (new LineNotifyService())->notifyBookingApproved($booking);

        return redirect()->route('director.bookings')
            ->with('success', 'อนุมัติการจองเรียบร้อยแล้ว');
    }

    /**
     * Reject a booking.
     */
    public function reject(Request $request, Booking $booking)
    {
        $user = Auth::user();
        
        // Super admin can access all departments
        if (!$user->isSuperAdmin() && !$user->canDirectDepartment($booking->requested_department)) {
            abort(403, 'ไม่มีสิทธิ์ปฏิเสธคำขอของหน่วยงานนี้');
        }

        // Only received bookings can be rejected by director
        if ($booking->status !== 'received') {
            return back()->with('error', 'สามารถไม่อนุมัติได้เฉพาะรายการที่รับเรื่องแล้วเท่านั้น');
        }

        $validated = $request->validate([
            'director_notes' => 'required|string',
        ]);

        $booking->update([
            'status' => 'rejected',
            'admin_notes' => $booking->admin_notes . "\n[ผู้อำนวยการไม่อนุมัติ] " . $validated['director_notes'],
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        // Send LINE Notify notification
        $booking->load('user');
        (new LineNotifyService())->notifyBookingRejected($booking);

        return redirect()->route('director.bookings')
            ->with('success', 'บันทึกการไม่อนุมัติเรียบร้อยแล้ว');
    }
}
