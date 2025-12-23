<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Van;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
    /**
     * Display a listing of user's bookings.
     */
    public function index()
    {
        $bookings = Auth::user()->bookings()
            ->with(['van'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('bookings.index', compact('bookings'));
    }

    /**
     * Show the form for creating a new booking.
     */
    public function create()
    {
        $vans = Van::where('status', 'active')->get();
        return view('bookings.create', compact('vans'));
    }

    /**
     * Store a newly created booking.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'travel_date' => 'required|date|after_or_equal:today',
            'departure_time' => 'required',
            'return_time' => 'nullable',
            'seats_requested' => 'required|integer|min:1|max:15',
            'origin' => 'required|string|max:255',
            'destination' => 'required|string|max:255',
            'purpose' => 'required|string',
            'passengers' => 'nullable|array',
            'passengers.*.name' => 'required_with:passengers|string|max:255',
            'passengers.*.department' => 'nullable|string|max:255',
        ]);

        $booking = Auth::user()->bookings()->create([
            'travel_date' => $validated['travel_date'],
            'departure_time' => $validated['departure_time'],
            'return_time' => $validated['return_time'],
            'seats_requested' => $validated['seats_requested'],
            'origin' => $validated['origin'],
            'destination' => $validated['destination'],
            'purpose' => $validated['purpose'],
            'status' => 'pending',
        ]);

        // Add passengers if provided
        if (!empty($validated['passengers'])) {
            foreach ($validated['passengers'] as $passenger) {
                $booking->passengers()->create([
                    'name' => $passenger['name'],
                    'department' => $passenger['department'] ?? null,
                ]);
            }
        }

        return redirect()->route('bookings.index')
            ->with('success', 'ส่งคำขอจองรถเรียบร้อยแล้ว รอการอนุมัติจากผู้ดูแลระบบ');
    }

    /**
     * Display the specified booking.
     */
    public function show(Booking $booking)
    {
        // Ensure user can only view their own bookings
        if ($booking->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403);
        }

        $booking->load(['van', 'passengers', 'approver']);
        return view('bookings.show', compact('booking'));
    }

    /**
     * Cancel a booking.
     */
    public function destroy(Booking $booking)
    {
        // Only allow cancellation of own pending bookings
        if ($booking->user_id !== Auth::id()) {
            abort(403);
        }

        if ($booking->status !== 'pending') {
            return back()->with('error', 'ไม่สามารถยกเลิกการจองที่ได้รับการอนุมัติแล้ว');
        }

        $booking->delete();

        return redirect()->route('bookings.index')
            ->with('success', 'ยกเลิกการจองเรียบร้อยแล้ว');
    }
}
