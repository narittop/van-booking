<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Van;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    /**
     * Display admin dashboard.
     */
    public function dashboard()
    {
        $stats = [
            'total_vans' => Van::count(),
            'active_vans' => Van::where('status', 'active')->count(),
            'pending_bookings' => Booking::where('status', 'pending')->count(),
            'approved_today' => Booking::where('status', 'approved')
                ->whereDate('travel_date', today())
                ->count(),
            'total_users' => User::where('role', 'user')->count(),
        ];

        $pendingBookings = Booking::with(['user', 'van'])
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        $todayBookings = Booking::with(['user', 'van'])
            ->where('status', 'approved')
            ->whereDate('travel_date', today())
            ->orderBy('departure_time')
            ->get();

        return view('admin.dashboard', compact('stats', 'pendingBookings', 'todayBookings'));
    }

    /**
     * Display all bookings.
     */
    public function bookings(Request $request)
    {
        $query = Booking::with(['user', 'van']);

        // Filter by status
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Filter by date
        if ($request->has('date')) {
            $query->whereDate('travel_date', $request->date);
        }

        $bookings = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('admin.bookings.index', compact('bookings'));
    }

    /**
     * Show booking details.
     */
    public function showBooking(Booking $booking)
    {
        $booking->load(['user', 'van', 'passengers', 'approver']);
        $vans = Van::where('status', 'active')->get();

        return view('admin.bookings.show', compact('booking', 'vans'));
    }

    /**
     * Approve a booking.
     */
    public function approve(Request $request, Booking $booking)
    {
        $validated = $request->validate([
            'van_id' => 'required|exists:vans,id',
            'admin_notes' => 'nullable|string',
        ]);

        // Check van availability
        $van = Van::findOrFail($validated['van_id']);
        $availableSeats = $van->getAvailableSeats($booking->travel_date);

        if ($availableSeats < $booking->seats_requested) {
            return back()->with('error', 'ที่นั่งไม่เพียงพอ (ว่าง: ' . $availableSeats . ' ที่นั่ง)');
        }

        $booking->update([
            'van_id' => $validated['van_id'],
            'status' => 'approved',
            'admin_notes' => $validated['admin_notes'],
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        return redirect()->route('admin.bookings')
            ->with('success', 'อนุมัติการจองเรียบร้อยแล้ว');
    }

    /**
     * Reject a booking.
     */
    public function reject(Request $request, Booking $booking)
    {
        $validated = $request->validate([
            'admin_notes' => 'required|string',
        ]);

        $booking->update([
            'status' => 'rejected',
            'admin_notes' => $validated['admin_notes'],
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        return redirect()->route('admin.bookings')
            ->with('success', 'ปฏิเสธการจองเรียบร้อยแล้ว');
    }

    /**
     * Mark booking as completed.
     */
    public function complete(Booking $booking)
    {
        $booking->update(['status' => 'completed']);

        return back()->with('success', 'บันทึกการเดินทางเสร็จสิ้นแล้ว');
    }
}
