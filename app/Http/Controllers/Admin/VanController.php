<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Van;
use Illuminate\Http\Request;

class VanController extends Controller
{
    /**
     * Display a listing of vans.
     */
    public function index()
    {
        $vans = Van::withCount('bookings')->paginate(10);
        return view('admin.vans.index', compact('vans'));
    }

    /**
     * Show the form for creating a new van.
     */
    public function create()
    {
        return view('admin.vans.create');
    }

    /**
     * Store a newly created van.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'license_plate' => 'required|string|max:20|unique:vans',
            'capacity' => 'required|integer|min:1|max:50',
            'status' => 'required|in:active,maintenance',
            'description' => 'nullable|string',
        ]);

        Van::create($validated);

        return redirect()->route('admin.vans.index')
            ->with('success', 'เพิ่มรถตู้เรียบร้อยแล้ว');
    }

    /**
     * Show the form for editing a van.
     */
    public function edit(Van $van)
    {
        return view('admin.vans.edit', compact('van'));
    }

    /**
     * Update the specified van.
     */
    public function update(Request $request, Van $van)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'license_plate' => 'required|string|max:20|unique:vans,license_plate,' . $van->id,
            'capacity' => 'required|integer|min:1|max:50',
            'status' => 'required|in:active,maintenance',
            'description' => 'nullable|string',
        ]);

        $van->update($validated);

        return redirect()->route('admin.vans.index')
            ->with('success', 'แก้ไขข้อมูลรถตู้เรียบร้อยแล้ว');
    }

    /**
     * Remove the specified van.
     */
    public function destroy(Van $van)
    {
        // Check if van has active bookings
        $activeBookings = $van->bookings()
            ->whereIn('status', ['pending', 'approved'])
            ->where('travel_date', '>=', today())
            ->count();

        if ($activeBookings > 0) {
            return back()->with('error', 'ไม่สามารถลบรถที่มีการจองอยู่ได้');
        }

        $van->delete();

        return redirect()->route('admin.vans.index')
            ->with('success', 'ลบรถตู้เรียบร้อยแล้ว');
    }
}
