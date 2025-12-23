<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            ขอจองรถตู้
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('bookings.store') }}" id="bookingForm">
                        @csrf

                        <!-- Travel Date -->
                        <div class="mb-6">
                            <label for="travel_date" class="block text-sm font-medium text-gray-700 mb-2">วันที่เดินทาง <span class="text-red-500">*</span></label>
                            <input type="date" name="travel_date" id="travel_date" 
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                value="{{ old('travel_date') }}" min="{{ date('Y-m-d') }}" required>
                            @error('travel_date')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Time -->
                        <div class="grid grid-cols-2 gap-4 mb-6">
                            <div>
                                <label for="departure_time" class="block text-sm font-medium text-gray-700 mb-2">เวลาออกเดินทาง <span class="text-red-500">*</span></label>
                                <input type="time" name="departure_time" id="departure_time" 
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    value="{{ old('departure_time') }}" required>
                                @error('departure_time')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="return_time" class="block text-sm font-medium text-gray-700 mb-2">เวลากลับ (ถ้ามี)</label>
                                <input type="time" name="return_time" id="return_time" 
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    value="{{ old('return_time') }}">
                            </div>
                        </div>

                        <!-- Seats -->
                        <div class="mb-6">
                            <label for="seats_requested" class="block text-sm font-medium text-gray-700 mb-2">จำนวนที่นั่ง <span class="text-red-500">*</span></label>
                            <input type="number" name="seats_requested" id="seats_requested" 
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                value="{{ old('seats_requested', 1) }}" min="1" max="15" required>
                            @error('seats_requested')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Origin & Destination -->
                        <div class="grid grid-cols-2 gap-4 mb-6">
                            <div>
                                <label for="origin" class="block text-sm font-medium text-gray-700 mb-2">ต้นทาง <span class="text-red-500">*</span></label>
                                <input type="text" name="origin" id="origin" 
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    value="{{ old('origin') }}" placeholder="เช่น สำนักงาน กทม." required>
                                @error('origin')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="destination" class="block text-sm font-medium text-gray-700 mb-2">ปลายทาง <span class="text-red-500">*</span></label>
                                <input type="text" name="destination" id="destination" 
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    value="{{ old('destination') }}" placeholder="เช่น ศูนย์ประชุมแห่งชาติสิริกิติ์" required>
                                @error('destination')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Purpose -->
                        <div class="mb-6">
                            <label for="purpose" class="block text-sm font-medium text-gray-700 mb-2">วัตถุประสงค์ <span class="text-red-500">*</span></label>
                            <textarea name="purpose" id="purpose" rows="3" 
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                placeholder="ระบุวัตถุประสงค์ในการเดินทาง" required>{{ old('purpose') }}</textarea>
                            @error('purpose')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Passengers Section -->
                        <div class="mb-6">
                            <div class="flex justify-between items-center mb-2">
                                <label class="block text-sm font-medium text-gray-700">รายชื่อผู้โดยสาร (ถ้ามี)</label>
                                <button type="button" id="addPassenger" class="text-sm text-indigo-600 hover:text-indigo-800">
                                    + เพิ่มผู้โดยสาร
                                </button>
                            </div>
                            <div id="passengersContainer" class="space-y-3">
                                <!-- Passenger rows will be added here -->
                            </div>
                        </div>

                        <!-- Submit -->
                        <div class="flex justify-end space-x-3">
                            <a href="{{ route('bookings.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                                ยกเลิก
                            </a>
                            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                ส่งคำขอจอง
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        let passengerCount = 0;
        
        document.getElementById('addPassenger').addEventListener('click', function() {
            const container = document.getElementById('passengersContainer');
            const row = document.createElement('div');
            row.className = 'flex space-x-2 items-center';
            row.innerHTML = `
                <input type="text" name="passengers[${passengerCount}][name]" placeholder="ชื่อ-นามสกุล" 
                    class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                <input type="text" name="passengers[${passengerCount}][department]" placeholder="หน่วยงาน" 
                    class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                <button type="button" onclick="this.parentElement.remove()" class="text-red-500 hover:text-red-700 px-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            `;
            container.appendChild(row);
            passengerCount++;
        });
    </script>
</x-app-layout>
