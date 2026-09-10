<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                รายงานการใช้รถราชการ
            </h2>
            <a href="{{ route('admin.reports.export', request()->query()) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 border border-transparent rounded-lg font-semibold text-sm text-white shadow-md hover:bg-emerald-700 hover:shadow-lg transition-all duration-150">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                ดาวน์โหลดรายงาน Excel
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Filters -->
            <div class="bg-white p-6 rounded-xl shadow-sm mb-6 border border-gray-100">
                <form method="GET" action="{{ route('admin.reports') }}" class="grid grid-cols-1 md:grid-cols-4 gap-6 items-end">
                    <div>
                        <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">ตั้งแต่วันที่</label>
                        <input type="date" name="start_date" id="start_date" value="{{ $startDate }}" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                    </div>
                    <div>
                        <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">ถึงวันที่</label>
                        <input type="date" name="end_date" id="end_date" value="{{ $endDate }}" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                    </div>
                    
                    @if(Auth::user()->isSuperAdmin())
                        <div>
                            <label for="department" class="block text-sm font-medium text-gray-700 mb-1">หน่วยงาน</label>
                            <select name="department" id="department" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                <option value="all" {{ request('department') === 'all' ? 'selected' : '' }}>ทั้งหมด</option>
                                @foreach(\App\Models\Van::DEPARTMENT_LABELS as $key => $label)
                                    <option value="{{ $key }}" {{ request('department') === $key ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    @else
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">หน่วยงาน</label>
                            <input type="text" readonly value="{{ \App\Models\Van::DEPARTMENT_LABELS[Auth::user()->getAdminDepartment()] ?? Auth::user()->getAdminDepartment() }}" class="w-full rounded-lg border-gray-200 bg-gray-50 text-gray-500 text-sm cursor-not-allowed">
                        </div>
                    @endif

                    <div class="flex gap-2">
                        <button type="submit" class="flex-grow px-4 py-2 bg-indigo-600 text-white font-medium rounded-lg text-sm hover:bg-indigo-700 transition shadow-sm">
                            กรองข้อมูล
                        </button>
                        <a href="{{ route('admin.reports') }}" class="px-4 py-2 text-gray-600 hover:text-gray-900 border border-gray-200 bg-white rounded-lg text-sm hover:bg-gray-50 transition">
                            ล้าง
                        </a>
                    </div>
                </form>
            </div>

            <!-- Stats Overview Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Total Bookings -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex items-center">
                    <div class="p-3 bg-blue-50 text-blue-600 rounded-lg">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">จำนวนคำขอทั้งหมด</p>
                        <p class="text-2xl font-bold text-gray-900">{{ number_format($totalBookings) }} ครั้ง</p>
                    </div>
                </div>

                <!-- Completed Bookings -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex items-center">
                    <div class="p-3 bg-purple-50 text-purple-600 rounded-lg">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">เสร็จสิ้นแล้ว</p>
                        <p class="text-2xl font-bold text-gray-900">{{ number_format($completedBookings) }} ครั้ง</p>
                    </div>
                </div>

                <!-- Total Distance -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex items-center">
                    <div class="p-3 bg-emerald-50 text-emerald-600 rounded-lg">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">ระยะทางสะสมรวม</p>
                        <p class="text-2xl font-bold text-gray-900">{{ number_format($totalDistance, 1) }} กม.</p>
                    </div>
                </div>

                <!-- Avg Distance -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex items-center">
                    <div class="p-3 bg-amber-50 text-amber-600 rounded-lg">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">เฉลี่ยต่อการเดินทาง</p>
                        <p class="text-2xl font-bold text-gray-900">{{ number_format($avgDistance, 1) }} กม.</p>
                    </div>
                </div>
            </div>

            <!-- Charts Section -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                <!-- Trips per Vehicle Chart -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 lg:col-span-2">
                    <h3 class="text-md font-semibold text-gray-800 mb-4">สถิติการใช้งานจำแนกตามรถตู้</h3>
                    <div class="h-80">
                        <canvas id="tripsChart"></canvas>
                    </div>
                </div>

                <!-- Status Distribution Chart -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-md font-semibold text-gray-800 mb-4">สถานะคำขอใช้รถตู้</h3>
                    <div class="h-80 flex items-center justify-center">
                        <canvas id="statusChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Vehicle Summary Table -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mb-8">
                <div class="px-6 py-4 bg-gray-50/50 border-b border-gray-100">
                    <h3 class="text-md font-semibold text-gray-800">สรุปการใช้งานรายคัน</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">ชื่อรถ</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">ทะเบียนรถ</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">หน่วยงานเจ้าของรถ</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">จำนวนทริป (ครั้ง)</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">ระยะทางสะสมรวม (กม.)</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @forelse($vanSummaries as $van)
                                <tr class="hover:bg-gray-50/55 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $van['name'] }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $van['license_plate'] }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ \App\Models\Van::DEPARTMENT_LABELS[$van['owner_department']] ?? $van['owner_department'] }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right font-semibold">
                                        {{ number_format($van['trips_count']) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right font-semibold text-indigo-600">
                                        {{ number_format($van['total_distance'], 1) }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-8 text-center text-sm text-gray-400">
                                        ไม่พบข้อมูลรถในหน่วยงาน
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Booking Requests Detailed Table -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 bg-gray-50/50 border-b border-gray-100">
                    <h3 class="text-md font-semibold text-gray-800">รายการคำขอทั้งหมดในรอบรายงาน</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">เลขคำขอ</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">ผู้ขอ/หน่วยงาน</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">วันที่เดินทาง</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">เส้นทาง</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">ยานพาหนะ/พนักงานขับ</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">เลขไมล์เริ่มต้น-สิ้นสุด</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">ระยะทาง (กม.)</th>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">สถานะ</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @forelse($bookings as $booking)
                                <tr class="hover:bg-gray-50/55 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-semibold">
                                        #{{ str_pad($booking->id, 6, '0', STR_PAD_LEFT) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $booking->user->name }}</div>
                                        <div class="text-xs text-gray-400">{{ \App\Models\Van::DEPARTMENT_LABELS[$booking->requested_department] ?? $booking->requested_department }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $booking->start_date->format('d/m/Y') }} {{ $booking->start_time }} น.
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600 max-w-xs truncate">
                                        {{ $booking->pickup_location }} → {{ $booking->destination }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($booking->van)
                                            <div class="text-sm text-gray-900">{{ $booking->van->name }}</div>
                                            <div class="text-xs text-gray-400">{{ $booking->van->license_plate }} / {{ $booking->driver->name ?? 'ไม่มีคนขับ' }}</div>
                                        @else
                                            <span class="text-xs text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">
                                        @if($booking->status === 'completed')
                                            {{ number_format($booking->start_mileage, 1) }} - {{ number_format($booking->end_mileage, 1) }}
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right font-semibold">
                                        @if($booking->status === 'completed')
                                            {{ number_format($booking->total_distance, 1) }}
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $booking->status_badge }}">
                                            {{ $booking->status_text }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-6 py-8 text-center text-sm text-gray-400">
                                        ไม่พบข้อมูลคำขอใช้รถในช่วงเวลาดังกล่าว
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Vehicle Trips & Distance Chart
            const ctxTrips = document.getElementById('tripsChart').getContext('2d');
            const tripsData = @json($chartVanTrips);
            const distanceData = @json($chartVanDistances);
            const labels = @json($chartVanNames);

            new Chart(ctxTrips, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'จำนวนทริป (ครั้ง)',
                            data: tripsData,
                            backgroundColor: 'rgba(99, 102, 241, 0.65)',
                            borderColor: 'rgb(99, 102, 241)',
                            borderWidth: 1,
                            yAxisID: 'yTrips',
                        },
                        {
                            label: 'ระยะทางรวม (กม.)',
                            data: distanceData,
                            backgroundColor: 'rgba(16, 185, 129, 0.65)',
                            borderColor: 'rgb(16, 185, 129)',
                            borderWidth: 1,
                            yAxisID: 'yDistance',
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        yTrips: {
                            type: 'linear',
                            position: 'left',
                            title: {
                                display: true,
                                text: 'จำนวนทริป (ครั้ง)'
                            },
                            grid: {
                                drawOnChartArea: false
                            },
                            ticks: {
                                precision: 0
                            }
                        },
                        yDistance: {
                            type: 'linear',
                            position: 'right',
                            title: {
                                display: true,
                                text: 'ระยะทางรวม (กม.)'
                            },
                            ticks: {
                                callback: function(value) {
                                    return value + ' กม.';
                                }
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            position: 'top',
                        }
                    }
                }
            });

            // Booking Status Distribution Chart
            const ctxStatus = document.getElementById('statusChart').getContext('2d');
            const statusCounts = [
                {{ $pendingBookings }},
                {{ $approvedBookings }},
                {{ $completedBookings }},
                {{ $rejectedBookings }}
            ];
            const statusLabels = ['รอรับเรื่อง/รออนุมัติ', 'อนุมัติแล้ว', 'เสร็จสิ้น', 'ปฏิเสธ/ไม่อนุมัติ'];
            const statusColors = [
                'rgba(245, 158, 11, 0.7)',  // amber
                'rgba(16, 185, 129, 0.7)',  // emerald
                'rgba(139, 92, 246, 0.7)',  // purple
                'rgba(239, 68, 68, 0.7)'    // red
            ];

            new Chart(ctxStatus, {
                type: 'doughnut',
                data: {
                    labels: statusLabels,
                    datasets: [{
                        data: statusCounts,
                        backgroundColor: statusColors,
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                        }
                    }
                }
            });
        });
    </script>
</x-app-layout>
