<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>ใบขออนุญาตใช้รถราชการ - {{ $booking->id }}</title>
    <style>
        @font-face {
            font-family: 'THSarabunNew';
            font-style: normal;
            font-weight: normal;
            src: url("{{ storage_path('fonts/THSarabunNew.ttf') }}") format('truetype');
        }
        @font-face {
            font-family: 'THSarabunNew';
            font-style: normal;
            font-weight: bold;
            src: url("{{ storage_path('fonts/THSarabunNew Bold.ttf') }}") format('truetype');
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        @page {
            margin: 1.5cm;
        }
        
        body {
            font-family: 'THSarabunNew', 'DejaVu Sans', sans-serif;
            font-size: 18px;
            line-height: 1;
            color: #333;
            margin-left: 40px;
            margin-right: 40px;
            margin-top: 20px;
            margin-bottom: 20px;
            padding: 0;
        }
        
        .header {
            text-align: center;
            border-bottom: 3px solid #4f46e5;
            padding-bottom: 5px;
            margin-bottom: 5px;
        }
        
        .header h1 {
            font-size: 28px;
            font-weight: bold;
            color: #1e1b4b;
            margin-bottom: 5px;
        }
        
        .header .subtitle {
            font-size: 18px;
            color: #6366f1;
            margin-bottom: 10px;
        }
        
        .header .print-date {
            font-size: 14px;
            color: #666;
        }
        
        .booking-id {
            text-align: right;
            font-size: 14px;
            color: #888;
            margin-bottom: 20px;
        }
        
        .status-badge {
            display: inline-block;
            padding: 8px 8px;
            border-radius: 25px;
            font-weight: bold;
            font-size: 18px;
            margin-bottom: 5px;
        }
        
        .status-pending { background-color: #fef3c7; color: #92400e; }
        .status-approved { background-color: #d1fae5; color: #065f46; }
        .status-rejected { background-color: #fee2e2; color: #991b1b; }
        .status-completed { background-color: #dbeafe; color: #1e40af; }
        
        .section {
            margin-bottom: 5px;
        }
        
        .section-title {
            font-size: 18px;
            font-weight: bold;
            color: #4f46e5;
            border-bottom: 2px solid #e5e7eb;
            padding-bottom: 8px;
            margin-bottom: 5px;
        }
        
        .info-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .info-table td {
            padding: 5px 5px;
            border: 1px solid #e5e7eb;
        }
        
        .info-table .label {
            background-color: #f3f4f6;
            font-weight: bold;
            width: 35%;
            color: #374151;
        }
        
        .info-table .value {
            background-color: #fff;
        }
        
        .passengers-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .passengers-table th {
            background-color: #4f46e5;
            color: white;
            padding: 12px 15px;
            text-align: left;
            font-weight: bold;
        }
        
        .passengers-table td {
            padding: 5px 5px;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .passengers-table tr:nth-child(even) {
            background-color: #f9fafb;
        }
        
        .purpose-box {
            background-color: #f3f4f6;
            padding: 15px;
            border-radius: 8px;
            border-left: 4px solid #4f46e5;
        }
        
        .notes-box {
            background-color: #fef3c7;
            padding: 15px;
            border-radius: 8px;
            border-left: 4px solid #f59e0b;
        }
        
        .footer {
            margin-top: 10px;
            padding-top: 20px;
            border-top: 2px solid #e5e7eb;
            text-align: center;
            font-size: 12px;
            color: #666;
        }
        
        .signature-section {
            margin-top: 50px;
            display: table;
            width: 100%;
        }
        
        .signature-box {
            display: table-cell;
            width: 50%;
            text-align: center;
            padding: 20px;
        }
        
        .signature-line {
            border-top: 1px solid #333;
            width: 200px;
            margin: 0 auto;
            margin-top: 60px;
            padding-top: 8px;
        }
</style>
</head>
<body>
    <!-- Header with Logo Area -->
    <table style="width: 100%; margin-bottom: 20px;">
        <tr>
            <td style="width: 15%; vertical-align: middle;">
                 <img src="{{ public_path('image/logorus.png') }}" style="width: 60px; height: auto;">
            </td>
            <td style="width: 45%; vertical-align: middle;">
                <div style="font-size: 24px; font-weight: bold; color: #1e40af;">ใบขออนุญาตใช้รถราชการ</div>
                <div style="font-size: 14px; color: #6366f1; margin-top: 5px;">มหาวิทยาลัยเทคโนโลยีราชมงคลสุวรรณภูมิ</div>
            </td>
            <td style="width: 25%; text-align: right;">
                <div style="font-size: 12px; color: #666;">เลขที่: <strong>#{{ str_pad($booking->id, 6, '0', STR_PAD_LEFT) }}</strong></div>
                <div style="font-size: 11px; color: #888; margin-top: 3px;">วันที่พิมพ์: {{ now()->format('d/m/Y H:i') }}</div>
            </td>
            <td style="width: 15%; text-align: right; vertical-align: middle;">
                @if(isset($qrCodeBase64) && $qrCodeBase64)
                <div style="text-align: center;">
                    <img src="{{ $qrCodeBase64 }}" style="width: 70px; height: 70px;">
                    <div style="font-size: 9px; color: #888; margin-top: 2px;">สแกนเพื่อตรวจสอบ</div>
                </div>
                @endif
            </td>
        </tr>
    </table>
    
    <div style="border-bottom: 3px solid #4f46e5; margin-bottom: 20px;"></div>
    
    <!-- Status Badge -->
    <table style="width: 100%; margin-bottom: 20px;">
        <tr>
            <td style="text-align: center;">
                <span class="status-badge status-{{ $booking->status }}">
                    @if($booking->status === 'pending') ⏳ @elseif($booking->status === 'approved') ✓ @elseif($booking->status === 'rejected') ✗ @else ✓ @endif
                    {{ $booking->status_text }}
                </span>
            </td>
        </tr>
    </table>
    
    <!-- Main Info Grid - 2 Columns -->
    <table style="width: 100%; margin-bottom: 20px; border-collapse: collapse;">
        <tr>
            <!-- Left Column: ข้อมูลผู้ขอ -->
            <td style="width: 48%; vertical-align: top; padding-right: 10px;">
                <div style="background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%); border: 1px solid #bae6fd; border-radius: 8px; padding: 15px;">
                    <div style="font-size: 14px; font-weight: bold; color: #0369a1; margin-bottom: 12px; border-bottom: 1px solid #7dd3fc; padding-bottom: 8px;">
                        ข้อมูลผู้ขอใช้รถ
                    </div>
                    <table style="width: 100%; font-size: 13px;">
                        <tr>
                            <td style="color: #666; padding: 4px 0; width: 40%;">ชื่อผู้ขอ:</td>
                            <td style="font-weight: bold; padding: 4px 0;">{{ $booking->user->name }}</td>
                        </tr>
                        <tr>
                            <td style="color: #666; padding: 4px 0;">หน่วยงาน:</td>
                            <td style="padding: 4px 0;">{{ \App\Models\Van::DEPARTMENT_LABELS[$booking->requested_department] ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td style="color: #666; padding: 4px 0;">วันที่ขอ:</td>
                            <td style="padding: 4px 0;">{{ $booking->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                    </table>
                </div>
            </td>
            
            <!-- Right Column: รถ & พขร -->
            <td style="width: 48%; vertical-align: top; padding-left: 10px;">
                <div style="background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%); border: 1px solid #bbf7d0; border-radius: 8px; padding: 15px;">
                    <div style="font-size: 14px; font-weight: bold; color: #15803d; margin-bottom: 12px; border-bottom: 1px solid #86efac; padding-bottom: 8px;">
                        รถและพนักงานขับรถ
                    </div>
                    <table style="width: 100%; font-size: 13px;">
                        <tr>
                            <td style="color: #666; padding: 4px 0; width: 40%;">รถที่ใช้:</td>
                            <td style="font-weight: bold; padding: 4px 0;">
                                @if($booking->van) {{ $booking->van->name }} @else <span style="color: #999;">รอมอบหมาย</span> @endif
                            </td>
                        </tr>
                        <tr>
                            <td style="color: #666; padding: 4px 0;">ทะเบียน:</td>
                            <td style="padding: 4px 0;">
                                @if($booking->van) {{ $booking->van->license_plate }} @else - @endif
                            </td>
                        </tr>
                        <tr>
                            <td style="color: #666; padding: 4px 0;">พนักงานขับรถ:</td>
                            <td style="padding: 4px 0;">
                                @if($booking->driver) {{ $booking->driver->name }} @else <span style="color: #999;">รอมอบหมาย</span> @endif
                            </td>
                        </tr>
                    </table>
                </div>
            </td>
        </tr>
    </table>
    
    <!-- Travel Details -->
    <div style="background: #fff; border: 2px solid #4f46e5; border-radius: 8px; padding: 15px; margin-bottom: 20px;">
        <div style="font-size: 14px; font-weight: bold; color: #4f46e5; margin-bottom: 15px;">
            รายละเอียดการเดินทาง
        </div>
        
        <table style="width: 100%; font-size: 13px; border-collapse: collapse;">
            <tr>
                <td style="width: 25%; background: #f3f4f6; padding: 10px; border: 1px solid #e5e7eb; font-weight: bold;">วันที่เริ่มต้น</td>
                <td style="width: 25%; padding: 10px; border: 1px solid #e5e7eb;">{{ $booking->start_date->format('d/m/Y') }} เวลา {{ $booking->start_time }} น.</td>
                <td style="width: 25%; background: #f3f4f6; padding: 10px; border: 1px solid #e5e7eb; font-weight: bold;">วันที่สิ้นสุด</td>
                <td style="width: 25%; padding: 10px; border: 1px solid #e5e7eb;">{{ $booking->end_date ? $booking->end_date->format('d/m/Y') : '-' }} เวลา {{ $booking->end_time }} น.</td>
            </tr>
            <tr>
                <td style="background: #f3f4f6; padding: 10px; border: 1px solid #e5e7eb; font-weight: bold;">สถานที่รอรถ</td>
                <td style="padding: 10px; border: 1px solid #e5e7eb;">{{ $booking->pickup_location }}</td>
                <td style="background: #f3f4f6; padding: 10px; border: 1px solid #e5e7eb; font-weight: bold;">ปลายทาง</td>
                <td style="padding: 10px; border: 1px solid #e5e7eb;">{{ $booking->destination }}</td>
            </tr>
            <tr>
                <td style="background: #f3f4f6; padding: 10px; border: 1px solid #e5e7eb; font-weight: bold;">จำนวนผู้โดยสาร</td>
                <td colspan="3" style="padding: 10px; border: 1px solid #e5e7eb;">{{ $booking->seats_requested }} คน</td>
            </tr>
        </table>
    </div>
    
    <!-- Purpose -->
    <div style="margin-bottom: 20px;">
        <div style="font-size: 14px; font-weight: bold; color: #374151; margin-bottom: 8px;">วัตถุประสงค์การเดินทาง</div>
        <div style="background: #fafafa; border: 1px solid #e5e7eb; border-left: 4px solid #4f46e5; padding: 12px; border-radius: 4px; font-size: 13px;">
            {{ $booking->purpose }}
        </div>
    </div>
    
    <!-- Passengers List -->
    @if($booking->passengers->count() > 0)
    <div style="margin-bottom: 20px;">
        <div style="font-size: 14px; font-weight: bold; color: #374151; margin-bottom: 8px;"> รายชื่อผู้โดยสาร ({{ $booking->passengers->count() }} คน)</div>
        <table style="width: 100%; border-collapse: collapse; font-size: 12px;">
            <thead>
                <tr>
                    <th style="background: #4f46e5; color: white; padding: 8px; text-align: center; width: 40px;">ลำดับ</th>
                    <th style="background: #4f46e5; color: white; padding: 8px; text-align: left;">ชื่อ-นามสกุล</th>
                    <th style="background: #4f46e5; color: white; padding: 8px; text-align: left;">หน่วยงาน</th>
                </tr>
            </thead>
            <tbody>
                @foreach($booking->passengers as $index => $passenger)
                <tr style="background: {{ $index % 2 == 0 ? '#fff' : '#f9fafb' }};">
                    <td style="padding: 8px; border-bottom: 1px solid #e5e7eb; text-align: center;">{{ $index + 1 }}</td>
                    <td style="padding: 8px; border-bottom: 1px solid #e5e7eb;">{{ $passenger->name }}</td>
                    <td style="padding: 8px; border-bottom: 1px solid #e5e7eb;">{{ $passenger->department ?? '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
    
    <!-- Admin Notes -->
    @if($booking->admin_notes)
    <div style="margin-bottom: 5px;">
        <div style="font-size: 14px; font-weight: bold; color: #374151; margin-bottom: 5px;">หมายเหตุจากผู้อนุมัติ</div>
        <div style="background: #fef3c7; border: 1px solid #fcd34d; border-left: 4px solid #f59e0b; padding: 12px; border-radius: 4px; font-size: 13px;">
            {{ $booking->admin_notes }}
            @if($booking->approver)
            <div style="margin-top: 10px; font-size: 11px; color: #666;">
                โดย {{ $booking->approver->name }} เมื่อ {{ $booking->approved_at->format('d/m/Y H:i') }} น.
            </div>
            @endif
        </div>
    </div>
    @endif
    
    <!-- Signature Section -->
    <div style="margin-top: 5px;">
        <table style="width: 100%;">
            <tr>
                <td style="width: 30%; text-align: center; padding: 20px;">
                    <div style="margin-top: 50px; border-top: 1px solid #333; width: 180px; margin-left: auto; margin-right: auto; padding-top: 8px;">
                       ผู้ขอใช้รถ
                    </div>
                    <div style="margin-top: 5px; font-size: 12px;">@if($booking->user){{ $booking->user->name }}@endif</div>
                    <div style="font-size: 11px; color: #666;">วันที่ {{ $booking->created_at->format('d/m/Y H:i:s') }}</div>
                </td>
                <td style="width: 40%; text-align: center; padding: 20px;">
                    <div style="margin-top: 50px; border-top: 1px solid #333; width: 180px; margin-left: auto; margin-right: auto; padding-top: 8px;">
                        ผู้อนุมัติ
                    </div>
                    <div style="margin-top: 5px; font-size: 12px;">@if($booking->approver){{ $booking->approver->name }}@else ................................................@endif</div>
                    <div style="font-size: 11px; color: #666;">วันที่ {{ $booking->approved_at ? $booking->approved_at->format('d/m/Y H:i:s') : '................................................' }}</div>
                </td>
                <td style="width: 30%; text-align: center; padding: 20px;">

                </td>
            </tr>
        </table>
    </div>
    
    <!-- Footer -->
    <div style="margin-top: 10px; padding-top: 5px; border-top: 1px solid #e5e7eb; text-align: center; font-size: 10px; color: #888;">
        เอกสารนี้พิมพ์จากระบบจองรถตู้ มหาวิทยาลัยเทคโนโลยีราชมงคลสุวรรณภูมิ
    </div>
</body>
</html>
