<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <!--[if gte mso 9]>
    <xml>
        <x:ExcelWorkbook>
            <x:ExcelWorksheets>
                <x:ExcelWorksheet>
                    <x:Name>รายงานการใช้รถ</x:Name>
                    <x:WorksheetOptions>
                        <x:DisplayGridlines/>
                    </x:WorksheetOptions>
                </x:ExcelWorksheet>
            </x:ExcelWorksheets>
        </x:ExcelWorkbook>
    </xml>
    <![endif]-->
    <style>
        body { font-family: 'Tahoma', 'Arial', sans-serif; }
        .title { font-size: 16pt; font-weight: bold; text-align: center; }
        .subtitle { font-size: 11pt; text-align: center; color: #555555; }
        .header-cell { background-color: #4F81BD; color: white; font-weight: bold; text-align: center; border: 0.5pt solid #999999; }
        .data-cell { border: 0.5pt solid #D9D9D9; text-align: left; }
        .data-cell-right { border: 0.5pt solid #D9D9D9; text-align: right; }
        .data-cell-center { border: 0.5pt solid #D9D9D9; text-align: center; }
        .section-header { font-size: 12pt; font-weight: bold; background-color: #DCE6F1; border: 0.5pt solid #999999; }
        .stats-label { font-weight: bold; background-color: #F2F2F2; border: 0.5pt solid #D9D9D9; }
        .stats-value { font-weight: bold; color: #1F497D; border: 0.5pt solid #D9D9D9; text-align: right; }
    </style>
</head>
<body>
    <table>
        <!-- Main Titles -->
        <tr>
            <td colspan="8" class="title">รายงานสรุปการใช้รถราชการ</td>
        </tr>
        <tr>
            <td colspan="8" class="subtitle">ระหว่างวันที่ {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} ถึง {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}</td>
        </tr>
        <tr>
            <td colspan="8" class="subtitle">พิมพ์เมื่อ: {{ now()->format('d/m/Y H:i') }} น.</td>
        </tr>
        <tr>
            <td colspan="8">&nbsp;</td>
        </tr>

        <!-- Summary Statistics -->
        <tr>
            <td colspan="8" class="section-header">ภาพรวมข้อมูลการเดินทาง</td>
        </tr>
        <tr>
            <td colspan="2" class="stats-label">จำนวนคำขอทั้งหมด:</td>
            <td colspan="2" class="stats-value">{{ number_format($totalBookings) }} ครั้ง</td>
            <td colspan="2" class="stats-label">เสร็จสิ้นแล้ว:</td>
            <td colspan="2" class="stats-value">{{ number_format($completedBookings) }} ครั้ง</td>
        </tr>
        <tr>
            <td colspan="2" class="stats-label">ระยะทางสะสมรวม:</td>
            <td colspan="2" class="stats-value">{{ number_format($totalDistance, 2) }} กม.</td>
            <td colspan="2" class="stats-label">เฉลี่ยต่อการเดินทาง:</td>
            <td colspan="2" class="stats-value">{{ number_format($completedBookings > 0 ? $totalDistance / $completedBookings : 0, 2) }} กม.</td>
        </tr>
        <tr>
            <td colspan="8">&nbsp;</td>
        </tr>

        <!-- Vehicle Summary -->
        <tr>
            <td colspan="8" class="section-header">สรุปการใช้งานรายคัน</td>
        </tr>
        <tr>
            <td colspan="2" class="header-cell">ชื่อรถ</td>
            <td colspan="2" class="header-cell">ทะเบียนรถ</td>
            <td colspan="2" class="header-cell">หน่วยงานเจ้าของรถ</td>
            <td class="header-cell">จำนวนทริป (ครั้ง)</td>
            <td class="header-cell">ระยะทางรวม (กม.)</td>
        </tr>
        @foreach($vanSummaries as $van)
        <tr>
            <td colspan="2" class="data-cell">{{ $van['name'] }}</td>
            <td colspan="2" class="data-cell-center">{{ $van['license_plate'] }}</td>
            <td colspan="2" class="data-cell">{{ \App\Models\Van::DEPARTMENT_LABELS[$van['owner_department']] ?? $van['owner_department'] }}</td>
            <td class="data-cell-right">{{ number_format($van['trips_count']) }}</td>
            <td class="data-cell-right">{{ number_format($van['total_distance'], 2) }}</td>
        </tr>
        @endforeach
        <tr>
            <td colspan="8">&nbsp;</td>
        </tr>

        <!-- Booking Details -->
        <tr>
            <td colspan="8" class="section-header">รายละเอียดรายการจองทั้งหมด</td>
        </tr>
        <tr>
            <td class="header-cell" style="width: 80px;">เลขคำขอ</td>
            <td class="header-cell" style="width: 150px;">ผู้ขอ</td>
            <td class="header-cell" style="width: 120px;">หน่วยงานที่ขอ</td>
            <td class="header-cell" style="width: 140px;">วันที่/เวลา เริ่มต้น</td>
            <td class="header-cell" style="width: 140px;">วันที่/เวลา สิ้นสุด</td>
            <td class="header-cell" style="width: 250px;">เส้นทาง (ต้นทาง -> ปลายทาง)</td>
            <td class="header-cell" style="width: 120px;">รถราชการ</td>
            <td class="header-cell" style="width: 100px;">ทะเบียนรถ</td>
            <td class="header-cell" style="width: 120px;">พนักงานขับรถ</td>
            <td class="header-cell" style="width: 90px;">ไมล์เริ่มต้น</td>
            <td class="header-cell" style="width: 90px;">ไมล์สิ้นสุด</td>
            <td class="header-cell" style="width: 90px;">ระยะทาง (กม.)</td>
            <td class="header-cell" style="width: 100px;">สถานะ</td>
        </tr>
        @forelse($bookings as $booking)
        <tr>
            <td class="data-cell-center">#{{ str_pad($booking->id, 6, '0', STR_PAD_LEFT) }}</td>
            <td class="data-cell">{{ $booking->user->name }}</td>
            <td class="data-cell">{{ \App\Models\Van::DEPARTMENT_LABELS[$booking->requested_department] ?? $booking->requested_department }}</td>
            <td class="data-cell-center">{{ $booking->start_date->format('d/m/Y') }} {{ $booking->start_time }} น.</td>
            <td class="data-cell-center">{{ $booking->end_date ? $booking->end_date->format('d/m/Y') : '-' }} {{ $booking->end_time }} น.</td>
            <td class="data-cell">{{ $booking->pickup_location }} → {{ $booking->destination }}</td>
            <td class="data-cell">{{ $booking->van->name ?? '-' }}</td>
            <td class="data-cell-center">{{ $booking->van->license_plate ?? '-' }}</td>
            <td class="data-cell">{{ $booking->driver->name ?? '-' }}</td>
            <td class="data-cell-right">
                @if($booking->status === 'completed')
                    {{ number_format($booking->start_mileage, 2) }}
                @else
                    -
                @endif
            </td>
            <td class="data-cell-right">
                @if($booking->status === 'completed')
                    {{ number_format($booking->end_mileage, 2) }}
                @else
                    -
                @endif
            </td>
            <td class="data-cell-right">
                @if($booking->status === 'completed')
                    {{ number_format($booking->total_distance, 2) }}
                @else
                    -
                @endif
            </td>
            <td class="data-cell-center">{{ $booking->status_text }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="13" class="data-cell-center" style="color: #999999; height: 50px;">ไม่พบข้อมูลรายการจอง</td>
        </tr>
        @endforelse
    </table>
</body>
</html>
