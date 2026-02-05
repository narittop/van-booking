<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            แดชบอร์ดผู้อำนวยการ
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">รอการอนุมัติ</p>
                            <p class="text-2xl font-semibold text-gray-900">{{ $stats['pending_approval'] }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-green-100 text-green-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">อนุมัติวันนี้</p>
                            <p class="text-2xl font-semibold text-gray-900">{{ $stats['approved_today'] }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">รับเรื่องทั้งหมด</p>
                            <p class="text-2xl font-semibold text-gray-900">{{ $stats['total_received'] }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Pending Approval Bookings -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-medium text-gray-900">รายการรอการอนุมัติ</h3>
                            <a href="{{ route('director.bookings', ['status' => 'received']) }}" class="text-sm text-indigo-600 hover:text-indigo-800">ดูทั้งหมด →</a>
                        </div>
                        @if($pendingBookings->isEmpty())
                            <p class="text-gray-500 text-sm">ไม่มีรายการที่รอการอนุมัติ</p>
                        @else
                            <div class="space-y-3">
                                @foreach($pendingBookings as $booking)
                                    <a href="{{ route('director.bookings.show', $booking) }}" class="block p-3 bg-blue-50 rounded-lg hover:bg-blue-100 transition">
                                        <div class="flex justify-between items-start">
                                            <div>
                                                <p class="font-medium text-gray-900">{{ $booking->user->name }}</p>
                                                <p class="text-sm text-gray-600">{{ $booking->pickup_location }} → {{ $booking->destination }}</p>
                                                <p class="text-xs text-gray-500 mt-1">
                                                    รับเรื่องโดย: {{ $booking->receiver->name ?? '-' }}
                                                </p>
                                            </div>
                                            <div class="text-right">
                                                <p class="text-sm font-medium text-gray-900">{{ $booking->start_date->format('d/m/Y') }}</p>
                                                <p class="text-xs text-gray-500">{{ $booking->seats_requested }} ที่นั่ง</p>
                                                @if($booking->van)
                                                    <p class="text-xs text-green-600">{{ $booking->van->license_plate }}</p>
                                                @endif
                                            </div>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Today's Trips -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-medium text-gray-900">การเดินทางวันนี้</h3>
                            <span class="text-sm text-gray-500">{{ now()->format('d/m/Y') }}</span>
                        </div>
                        @if($todayBookings->isEmpty())
                            <p class="text-gray-500 text-sm">ไม่มีการเดินทางวันนี้</p>
                        @else
                            <div class="space-y-3">
                                @foreach($todayBookings as $booking)
                                    <div class="p-3 bg-green-50 rounded-lg">
                                        <div class="flex justify-between items-start">
                                            <div>
                                                <p class="font-medium text-gray-900">
                                                    {{ $booking->start_time }} น. - {{ $booking->van->name ?? 'N/A' }}
                                                    @if($booking->van)
                                                        <span class="text-xs text-gray-500">({{ $booking->van->license_plate }})</span>
                                                    @endif
                                                </p>
                                                <p class="text-sm text-gray-600">{{ $booking->pickup_location }} → {{ $booking->destination }}</p>
                                                <p class="text-xs text-gray-500 mt-1">{{ $booking->user->name }} ({{ $booking->seats_requested }} คน)</p>
                                            </div>
                                            <span class="px-2 py-1 text-xs bg-green-100 text-green-800 rounded">อนุมัติแล้ว</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Quick Links -->
            <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                <a href="{{ route('director.bookings', ['status' => 'received']) }}" class="bg-white p-6 rounded-lg shadow-sm hover:shadow-md transition flex items-center">
                    <div class="p-3 bg-blue-100 rounded-full mr-4">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                    </div>
                    <div>
                        <h4 class="font-medium text-gray-900">รายการรอการอนุมัติ</h4>
                        <p class="text-sm text-gray-500">ดูและอนุมัติคำขอจอง</p>
                    </div>
                </a>

                <a href="{{ route('director.bookings') }}" class="bg-white p-6 rounded-lg shadow-sm hover:shadow-md transition flex items-center">
                    <div class="p-3 bg-green-100 rounded-full mr-4">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                        </svg>
                    </div>
                    <div>
                        <h4 class="font-medium text-gray-900">รายการจองทั้งหมด</h4>
                        <p class="text-sm text-gray-500">ดูประวัติรายการจอง</p>
                    </div>
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
