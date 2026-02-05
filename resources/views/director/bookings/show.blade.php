<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center">
            <a href="{{ route('director.bookings') }}" class="mr-4 text-gray-500 hover:text-gray-700">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                รายละเอียดคำขอจอง
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            @if(session('error'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                    {{ session('error') }}
                </div>
            @endif
            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                    {{ session('success') }}
                </div>
            @endif
                    
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <!-- Header with Status -->
                    <div class="flex justify-between items-start mb-6 pb-6 border-b">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">{{ $booking->user->name }}</h3>
                            <p class="text-sm text-gray-500">{{ $booking->user->email }}</p>
                            <p class="text-xs text-gray-400 mt-1">สร้างเมื่อ {{ $booking->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="px-3 py-1 text-sm font-semibold rounded-full {{ $booking->status_badge }}">
                                {{ $booking->status_text }}
                            </span>
                            <span class="px-3 py-1 text-sm font-semibold rounded-full bg-gray-100 text-gray-900">
                                #{{ str_pad($booking->id, 6, '0', STR_PAD_LEFT) }}
                            </span>
                        </div>
                    </div>

                    <!-- Department Info -->
                    <div class="grid grid-cols gap-6 mb-6">
                        <div class="bg-yellow-50 rounded p-3">
                            <p class="text-md font-bold text-gray-600">
                                <span class="font-bold">ขอใช้รถของหน่วยงาน:</span> 
                                {{ \App\Models\Van::DEPARTMENT_LABELS[$booking->requested_department] ?? $booking->requested_department }}
                            </p>
                        </div>
                    </div>

                    <!-- Booking Details -->
                    <div class="grid grid-cols-2 gap-6 mb-6">
                        <div>
                            <h4 class="text-sm font-medium text-gray-500 mb-3">ข้อมูลการเดินทาง</h4>
                            <dl class="space-y-2">
                                <div class="flex">
                                    <dt class="w-28 text-sm text-gray-500">ตั้งแต่:</dt>
                                    <dd class="text-sm text-gray-900 font-medium">{{ $booking->start_date->format('d/m/Y') }} เวลา {{ $booking->start_time }} น.</dd>
                                </div>
                                <div class="flex">
                                    <dt class="w-28 text-sm text-gray-500">ถึง:</dt>
                                    <dd class="text-sm text-gray-900">
                                        {{ $booking->end_date ? $booking->end_date->format('d/m/Y') : '-' }} เวลา {{ $booking->end_time }} น.
                                    </dd>
                                </div>
                                <div class="flex">
                                    <dt class="w-28 text-sm text-gray-500">สถานที่รอรถ:</dt>
                                    <dd class="text-sm text-gray-900">{{ $booking->pickup_location }}</dd>
                                </div>
                                <div class="flex">
                                    <dt class="w-28 text-sm text-gray-500">ปลายทาง:</dt>
                                    <dd class="text-sm text-gray-900">{{ $booking->destination }}</dd>
                                </div>
                                <div class="flex">
                                    <dt class="w-28 text-sm text-gray-500">ที่นั่ง:</dt>
                                    <dd class="text-sm text-gray-900 font-medium">{{ $booking->seats_requested }} ที่นั่ง</dd>
                                </div>
                            </dl>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-500 mb-3">วัตถุประสงค์</h4>
                            <p class="text-sm text-gray-700 bg-gray-50 p-3 rounded">{{ $booking->purpose }}</p>
                        </div>
                    </div>

                    <!-- Van & Driver Assignment -->
                    @if($booking->van || $booking->driver)
                    <div class="mb-6 pb-6 border-b">
                        <h4 class="text-sm font-medium text-gray-500 mb-3">ข้อมูลรถและพนักงานขับรถ</h4>
                        <div class="bg-blue-50 p-4 rounded">
                            @if($booking->van)
                                <p class="text-sm"><span class="text-gray-500">รถ:</span> <span class="font-medium">{{ $booking->van->name }} ({{ $booking->van->license_plate }})</span></p>
                            @endif
                            @if($booking->driver)
                                <p class="text-sm"><span class="text-gray-500">พนักงานขับรถ:</span> <span class="font-medium">{{ $booking->driver->name }}</span></p>
                            @endif
                        </div>
                    </div>
                    @endif

                    <!-- Passengers -->
                    @if($booking->passengers->count() > 0)
                        <div class="mb-6 pb-6 border-b">
                            <h4 class="text-sm font-medium text-gray-500 mb-3">รายชื่อผู้โดยสาร ({{ $booking->passengers->count() }} คน)</h4>
                            <div class="bg-gray-50 rounded p-3">
                                <ul class="divide-y divide-gray-200">
                                    @foreach($booking->passengers as $passenger)
                                        <li class="py-2 flex justify-between text-sm">
                                            <span>{{ $passenger->name }}</span>
                                            <span class="text-gray-500">{{ $passenger->department ?? '-' }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @endif

                    <!-- Receiver Info -->
                    @if($booking->receiver)
                        <div class="mb-6 pb-6 border-b">
                            <h4 class="text-sm font-medium text-gray-500 mb-3">ข้อมูลการรับเรื่อง</h4>
                            <div class="bg-blue-50 p-4 rounded">
                                <p class="text-sm"><span class="text-gray-500">รับเรื่องโดย:</span> {{ $booking->receiver->name }}</p>
                                <p class="text-sm"><span class="text-gray-500">เมื่อ:</span> {{ $booking->received_at->format('d/m/Y H:i') }}</p>
                                @if($booking->admin_notes)
                                    <p class="text-sm mt-2"><span class="text-gray-500">หมายเหตุ:</span> {{ $booking->admin_notes }}</p>
                                @endif
                            </div>
                        </div>
                    @endif

                    <!-- Director Approval Section -->
                    @if($booking->status === 'received')
                        <div class="border-t pt-6">
                            <h4 class="text-lg font-medium text-gray-900 mb-4">ดำเนินการอนุมัติ</h4>
                            
                            <div class="grid grid-cols-2 gap-6">
                                <!-- Approve Form -->
                                <div class="bg-green-50 p-4 rounded-lg">
                                    <h5 class="font-medium text-green-800 mb-3">อนุมัติการจอง</h5>
                                    <form action="{{ route('director.bookings.approve', $booking) }}" method="POST">
                                        @csrf
                                        <div class="mb-3">
                                            <label class="block text-sm font-medium text-gray-700 mb-1">หมายเหตุ</label>
                                            <textarea name="director_notes" rows="2" class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 text-sm" placeholder="หมายเหตุเพิ่มเติม (ถ้ามี)"></textarea>
                                        </div>
                                        <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-5 py-3 bg-gradient-to-r from-emerald-500 to-green-600 text-white rounded-xl font-semibold shadow-lg shadow-emerald-500/30 hover:from-emerald-600 hover:to-green-700 hover:shadow-emerald-500/40 hover:-translate-y-0.5 transition-all duration-200">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                            อนุมัติ
                                        </button>
                                    </form>
                                </div>

                                <!-- Reject Form -->
                                <div class="bg-red-50 p-4 rounded-lg">
                                    <h5 class="font-medium text-red-800 mb-3">ไม่อนุมัติ</h5>
                                    <form action="{{ route('director.bookings.reject', $booking) }}" method="POST">
                                        @csrf
                                        <div class="mb-3">
                                            <label class="block text-sm font-medium text-gray-700 mb-1">เหตุผล <span class="text-red-500">*</span></label>
                                            <textarea name="director_notes" rows="4" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 text-sm" placeholder="ระบุเหตุผลที่ไม่อนุมัติ"></textarea>
                                        </div>
                                        <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-5 py-3 bg-gradient-to-r from-red-500 to-rose-600 text-white rounded-xl font-semibold shadow-lg shadow-red-500/30 hover:from-red-600 hover:to-rose-700 hover:shadow-red-500/40 hover:-translate-y-0.5 transition-all duration-200" onclick="return confirm('ยืนยันการไม่อนุมัติ?')">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                            ไม่อนุมัติ
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @else
                        <!-- Show approval info -->
                        @if($booking->approver)
                            <div class="border-t pt-6">
                                <h4 class="text-sm font-medium text-gray-500 mb-3">ข้อมูลการอนุมัติ</h4>
                                <div class="bg-gray-50 p-4 rounded">
                                    <p class="text-sm"><span class="text-gray-500">อนุมัติโดย:</span> {{ $booking->approver->name }}</p>
                                    <p class="text-sm"><span class="text-gray-500">เมื่อ:</span> {{ $booking->approved_at->format('d/m/Y H:i') }}</p>
                                </div>
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
