<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                จัดการคำขอใช้รถราชการ
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Filters -->
            <div class="bg-white p-4 rounded-lg shadow-sm mb-6">
                <form method="GET" class="flex flex-wrap gap-4 items-end">
                    <div class="flex-grow">
                        <label class="block text-sm font-medium text-gray-700 mb-1">ค้นหา</label>
                        <input type="text" id="searchInput" placeholder="พิมพ์เพื่อค้นหา..." class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">สถานะ</label>
                        <select name="status" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="all" {{ request('status') === 'all' ? 'selected' : '' }}>ทั้งหมด</option>
                            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>รออนุมัติ</option>
                            <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>อนุมัติแล้ว</option>
                            <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>ไม่อนุมัติ</option>
                            <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>เสร็จสิ้น</option>
                            <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>ยกเลิก</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">วันที่เดินทาง</label>
                        <input type="date" name="date" value="{{ request('date') }}" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                        กรอง
                    </button>
                    <a href="{{ route('admin.bookings') }}" class="px-4 py-2 text-gray-600 hover:text-gray-800">
                        ล้าง
                    </a>
                </form>
            </div>

            <!-- Bookings Table -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200" id="bookingsTable">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">เลขที่</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ผู้ขอ</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">วันที่/เวลา-เส้นทาง</th>
                                <!-- <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">เส้นทาง</th> -->
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ที่นั่ง</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">รถ/คนขับ</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">สถานะ</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">การดำเนินการ</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($bookings as $booking)
                                <tr class="booking-row {{ $booking->status === 'pending' ? 'bg-yellow-50' : '' }}">
                                      <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900">
                                                    #{{ str_pad($booking->id, 6, '0', STR_PAD_LEFT) }}
                                                </div>
                                            </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $booking->user->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $booking->user->email }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $booking->start_date->format('d/m/Y') }} {{ $booking->start_time }} น. ถึง {{ $booking->end_date ? $booking->end_date->format('d/m/Y') : '-' }} {{ $booking->end_time }} น.</div>
                                       
                                           <div class="text-sm text-gray-900">{{ Str::limit($booking->pickup_location, 50) }}</div>
                                        <div class="text-sm text-gray-500">→ {{ Str::limit($booking->destination, 50) }}</div>
                                    </td>
                                 
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $booking->seats_requested }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        @if($booking->van)
                                            <div class="text-gray-900">{{ $booking->van->name }}</div>
                                            <div class="text-gray-500 text-xs">{{ $booking->van->license_plate }}</div>
                                            <div class="text-gray-500 text-xs">{{ $booking->driver->name }}</div>   
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                   
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $booking->status_badge }}">
                                            {{ $booking->status_text }}
                                        </span>
                                    </td>
                                    
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        @if($booking->status === 'pending')
                                            <div class="flex items-center gap-2">
                                            <a href="{{ route('admin.bookings.show', $booking) }}" class="inline-flex items-center gap-0.5 px-2 py-1 text-xs bg-gradient-to-r from-amber-400 to-orange-500 text-white rounded-md font-medium shadow-sm hover:from-amber-500 hover:to-orange-600 transition-all duration-150">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                                                </svg>
                                                ตรวจสอบ
                                            </a>
                                            </div>
                                        @else
                                            <div class="flex items-center gap-2">
                                                <a href="{{ route('admin.bookings.show', $booking) }}" class="inline-flex items-center gap-0.5 px-2 py-1 text-xs text-indigo-600 bg-indigo-50 rounded-md font-medium hover:bg-indigo-100 hover:text-indigo-700 transition-colors duration-150">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                    </svg>
                                                    ดู
                                                </a>

                                                @if(in_array($booking->status, ['received', 'approved']))
                                                    {{-- Edit Assignment Button --}}
                                                    <div x-data="{ openEditModal: false }" class="inline-block">
                                                        <button type="button" @click="openEditModal = true" class="inline-flex items-center gap-0.5 px-2 py-1 text-xs bg-teal-500 text-white rounded-md font-medium shadow-sm hover:bg-teal-600 transition-all duration-150">
                                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                            </svg>
                                                            แก้ไขรถ
                                                        </button>

                                                        {{-- Edit Assignment Modal --}}
                                                        <div x-show="openEditModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="edit-modal-title" role="dialog" aria-modal="true">
                                                            <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                                                                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="openEditModal = false" aria-hidden="true"></div>
                                                                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                                                                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                                                                    <form action="{{ route('admin.bookings.update-assignment', $booking) }}" method="POST">
                                                                        @csrf
                                                                        @method('PUT')
                                                                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                                                            <div class="sm:flex sm:items-start">
                                                                                <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-teal-100 sm:mx-0 sm:h-10 sm:w-10">
                                                                                    <svg class="h-6 w-6 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                                                    </svg>
                                                                                </div>
                                                                                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                                                                    <h3 class="text-lg leading-6 font-medium text-gray-900 text-left" id="edit-modal-title">
                                                                                        แก้ไขการจัดรถ #{{ str_pad($booking->id, 6, '0', STR_PAD_LEFT) }}
                                                                                    </h3>
                                                                                    <p class="text-sm text-gray-500 mt-1 text-left">
                                                                                        {{ $booking->user->name }} — {{ $booking->start_date->format('d/m/Y') }} {{ $booking->start_time }} น.
                                                                                    </p>
                                                                                    <div class="mt-4 space-y-4 text-left">
                                                                                        <div>
                                                                                            <label class="block text-sm font-medium text-gray-700 mb-1">เลือกรถ <span class="text-red-500">*</span></label>
                                                                                            <select name="van_id" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500 text-sm">
                                                                                                <option value="">-- เลือกรถ --</option>
                                                                                                @foreach($vans->where('owner_department', $booking->requested_department) as $van)
                                                                                                    <option value="{{ $van->id }}" {{ $booking->van_id == $van->id ? 'selected' : '' }}>
                                                                                                        {{ $van->name }} ({{ $van->license_plate }}) - {{ $van->capacity }} ที่นั่ง
                                                                                                    </option>
                                                                                                @endforeach
                                                                                            </select>
                                                                                        </div>
                                                                                        <div>
                                                                                            <label class="block text-sm font-medium text-gray-700 mb-1">พนักงานขับรถ</label>
                                                                                            <select name="driver_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500 text-sm">
                                                                                                <option value="">-- ไม่ระบุ --</option>
                                                                                                @foreach($drivers->where('department', $booking->requested_department) as $driver)
                                                                                                    <option value="{{ $driver->id }}" {{ $booking->driver_id == $driver->id ? 'selected' : '' }}>
                                                                                                        {{ $driver->name }}
                                                                                                    </option>
                                                                                                @endforeach
                                                                                            </select>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                                                                            <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-600 sm:ml-3 sm:w-auto sm:text-sm">
                                                                                บันทึกการแก้ไข
                                                                            </button>
                                                                            <button type="button" @click="openEditModal = false" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                                                                ยกเลิก
                                                                            </button>
                                                                        </div>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif

                                                @if($booking->status === 'approved')
                                                    <div x-data="{ openCompleteModal: false }" class="inline-block">
                                                        <button type="button" @click="openCompleteModal = true" class="inline-flex items-center gap-0.5 px-2 py-1 text-xs bg-emerald-500 text-white rounded-md font-medium shadow-sm hover:bg-emerald-600 transition-all duration-150">
                                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                            </svg>
                                                            เสร็จสิ้น
                                                        </button>

                                                        <!-- Modal -->
                                                        <div x-show="openCompleteModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                                                            <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                                                                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="openCompleteModal = false" aria-hidden="true"></div>
                                                                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                                                                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                                                                    <form action="{{ route('admin.bookings.complete', $booking) }}" method="POST" x-data="{ start: '', end: '', total: '' }">
                                                                        @csrf
                                                                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                                                            <div class="sm:flex sm:items-start">
                                                                                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                                                                    <h3 class="text-lg leading-6 font-medium text-gray-900 text-left" id="modal-title">
                                                                                        ปิดงานการเดินทาง #{{ str_pad($booking->id, 6, '0', STR_PAD_LEFT) }}
                                                                                    </h3>
                                                                                    <div class="mt-4 space-y-4 text-left">
                                                                                        <div>
                                                                                            <label class="block text-sm font-medium text-gray-700">เลขไมล์เริ่มต้น</label>
                                                                                            <input type="number" step="0.01" name="start_mileage" required x-model="start" @input="total = (end - start > 0 ? (end - start).toFixed(2) : '')" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                                                                        </div>
                                                                                        <div>
                                                                                            <label class="block text-sm font-medium text-gray-700">เลขไมล์สิ้นสุด</label>
                                                                                            <input type="number" step="0.01" name="end_mileage" required x-model="end" @input="total = (end - start > 0 ? (end - start).toFixed(2) : '')" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                                                                            <p x-show="start !== '' && end !== '' && Number(end) <= Number(start)" x-cloak class="mt-1 text-sm text-red-600">เลขไมล์สิ้นสุดต้องมากกว่าเริ่มต้น</p>
                                                                                        </div>
                                                                                        <div>
                                                                                            <label class="block text-sm font-medium text-gray-700">รวมระยะทาง (กม.)</label>
                                                                                            <input type="number" step="0.01" name="total_distance" x-model="total" class="mt-1 bg-gray-50 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                                                                          
                                                                        <button type="submit" :disabled="start !== '' && end !== '' && Number(end) <= Number(start)" :class="{ 'opacity-50 cursor-not-allowed': start !== '' && end !== '' && Number(end) <= Number(start) }" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm">
                                                                                บันทึกและปิดงาน
                                                                            </button>
                                                                            <button type="button" @click="openCompleteModal = false" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                                                                ยกเลิก
                                                                            </button>
                                                                        </div>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        @endif

                                        {{-- Cancel Button - shown for pending, received, approved --}}
                                        @if(in_array($booking->status, ['pending', 'received', 'approved']))
                                            <div x-data="{ openCancelModal: false }" class="inline-block">
                                                <button type="button" @click="openCancelModal = true" class="inline-flex items-center gap-0.5 px-2 py-1 text-xs bg-red-500 text-white rounded-md font-medium shadow-sm hover:bg-red-600 transition-all duration-150">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                    </svg>
                                                    ยกเลิก
                                                </button>

                                                {{-- Cancel Modal --}}
                                                <div x-show="openCancelModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="cancel-modal-title" role="dialog" aria-modal="true">
                                                    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                                                        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="openCancelModal = false" aria-hidden="true"></div>
                                                        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                                                        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                                                            <form action="{{ route('admin.bookings.cancel', $booking) }}" method="POST">
                                                                @csrf
                                                                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                                                    <div class="sm:flex sm:items-start">
                                                                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                                                                            <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                                                            </svg>
                                                                        </div>
                                                                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                                                            <h3 class="text-lg leading-6 font-medium text-gray-900 text-left" id="cancel-modal-title">
                                                                                ยกเลิกคำขอ #{{ str_pad($booking->id, 6, '0', STR_PAD_LEFT) }}
                                                                            </h3>
                                                                            <p class="text-sm text-gray-500 mt-1 text-left">
                                                                                {{ $booking->user->name }} — {{ $booking->start_date->format('d/m/Y') }} {{ $booking->start_time }} น.
                                                                            </p>
                                                                            <div class="mt-4 space-y-4 text-left">
                                                                                <div>
                                                                                    <label class="block text-sm font-medium text-gray-700 mb-1">ชื่อผู้แจ้งยกเลิก <span class="text-red-500">*</span></label>
                                                                                    <input type="text" name="cancelled_by_name" required placeholder="กรอกชื่อผู้แจ้งยกเลิก" class="w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 text-sm">
                                                                                </div>
                                                                                <div>
                                                                                    <label class="block text-sm font-medium text-gray-700 mb-1">เหตุผลการยกเลิก <span class="text-red-500">*</span></label>
                                                                                    <textarea name="cancelled_reason" required rows="3" placeholder="กรอกเหตุผลการยกเลิก" class="w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 text-sm"></textarea>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                                                                    <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                                                                        ยืนยันยกเลิก
                                                                    </button>
                                                                    <button type="button" @click="openCancelModal = false" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                                                        ปิด
                                                                    </button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="px-6 py-12 text-center text-gray-500">
                                        ไม่พบข้อมูลการจอง
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="p-4">
                    {{ $bookings->withQueryString()->links() }}
                </div>
            </div>
        </div>
    </div>
    
    <script>
        document.getElementById('searchInput').addEventListener('keyup', function() {
            var input, filter, table, tr, td, i, txtValue;
            input = document.getElementById('searchInput');
            filter = input.value.toUpperCase();
            table = document.getElementById('bookingsTable');
            tr = table.getElementsByTagName('tr');
    
            for (i = 0; i < tr.length; i++) {
                // Skip header row
                if (tr[i].getElementsByTagName('th').length > 0) continue;
                
                var found = false;
                var tds = tr[i].getElementsByTagName('td');
                for (var j = 0; j < tds.length; j++) {
                    if (tds[j]) {
                        txtValue = tds[j].textContent || tds[j].innerText;
                        if (txtValue.toUpperCase().indexOf(filter) > -1) {
                            found = true;
                            break;
                        }
                    }
                }
                
                if (found) {
                    tr[i].style.display = "";
                } else {
                    tr[i].style.display = "none";
                }
            }
        });
    </script>
</x-app-layout>
