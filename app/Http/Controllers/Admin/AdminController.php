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
        $vansQuery = Van::query();
        if (!$user->isSuperAdmin() && $user->isDepartmentAdmin()) {
            $bookingsQuery->where('requested_department', $user->getAdminDepartment());
            $vansQuery->where('owner_department', $user->getAdminDepartment());
        }
        
        $stats = [
            'total_vans' => (clone $vansQuery)->count(),
            'active_vans' => (clone $vansQuery)->where('status', 'active')->count(),
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
        $query = Booking::with(['user', 'van', 'driver']);

        // Filter by department for department admins
        if (!$user->isSuperAdmin() && $user->isDepartmentAdmin()) {
            $query->where('requested_department', $user->getAdminDepartment());
        }

        // Filter by status
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Filter by date (only when a date is actually provided)
        if ($request->filled('date')) {
            $query->whereDate('start_date', $request->date);
        }

        // Order by status priority: pending (รอรับเรื่อง) -> received (รออนุมัติ) -> approved -> completed -> others
        $bookings = $query->orderByRaw("FIELD(status, 'pending', 'received', 'approved', 'completed', 'rejected', 'cancelled') ASC")
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        // Load all vans and drivers - filtering by booking's requested_department is done in the view
        $vans = Van::where('status', 'active')->get();
        $drivers = User::where('role', 'driver')->orderBy('name')->get();

        return view('admin.bookings.index', compact('bookings', 'vans', 'drivers'));
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
     * Reject a booking (รับเรื่อง(ไม่จัดรถ)).
     */
    public function reject(Request $request, Booking $booking)
    {
        // Check department access
        $user = Auth::user();
        if (!$user->isSuperAdmin() && $user->isDepartmentAdmin()) {
            if ($booking->requested_department !== $user->getAdminDepartment()) {
                abort(403, 'ไม่มีสิทธิ์รับเรื่อง(ไม่จัดรถ)คำขอของหน่วยงานนี้');
            }
        }

        $validated = $request->validate([
            'admin_notes' => 'required|string',
        ]);

        $booking->update([
            'status' => 'received',
            'van_id' => null,
            'driver_id' => null,
            'admin_notes' => $validated['admin_notes'],
            'received_by' => Auth::id(),
            'received_at' => now(),
        ]);

        // Send LINE Notify notification
        $booking->load(['user', 'van', 'driver']);
        (new LineNotifyService())->notifyBookingReceived($booking);

        return redirect()->route('admin.bookings')
            ->with('success', 'ส่งเรื่องไปยังผู้อนุมัติเรียบร้อยแล้ว');
    }

    /**
     * Mark booking as completed.
     */
    public function complete(Request $request, Booking $booking)
    {
        // Check department access
        $user = Auth::user();
        if (!$user->isSuperAdmin() && $user->isDepartmentAdmin()) {
            if ($booking->requested_department !== $user->getAdminDepartment()) {
                abort(403, 'ไม่มีสิทธิ์ดำเนินการกับคำขอของหน่วยงานนี้');
            }
        }

        $validated = $request->validate([
            'start_mileage' => 'required|numeric|min:0',
            'end_mileage' => 'required|numeric|gt:start_mileage',
            'total_distance' => 'nullable|numeric|min:0',
        ]);

        $booking->update([
            'status' => 'completed',
            'start_mileage' => $validated['start_mileage'] ?? null,
            'end_mileage' => $validated['end_mileage'] ?? null,
            'total_distance' => $validated['total_distance'] ?? null,
        ]);

        // Send LINE Notify notification
        $booking->load(['user', 'van']);
        (new LineNotifyService())->notifyBookingCompleted($booking);

        return back()->with('success', 'บันทึกการเดินทางเสร็จสิ้นแล้ว');
    }

    /**
     * Update van and driver assignment for a booking.
     */
    public function updateAssignment(Request $request, Booking $booking)
    {
        // Only allow editing for received or approved bookings
        if (!in_array($booking->status, ['received', 'approved'])) {
            return back()->with('error', 'ไม่สามารถแก้ไขการจัดรถสำหรับคำขอที่มีสถานะนี้ได้');
        }

        // Check department access
        $user = Auth::user();
        if (!$user->isSuperAdmin() && $user->isDepartmentAdmin()) {
            if ($booking->requested_department !== $user->getAdminDepartment()) {
                abort(403, 'ไม่มีสิทธิ์แก้ไขคำขอของหน่วยงานนี้');
            }
        }

        $validated = $request->validate([
            'van_id' => 'required|exists:vans,id',
            'driver_id' => 'nullable|exists:users,id',
        ]);

        // Check van availability
        $van = Van::findOrFail($validated['van_id']);
        
        // If van changed, check seat availability
        if ($van->id !== $booking->van_id) {
            $available = $van->getAvailableSeatsForDateRange($booking->start_date, $booking->end_date);
            if ($available < $booking->seats_requested) {
                return back()->with('error', 'ที่นั่งไม่เพียงพอ (ว่าง: ' . $available . ' ที่นั่ง)');
            }
        }

        $booking->update([
            'van_id' => $validated['van_id'],
            'driver_id' => $validated['driver_id'] ?? null,
        ]);

        return back()->with('success', 'แก้ไขการจัดรถและคนขับเรียบร้อยแล้ว');
    }

    /**
     * Cancel a booking (ยกเลิกคำขอ).
     */
    public function cancel(Request $request, Booking $booking)
    {
        // Only allow cancelling for pending, received, approved
        if (!in_array($booking->status, ['pending', 'received', 'approved'])) {
            return back()->with('error', 'ไม่สามารถยกเลิกคำขอที่มีสถานะนี้ได้');
        }

        // Check department access
        $user = Auth::user();
        if (!$user->isSuperAdmin() && $user->isDepartmentAdmin()) {
            if ($booking->requested_department !== $user->getAdminDepartment()) {
                abort(403, 'ไม่มีสิทธิ์ยกเลิกคำขอของหน่วยงานนี้');
            }
        }

        $validated = $request->validate([
            'cancelled_reason' => 'required|string|max:1000',
            'cancelled_by_name' => 'required|string|max:255',
        ]);

        $booking->update([
            'status' => 'cancelled',
            'cancelled_reason' => $validated['cancelled_reason'],
            'cancelled_by_name' => $validated['cancelled_by_name'],
            'cancelled_by' => Auth::id(),
            'cancelled_at' => now(),
        ]);

        return back()->with('success', 'ยกเลิกคำขอเรียบร้อยแล้ว');
    }

    /**
     * Display reporting dashboard.
     */
    public function reports(Request $request)
    {
        $user = Auth::user();
        
        // Base dates
        $startDate = $request->input('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->endOfMonth()->format('Y-m-d'));

        // Query Bookings
        $bookingsQuery = Booking::with(['user', 'van', 'driver']);
        if (!$user->isSuperAdmin() && $user->isDepartmentAdmin()) {
            $bookingsQuery->where('requested_department', $user->getAdminDepartment());
        } elseif ($user->isSuperAdmin() && $request->filled('department') && $request->department !== 'all') {
            $bookingsQuery->where('requested_department', $request->department);
        }
        
        $bookingsQuery->whereDate('start_date', '>=', $startDate)
                      ->whereDate('start_date', '<=', $endDate);
        
        $bookings = $bookingsQuery->orderBy('start_date', 'desc')->get();

        // Query Vans for vehicle summaries
        $vansQuery = Van::query();
        if (!$user->isSuperAdmin() && $user->isDepartmentAdmin()) {
            $vansQuery->where('owner_department', $user->getAdminDepartment());
        } elseif ($user->isSuperAdmin() && $request->filled('department') && $request->department !== 'all') {
            $vansQuery->where('owner_department', $request->department);
        }
        $vans = $vansQuery->get();

        $vanSummaries = [];
        foreach ($vans as $van) {
            $vanBookings = Booking::where('van_id', $van->id)
                ->whereDate('start_date', '>=', $startDate)
                ->whereDate('start_date', '<=', $endDate)
                ->whereIn('status', ['approved', 'completed'])
                ->get();

            $vanSummaries[] = [
                'name' => $van->name,
                'license_plate' => $van->license_plate,
                'owner_department' => $van->owner_department,
                'trips_count' => $vanBookings->count(),
                'total_distance' => floatval($vanBookings->sum('total_distance')),
            ];
        }

        // Stats
        $totalBookings = $bookings->count();
        $completedBookings = $bookings->where('status', 'completed')->count();
        $pendingBookings = $bookings->where('status', 'pending')->count();
        $approvedBookings = $bookings->where('status', 'approved')->count();
        $rejectedBookings = $bookings->where('status', 'rejected')->count();

        $totalDistance = $bookings->where('status', 'completed')->sum('total_distance');
        $avgDistance = $completedBookings > 0 ? ($totalDistance / $completedBookings) : 0;

        // Chart data arrays
        $chartVanNames = [];
        $chartVanTrips = [];
        $chartVanDistances = [];
        foreach ($vanSummaries as $summary) {
            $chartVanNames[] = $summary['name'] . ' (' . $summary['license_plate'] . ')';
            $chartVanTrips[] = $summary['trips_count'];
            $chartVanDistances[] = $summary['total_distance'];
        }

        return view('admin.reports.index', compact(
            'bookings',
            'vanSummaries',
            'startDate',
            'endDate',
            'totalBookings',
            'completedBookings',
            'pendingBookings',
            'approvedBookings',
            'rejectedBookings',
            'totalDistance',
            'avgDistance',
            'chartVanNames',
            'chartVanTrips',
            'chartVanDistances'
        ));
    }

    /**
     * Export reports data to Excel format.
     */
    public function exportReport(Request $request)
    {
        $user = Auth::user();
        
        $startDate = $request->input('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->endOfMonth()->format('Y-m-d'));

        // Query Bookings (same filters)
        $bookingsQuery = Booking::with(['user', 'van', 'driver']);
        if (!$user->isSuperAdmin() && $user->isDepartmentAdmin()) {
            $bookingsQuery->where('requested_department', $user->getAdminDepartment());
        } elseif ($user->isSuperAdmin() && $request->filled('department') && $request->department !== 'all') {
            $bookingsQuery->where('requested_department', $request->department);
        }
        $bookingsQuery->whereDate('start_date', '>=', $startDate)
                      ->whereDate('start_date', '<=', $endDate);
        $bookings = $bookingsQuery->orderBy('start_date', 'desc')->get();

        // Query Vans (same filters)
        $vansQuery = Van::query();
        if (!$user->isSuperAdmin() && $user->isDepartmentAdmin()) {
            $vansQuery->where('owner_department', $user->getAdminDepartment());
        } elseif ($user->isSuperAdmin() && $request->filled('department') && $request->department !== 'all') {
            $vansQuery->where('owner_department', $request->department);
        }
        $vans = $vansQuery->get();

        // Compute Summaries
        $vanSummaries = [];
        foreach ($vans as $van) {
            $vanBookings = Booking::where('van_id', $van->id)
                ->whereDate('start_date', '>=', $startDate)
                ->whereDate('start_date', '<=', $endDate)
                ->whereIn('status', ['approved', 'completed'])
                ->get();
            $vanSummaries[] = [
                'name' => $van->name,
                'license_plate' => $van->license_plate,
                'owner_department' => $van->owner_department,
                'trips_count' => $vanBookings->count(),
                'total_distance' => $vanBookings->sum('total_distance'),
            ];
        }

        // Stats
        $totalBookings = $bookings->count();
        $completedBookings = $bookings->where('status', 'completed')->count();
        $totalDistance = $bookings->where('status', 'completed')->sum('total_distance');

        // Render HTML representation
        $html = view('admin.reports.excel', compact(
            'bookings', 'vanSummaries', 'startDate', 'endDate',
            'totalBookings', 'completedBookings', 'totalDistance'
        ))->render();

        $filename = 'car_usage_report_' . $startDate . '_to_' . $endDate . '.xls';

        return response($html)
            ->header('Content-Type', 'application/vnd.ms-excel; charset=utf-8')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }
}
