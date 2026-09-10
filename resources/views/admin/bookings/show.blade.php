<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center">
            <a href="{{ route('admin.bookings') }}" class="mr-4 text-gray-500 hover:text-gray-700">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                รายละเอียดการขอใช้รถราชการ
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
                            <!-- Download PDF Button -->
                            <a href="{{ route('bookings.pdf', $booking) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-100 text-indigo-700 rounded-lg hover:bg-indigo-200 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                ดาวน์โหลด PDF
                            </a>
                            
                            <span class="px-3 py-1 text-sm font-semibold rounded-full {{ $booking->status_badge }}">
                                {{ $booking->status_text }}
                            </span>
                            <span class="px-3 py-1 text-sm font-semibold rounded-full bg-gray-100 text-gray-900">
                                #{{ str_pad($booking->id, 6, '0', STR_PAD_LEFT) }}
                            </span>
                        </div>
                                              
                    </div>
 <div class="grid grid-cols gap-6 mb-6">
                            <div class="bg-yellow-50 rounded p-3">
                                <ul class="divide-y divide-gray-200">
                                    
                                    @if($booking->requested_department)
                                        <p class="text-md font-bold  text-gray-600 mt-1">
                                            <span class="font-bold">ขอใช้รถของหน่วยงาน:</span> 
                                            {{ \App\Models\Van::DEPARTMENT_LABELS[$booking->requested_department] ?? $booking->requested_department }}
                                        </p>
                                    @endif
                                   
                                </ul>
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
                                <div class="flex">
                                    <dt class="w-28 text-sm text-gray-500">ติดต่อ:</dt>
                                    <dd class="text-sm text-gray-900 font-medium">{{ $booking->contact ?? '-' }}</dd>
                                </div>
                            </dl>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-500 mb-3">วัตถุประสงค์</h4>
                            <p class="text-sm text-gray-700 bg-gray-50 p-3 rounded">{{ $booking->purpose }}</p>
                        </div>
                    </div>

                

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

    <!-- Conflicting Bookings (Vans already in use) -->
                    @if(isset($conflictingBookings) && $conflictingBookings->count() > 0)
                        <div class="mb-6 pb-6 border-b">
                            <h4 class="text-sm font-medium text-red-600 mb-3 flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                </svg>
                                รถที่มีการใช้งานในช่วงวันที่ขอ ({{ $conflictingBookings->count() }} รายการ)
                            </h4>
                            <div class="bg-red-50 rounded-lg p-3 border border-red-200">
                                <table class="min-w-full text-sm">
                                    <thead>
                                        <tr class="text-left text-red-800">
                                            <th class="pb-2 font-medium">รถ</th>
                                            <th class="pb-2 font-medium">ผู้ขอใช้</th>
                                            <th class="pb-2 font-medium">วันที่ใช้งาน</th>
                                            <th class="pb-2 font-medium">ที่นั่ง</th>
                                            <th class="pb-2 font-medium text-center">รายละเอียด</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-red-200">
                                        @foreach($conflictingBookings as $index => $conflict)
                                            <tr class="text-gray-700" x-data="{ showModal{{ $index }}: false }">
                                                <td class="py-2">
                                                    @if($conflict->van)
                                                        <span class="font-medium">{{ $conflict->van->name }}</span>
                                                        <br><span class="text-xs text-gray-500">{{ $conflict->van->license_plate }}</span>
                                                    @else
                                                        <span class="text-gray-400">-</span>
                                                    @endif
                                                </td>
                                                <td class="py-2">{{ $conflict->user->name ?? '-' }}</td>
                                                <td class="py-2">
                                                    {{ $conflict->start_date->format('d/m/Y') }} 
                                                    @if($conflict->end_date && $conflict->end_date != $conflict->start_date)
                                                        - {{ $conflict->end_date->format('d/m/Y') }}
                                                    @endif
                                                    @if($conflict->driver)
                                                        <br><span class="inline-flex items-center gap-1 text-xs bg-purple-100 text-purple-700 px-1.5 py-0.5 rounded mt-1">
                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                                            </svg> (พขร.)
                                                            {{ $conflict->driver->name }}
                                                        </span>
                                                    @endif
                                                </td>
                                                <td class="py-2">{{ $conflict->seats_requested }} ที่นั่ง</td>
                                                <td class="py-2 text-center">
                                                    <button @click="showModal{{ $index }} = true" 
                                                            class="inline-flex items-center gap-1 px-2 py-1 text-xs bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition-colors">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                        </svg>
                                                        ดู
                                                    </button>

                                                    <!-- Modal -->
                                                    <div x-show="showModal{{ $index }}" 
                                                         x-cloak
                                                         class="fixed inset-0 z-50 overflow-y-auto" 
                                                         aria-labelledby="modal-title" 
                                                         role="dialog" 
                                                         aria-modal="true">
                                                        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:p-0">
                                                            <!-- Background overlay -->
                                                            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" 
                                                                 @click="showModal{{ $index }} = false"></div>

                                                            <!-- Modal panel -->
                                                            <div class="relative inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full"
                                                                 @click.away="showModal{{ $index }} = false">
                                                                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                                                    <div class="sm:flex sm:items-start">
                                                                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 sm:mx-0 sm:h-10 sm:w-10">
                                                                            <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                                            </svg>
                                                                        </div>
                                                                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                                                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                                                                รายละเอียดการจอง
                                                                            </h3>
                                                                            <div class="mt-4 space-y-3">
                                                                                <div class="bg-gray-50 p-3 rounded-lg">
                                                                                    <p class="text-sm text-gray-500 mb-1">รถ</p>
                                                                                    <p class="text-sm font-medium text-gray-900">
                                                                                        {{ $conflict->van->name ?? '-' }} 
                                                                                        @if($conflict->van)
                                                                                            <span class="text-gray-500">({{ $conflict->van->license_plate }})</span>
                                                                                        @endif
                                                                                    </p>
                                                                                </div>
                                                                                <div class="bg-gray-50 p-3 rounded-lg">
                                                                                    <p class="text-sm text-gray-500 mb-1">ผู้ขอใช้</p>
                                                                                    <p class="text-sm font-medium text-gray-900">{{ $conflict->user->name ?? '-' }}</p>
                                                                                </div>
                                                                                <div class="bg-green-50 p-3 rounded-lg border border-green-200">
                                                                                    <p class="text-sm text-green-600 mb-1 font-medium">🚩 ต้นทาง (สถานที่รอรถ)</p>
                                                                                    <p class="text-sm font-medium text-gray-900">{{ $conflict->pickup_location }}</p>
                                                                                </div>
                                                                                <div class="bg-red-50 p-3 rounded-lg border border-red-200">
                                                                                    <p class="text-sm text-red-600 mb-1 font-medium">📍 ปลายทาง</p>
                                                                                    <p class="text-sm font-medium text-gray-900">{{ $conflict->destination }}</p>
                                                                                </div>
                                                                                <div class="bg-blue-50 p-3 rounded-lg border border-blue-200">
                                                                                    <p class="text-sm text-blue-600 mb-1 font-medium">📅 วันเวลาที่ใช้</p>
                                                                                    <p class="text-sm font-medium text-gray-900">
                                                                                        {{ $conflict->start_date->format('d/m/Y') }} เวลา {{ $conflict->start_time }} น.
                                                                                        <br>ถึง {{ $conflict->end_date ? $conflict->end_date->format('d/m/Y') : '-' }} เวลา {{ $conflict->end_time }} น.
                                                                                    </p>
                                                                                </div>
                                                                                <div class="bg-gray-50 p-3 rounded-lg">
                                                                                    <p class="text-sm text-gray-500 mb-1">วัตถุประสงค์</p>
                                                                                    <p class="text-sm text-gray-900">{{ $conflict->purpose }}</p>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                                                                    <button type="button" 
                                                                            @click="showModal{{ $index }} = false"
                                                                            class="w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:w-auto sm:text-sm">
                                                                        ปิด
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif

                    <!-- Approval Section (Only for pending) -->
                    @if($booking->status === 'pending')
                        <div class="border-t pt-6">
                            <h4 class="text-lg font-medium text-gray-900 mb-4">ดำเนินการ</h4>
                            
                            <div class="grid grid-cols-2 gap-6">
                                <!-- Receive Form -->
                                <div class="bg-blue-50 p-4 rounded-lg">
                                    <h5 class="font-medium text-blue-800 mb-3">รับเรื่องการจอง</h5>
                                    <form action="{{ route('admin.bookings.receive', $booking) }}" method="POST">
                                        @csrf
                                        <div class="mb-3">
                                            <label class="block text-sm font-medium text-gray-700 mb-1">เลือกรถ <span class="text-red-500">*</span></label>
                                            <select name="van_id" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                                <option value="">-- เลือกรถ --</option>
                                                @foreach($vans as $van)
                                                    @php
                                                        $available = $van->getAvailableSeatsForDateRange($booking->start_date, $booking->end_date);
                                                    @endphp
                                                    <option value="{{ $van->id }}" {{ $available < $booking->seats_requested ? 'disabled' : '' }}>
                                                        {{ $van->name }} ({{ $van->license_plate }}) - ว่าง {{ $available }}/{{ $van->capacity }} ที่นั่ง
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label class="block text-sm font-medium text-gray-700 mb-1">พนักงานขับรถ</label>
                                            <select name="driver_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                                <option value="">-- ไม่ระบุ --</option>
                                                @foreach($drivers as $driver)
                                                    <option value="{{ $driver->id }}">{{ $driver->name }}</option>
                                                @endforeach
                                            </select>
                                            @if($drivers->isEmpty())
                                                <p class="text-xs text-gray-500 mt-1">ยังไม่มีพนักงานขับรถในระบบ กรุณาเพิ่มในเมนูจัดการสิทธิ์</p>
                                            @endif
                                        </div>
                                        <div class="mb-3">
                                            <label class="block text-sm font-medium text-gray-700 mb-1">หมายเหตุ</label>
                                            <textarea name="admin_notes" rows="2" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" placeholder="หมายเหตุเพิ่มเติม (ถ้ามี)"></textarea>
                                        </div>
                                        <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-5 py-3 bg-orange-500 text-white rounded-xl font-semibold shadow-lg shadow-orange-500/30 hover:bg-orange-600 hover:shadow-orange-600/40 hover:-translate-y-0.5 transition-all duration-200">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                            </svg>
                                            รับเรื่อง
                                        </button>
                                    </form>
                                </div>

                                 <!-- Reject Form -->
                                 <div class="bg-red-50 p-4 rounded-lg">
                                     <h5 class="font-medium text-red-800 mb-3">รับเรื่อง(ไม่จัดรถ)</h5>
                                     <form action="{{ route('admin.bookings.reject', $booking) }}" method="POST">
                                         @csrf
                                         <div class="mb-3">
                                             <label class="block text-sm font-medium text-gray-700 mb-1">เหตุผล <span class="text-red-500">*</span></label>
                                             <textarea name="admin_notes" rows="4" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 text-sm" placeholder="ระบุเหตุผลที่รับเรื่อง(ไม่จัดรถ)"></textarea>
                                         </div>
                                         <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-5 py-3 bg-gradient-to-r from-red-500 to-rose-600 text-white rounded-xl font-semibold shadow-lg shadow-red-500/30 hover:from-red-600 hover:to-rose-700 hover:shadow-red-500/40 hover:-translate-y-0.5 transition-all duration-200" onclick="return confirm('ยืนยันการรับเรื่อง(ไม่จัดรถ)?')">
                                             <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                             </svg>
                                             รับเรื่อง(ไม่จัดรถ)
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
                                    <p class="text-sm"><span class="text-gray-500">โดย:</span> {{ $booking->approver->name }}</p>
                                    <p class="text-sm"><span class="text-gray-500">เมื่อ:</span> {{ $booking->approved_at->format('d/m/Y H:i') }}</p>
                                    @if($booking->van)
                                        <p class="text-sm"><span class="text-gray-500">รถ:</span> {{ $booking->van->name }} ({{ $booking->van->license_plate }})</p>
                                    @endif
                                    @if($booking->driver)
                                        <p class="text-sm"><span class="text-gray-500">พนักงานขับรถ:</span> {{ $booking->driver->name }}</p>
                                    @endif
                                    @if($booking->admin_notes)
                                        <p class="text-sm mt-2"><span class="text-gray-500">หมายเหตุ:</span> {{ $booking->admin_notes }}</p>
                                    @endif
                                </div>
                            </div>
                        @endif

                        {{-- Edit Assignment Section (for received/approved bookings) --}}
                        @if(in_array($booking->status, ['received', 'approved']))
                            <div class="border-t pt-6 mt-6" x-data="{ showEditForm: false }">
                                <div class="flex justify-between items-center mb-4">
                                    <h4 class="text-lg font-medium text-gray-900">การจัดรถและคนขับ</h4>
                                    <button type="button" @click="showEditForm = !showEditForm" class="inline-flex items-center gap-1 px-3 py-1.5 text-teal-700 bg-teal-50 rounded-lg font-medium hover:bg-teal-100 hover:text-teal-800 transition-colors duration-150">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                        <span x-text="showEditForm ? 'ยกเลิก' : 'แก้ไข'"></span>
                                    </button>
                                </div>

                                {{-- Current Assignment Info --}}
                                <div x-show="!showEditForm" class="bg-gray-50 p-4 rounded">
                                    @if($booking->van)
                                        <p class="text-sm"><span class="text-gray-500">รถ:</span> {{ $booking->van->name }} ({{ $booking->van->license_plate }})</p>
                                    @else
                                        <p class="text-sm"><span class="text-gray-500">รถ:</span> <span class="text-gray-400">ยังไม่ได้จัด</span></p>
                                    @endif
                                    @if($booking->driver)
                                        <p class="text-sm"><span class="text-gray-500">พนักงานขับรถ:</span> {{ $booking->driver->name }}</p>
                                    @else
                                        <p class="text-sm"><span class="text-gray-500">พนักงานขับรถ:</span> <span class="text-gray-400">ยังไม่ได้ระบุ</span></p>
                                    @endif
                                </div>

                                {{-- Edit Form --}}
                                <div x-show="showEditForm" x-cloak class="bg-teal-50 p-4 rounded-lg border border-teal-200">
                                    <form action="{{ route('admin.bookings.update-assignment', $booking) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1">เลือกรถ <span class="text-red-500">*</span></label>
                                                <select name="van_id" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500 text-sm">
                                                    <option value="">-- เลือกรถ --</option>
                                                    @foreach($vans as $van)
                                                        @php
                                                            $available = $van->getAvailableSeatsForDateRange($booking->start_date, $booking->end_date);
                                                        @endphp
                                                        <option value="{{ $van->id }}" {{ $booking->van_id == $van->id ? 'selected' : '' }} {{ $available < $booking->seats_requested && $booking->van_id != $van->id ? 'disabled' : '' }}>
                                                            {{ $van->name }} ({{ $van->license_plate }}) - ว่าง {{ $available }}/{{ $van->capacity }} ที่นั่ง
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1">พนักงานขับรถ</label>
                                                <select name="driver_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500 text-sm">
                                                    <option value="">-- ไม่ระบุ --</option>
                                                    @foreach($drivers as $driver)
                                                        <option value="{{ $driver->id }}" {{ $booking->driver_id == $driver->id ? 'selected' : '' }}>
                                                            {{ $driver->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="flex justify-end">
                                            <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 bg-teal-500 text-white rounded-xl font-semibold shadow-lg shadow-teal-500/30 hover:bg-teal-600 hover:shadow-teal-600/40 hover:-translate-y-0.5 transition-all duration-200">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                </svg>
                                                บันทึกการแก้ไข
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        @endif

                        <!-- Closing Section (Only for approved) -->
                        @if($booking->status === 'approved')
                            <div class="border-t pt-6 mt-6">
                                <h4 class="text-lg font-medium text-gray-900 mb-4">ปิดงานการเดินทาง</h4>
                                <div class="bg-indigo-50 p-6 rounded-xl border border-indigo-100">
                                    <form action="{{ route('admin.bookings.complete', $booking) }}" method="POST" x-data="{ start: '', end: '', total: '' }">
                                        @csrf
                                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                                            @php
                                                $latestMileage = null;
                                                if ($booking->van) {
                                                    $latestMileage = $booking->van->bookings()
                                                        ->where('status', 'completed')
                                                        ->whereNotNull('end_mileage')
                                                        ->where('id', '!=', $booking->id)
                                                        ->orderBy('end_date', 'desc')
                                                        ->orderBy('updated_at', 'desc')
                                                        ->value('end_mileage');
                                                }
                                            @endphp
                                            
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-2">เลขไมล์เริ่มต้น (กม.)</label>
                                                <input type="number" step="0.01" name="start_mileage" required x-model="start" @input="total = (end - start > 0 ? (end - start).toFixed(2) : '')" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition-colors" placeholder="0.00">
                                                @if($latestMileage)
                                                    <p class="mt-1 text-xs text-gray-500 flex items-center gap-1">
                                                        <span>ลอกเลขไมล์ล่าสุด:</span>
                                                        <button type="button" @click="start = {{ $latestMileage }}; total = (end !== '' && end - start > 0 ? (end - start).toFixed(2) : '')" class="text-indigo-600 hover:text-indigo-800 font-medium hover:underline transition-colors" title="คลิกเพื่อคัดลอกเลขไมล์">
                                                            {{ number_format($latestMileage, 2) }}
                                                        </button>
                                                    </p>
                                                @endif
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-2">เลขไมล์สิ้นสุด (กม.)</label>
                                                <input type="number" step="0.01" name="end_mileage" required x-model="end" @input="total = (end - start > 0 ? (end - start).toFixed(2) : '')" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition-colors" placeholder="0.00">
                                                <p x-show="start !== '' && end !== '' && Number(end) <= Number(start)" x-cloak class="mt-1 text-sm text-red-600">เลขไมล์สิ้นสุดต้องมากกว่าเริ่มต้น</p>
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-2">รวมระยะทาง (กม.)</label>
                                                <input type="number" step="0.01" name="total_distance" x-model="total" class="w-full bg-gray-100 rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition-colors" readonly placeholder="0.00">
                                            </div>
                                        </div>
                                        <div class="flex justify-end">
                                            <button type="submit" :disabled="start !== '' && end !== '' && Number(end) <= Number(start)" :class="{ 'opacity-50 cursor-not-allowed': start !== '' && end !== '' && Number(end) <= Number(start) }" class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-green-500 to-emerald-600 text-black rounded-xl font-semibold shadow-lg shadow-green-500/30 hover:from-green-600 hover:to-emerald-700 hover:shadow-green-500/40 hover:-translate-y-0.5 transition-all duration-200">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                </svg>
                                                บันทึกและปิดงาน
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        @elseif($booking->status === 'completed' && ($booking->start_mileage || $booking->end_mileage || $booking->total_distance))
                            <div class="border-t pt-6 mt-6">
                                <h4 class="text-sm font-medium text-gray-500 mb-3">ข้อมูลการใช้งานรถ (เลขไมล์)</h4>
                                <div class="bg-gray-50 p-4 rounded grid grid-cols-3 gap-4">
                                    <div class="text-center">
                                        <p class="text-xs text-gray-500 uppercase tracking-wide">ไมล์เริ่มต้น</p>
                                        <p class="mt-1 text-lg font-semibold text-gray-900">{{ number_format($booking->start_mileage, 2) }}</p>
                                    </div>
                                    <div class="text-center border-l border-r border-gray-200">
                                        <p class="text-xs text-gray-500 uppercase tracking-wide">ไมล์สิ้นสุด</p>
                                        <p class="mt-1 text-lg font-semibold text-gray-900">{{ number_format($booking->end_mileage, 2) }}</p>
                                    </div>
                                    <div class="text-center">
                                        <p class="text-xs text-gray-500 uppercase tracking-wide">รวมระยะทาง (กม.)</p>
                                        <p class="mt-1 text-lg font-bold text-indigo-600">{{ number_format($booking->total_distance, 2) }}</p>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
