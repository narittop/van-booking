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
            font-size: 22px;
            line-height: 1.0;
            color: #000000;
            margin-left: 40px;
            margin-right: 40px;
            margin-top: 15px;
            margin-bottom: 15px;
            padding: 0;
        }
        
        .header {
            text-align: center;
            border-bottom: 3px solid #4f46e5;
            padding-bottom: 5px;
            margin-bottom: 5px;
        }
        
        .header h1 {
            font-size: 32px;
            font-weight: bold;
            color: #1e1b4b;
            margin-bottom: 5px;
        }
        
        .header .subtitle {
            font-size: 22px;
            color: #6366f1;
            margin-bottom: 10px;
        }
        
        .header .print-date {
            font-size: 18px;
            color: #111111;
        }
        
        .booking-id {
            text-align: right;
            font-size: 18px;
            color: #222222;
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
            font-size: 22px;
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
            padding: 4px 8px;
            border: 1px solid #cccccc;
            font-size: 18px;
        }
        
        .info-table .label {
            background-color: #f3f4f6;
            font-weight: bold;
            width: 35%;
            color: #000000;
        }
        
        .info-table .value {
            background-color: #fff;
            color: #000000;
        }
        
        .passengers-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .passengers-table th {
            background-color: #4f46e5;
            color: white;
            padding: 6px 10px;
            text-align: left;
            font-weight: bold;
            font-size: 18px;
        }
        
        .passengers-table td {
            padding: 4px 10px;
            border-bottom: 1px solid #cccccc;
            font-size: 18px;
            color: #000000;
        }
        
        .passengers-table tr:nth-child(even) {
            background-color: #f9fafb;
        }
        
        .purpose-box {
            background-color: #f3f4f6;
            padding: 10px 15px;
            border-radius: 8px;
            border-left: 4px solid #4f46e5;
            font-size: 18px;
            color: #000000;
        }
        
        .notes-box {
            background-color: #fef3c7;
            padding: 10px 15px;
            border-radius: 8px;
            border-left: 4px solid #f59e0b;
            font-size: 18px;
            color: #000000;
        }
        
        .footer {
            margin-top: 10px;
            padding-top: 20px;
            border-top: 2px solid #e5e7eb;
            text-align: center;
            font-size: 16px;
            color: #333333;
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
            border-top: 1px solid #000000;
            width: 200px;
            margin: 0 auto;
            margin-top: 60px;
            padding-top: 8px;
        }
</style>
</head>
<body>
    <!-- Header with Logo Area -->
    <table style="width: 100%; margin-bottom: 12px;">
        <tr>
            <td style="width: 15%; vertical-align: middle;">
                 <img src="{{ public_path('image/logorus.png') }}" style="width: 60px; height: auto;">
            </td>
            <td style="width: 50%; vertical-align: middle;">
                <div style="font-size: 28px; font-weight: bold; color: #1e3a8a;">ใบขออนุญาตใช้รถราชการ</div>
                <div style="font-size: 18px; font-weight: bold; color: #4338ca; margin-top: 5px;">มหาวิทยาลัยเทคโนโลยีราชมงคลสุวรรณภูมิ</div>
            </td>
            <td style="width: 20%; text-align: right; vertical-align: middle;">
                <div style="font-size: 16px; color: #000000; font-weight: bold;">เลขที่: <strong>#{{ str_pad($booking->id, 6, '0', STR_PAD_LEFT) }}</strong></div>
                <div style="font-size: 15px; color: #111111; margin-top: 3px; font-weight: bold;">วันที่พิมพ์: {{ now()->format('d/m/Y H:i') }}</div>
                
                    <span class="status-badge status-{{ $booking->status }}">
                    @if($booking->status === 'pending') ⏳ @elseif($booking->status === 'approved') ✓ @elseif($booking->status === 'rejected') ✗ @else ✓ @endif
                    {{ $booking->status_text }}
                </span> 
            </td>
            <td style="width: 15%; text-align: right; vertical-align: middle;">
                @if(isset($qrCodeBase64) && $qrCodeBase64)
                <div style="text-align: center;">
                    <img src="{{ $qrCodeBase64 }}" style="width: 70px; height: 70px;">
                    <div style="font-size: 12px; color: #111111; font-weight: bold; margin-top: 2px;">สแกนเพื่อตรวจสอบ</div>
                </div>
                @endif
            </td>
        </tr>
    </table>
    
    <div style="border-bottom: 3px solid #4f46e5; margin-bottom: 12px;"></div>
    
    <!-- Status Badge -->
    <!-- <table style="width: 100%; margin-bottom: 12px;">
        <tr>
            <td style="text-align: center;">
              
            </td>
        </tr>
    </table> -->
    
    <!-- Main Info Grid - 2 Columns -->
    <table style="width: 100%; margin-bottom: 12px; border-collapse: collapse;">
        <tr>
            <!-- Left Column: ข้อมูลผู้ขอ -->
            <td style="width: 48%; vertical-align: top; padding-right: 10px;">
                <div style="background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%); border: 1px solid #bae6fd; border-radius: 8px; padding: 15px;">
                    <div style="font-size: 18px; font-weight: bold; color: #0369a1; margin-bottom: 12px; border-bottom: 1px solid #7dd3fc; padding-bottom: 8px;">
                        ข้อมูลผู้ขอใช้รถ
                    </div>
                    <table style="width: 100%; font-size: 17px; color: #000000;">
                        <tr>
                            <td style="color: #000000; font-weight: bold; padding: 6px 0; width: 35%;">ชื่อผู้ขอ:</td>
                            <td style="font-weight: bold; padding: 6px 0;">{{ $booking->user->name }}</td>
                        </tr>
                        <tr>
                            <td style="color: #000000; font-weight: bold; padding: 6px 0;">หน่วยงาน:</td>
                            <td style="font-weight: bold; padding: 6px 0;">{{ $booking->user->hrdPerson?->faculty_name_th ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td style="color: #000000; font-weight: bold; padding: 6px 0;">วันที่ขอ:</td>
                            <td style="font-weight: bold; padding: 6px 0;">{{ $booking->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                    </table>
                </div>
            </td>
            
            <!-- Right Column: รถ & พขร -->
            <td style="width: 48%; vertical-align: top; padding-left: 10px;">
                <div style="background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%); border: 1px solid #bbf7d0; border-radius: 8px; padding: 15px;">
                    <div style="font-size: 18px; font-weight: bold; color: #15803d; margin-bottom: 12px; border-bottom: 1px solid #86efac; padding-bottom: 8px;">
                        รถและพนักงานขับรถ
                    </div>
                    <table style="width: 100%; font-size: 17px; color: #000000;">
                        <tr>
                            <td style="color: #000000; font-weight: bold; padding: 6px 0; width: 25%;">รถที่ใช้:</td>
                            <td style="font-weight: bold; padding: 6px 0; width: 50%;">
                                @if($booking->van) {{ $booking->van->name }} @else <span style="color: #444444; font-weight: bold;">รอมอบหมาย</span> @endif
                            </td>
                            <td style="font-weight: bold; padding: 6px 0; width: 25%;">
                               <span style="color: #111111;">เลขไมล์เริ่มต้น.............................</span> 
                            </td>
                        </tr>
                        <tr>
                            <td style="color: #000000; font-weight: bold; padding: 6px 0;">ทะเบียน:</td>
                            <td style="font-weight: bold; padding: 6px 0;">
                                @if($booking->van) {{ $booking->van->license_plate }} @else - @endif
                            </td>
                            <td style="font-weight: bold; padding: 6px 0;">
                               <span style="color: #111111;">เลขไมล์สิ้นสุด.............................</span> 
                            </td>
                        </tr>
                        <tr>
                            <td style="color: #000000; font-weight: bold; padding: 6px 0;">พนักงานขับรถ:</td>
                            <td colspan="2" style="font-weight: bold; padding: 6px 0;">
                                @if($booking->driver)
                                    {{ $booking->driver->name }} @if($booking->driver->phone) (โทร. {{ $booking->driver->phone }}) @endif
                                @else
                                    <span style="color: #444444; font-weight: bold;">รอมอบหมาย</span>
                                @endif
                            </td>
                           
                        </tr>
                    </table>
                </div>
            </td>
        </tr>
    </table>
    
    <!-- Travel Details -->
    <div style="background: #fff; border: 2.5px solid #4f46e5; border-radius: 8px; padding: 10px 12px; margin-bottom: 12px;">
        <div style="font-size: 18px; font-weight: bold; color: #4f46e5; margin-bottom: 12px;">
            รายละเอียดการเดินทาง
        </div>
        
        <table style="width: 100%; font-size: 17px; color: #000000; border-collapse: collapse;">
            <tr>
                <td style="width: 18%; background: #f3f4f6; padding: 10px; border: 1px solid #cccccc; font-weight: bold;">วันที่เริ่มต้น</td>
                <td style="width: 32%; padding: 10px; border: 1px solid #cccccc; font-weight: bold;">{{ $booking->start_date->format('d/m/Y') }} เวลา {{ $booking->start_time }} น.</td>
                <td style="width: 15%; background: #f3f4f6; padding: 10px; border: 1px solid #cccccc; font-weight: bold;">วันที่สิ้นสุด</td>
                <td style="width: 33%; padding: 10px; border: 1px solid #cccccc; font-weight: bold;">{{ $booking->end_date ? $booking->end_date->format('d/m/Y') : '-' }} เวลา {{ $booking->end_time }} น.</td>
            </tr>
            <tr>
                <td style="width: 18%; background: #f3f4f6; padding: 10px; border: 1px solid #cccccc; font-weight: bold;">สถานที่รอรถ</td>
                <td style="width: 32%; padding: 10px; border: 1px solid #cccccc; font-weight: bold;">{{ $booking->pickup_location }}</td>
                <td style="width: 15%; background: #f3f4f6; padding: 10px; border: 1px solid #cccccc; font-weight: bold;">ปลายทาง</td>
                <td style="width: 35%; padding: 10px; border: 1px solid #cccccc; font-weight: bold;">{{ $booking->destination }}</td>
            </tr>
    
            <tr>
                <td style="background: #f3f4f6; padding: 10px; border: 1px solid #cccccc; font-weight: bold;">ช่องทางการติดต่อ</td>
                <td colspan="3" style="padding: 10px; border: 1px solid #cccccc; font-weight: bold;">{{ $booking->contact ?? '-' }}</td>
            </tr>
    
            <!-- <tr>
                <td style="background: #f3f4f6; padding: 10px; border: 1px solid #cccccc; font-weight: bold;">จำนวนผู้โดยสาร</td>
                <td colspan="3" style="padding: 10px; border: 1px solid #cccccc; font-weight: bold;">{{ $booking->seats_requested }} คน</td>
            </tr> -->
        </table>
    </div>
    
    <!-- Purpose -->
    <div style="margin-bottom: 12px;">
        <div style="font-size: 18px; font-weight: bold; color: #000000; margin-bottom: 8px;">วัตถุประสงค์การเดินทาง</div>
        <div style="background: #fafafa; border: 1px solid #cccccc; border-left: 4px solid #4f46e5; padding: 12px; border-radius: 4px; font-size: 17px; font-weight: bold; color: #000000;">
            {{ $booking->purpose }}
        </div>
    </div>
    
    <!-- Passengers List -->
    @if($booking->passengers->count() > 0)
    <div style="margin-bottom: 12px;">
        <div style="font-size: 18px; font-weight: bold; color: #000000; margin-bottom: 8px;">รายชื่อผู้โดยสาร ({{ $booking->passengers->count() }} คน)</div>
        <table style="width: 100%; border-collapse: collapse; font-size: 16px; color: #000000;">
            <thead>
                <tr>
                    <th style="background: #4f46e5; color: white; padding: 8px; text-align: center; width: 60px; font-weight: bold; font-size: 16px;">ลำดับ</th>
                    <th style="background: #4f46e5; color: white; padding: 8px; text-align: left; font-weight: bold; font-size: 16px;">ชื่อ-นามสกุล</th>
                    <th style="background: #4f46e5; color: white; padding: 8px; text-align: left; font-weight: bold; font-size: 16px;">หน่วยงาน</th>
                </tr>
            </thead>
            <tbody>
                @foreach($booking->passengers as $index => $passenger)
                <tr style="background: {{ $index % 2 == 0 ? '#fff' : '#f9fafb' }};">
                    <td style="padding: 8px; border-bottom: 1px solid #cccccc; text-align: center; font-weight: bold;">{{ $index + 1 }}</td>
                    <td style="padding: 8px; border-bottom: 1px solid #cccccc; font-weight: bold;">{{ $passenger->name }}</td>
                    <td style="padding: 8px; border-bottom: 1px solid #cccccc; font-weight: bold;">{{ $passenger->department ?? '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
    
    <!-- Admin Notes -->
    @if($booking->admin_notes)
    <div style="margin-bottom: 5px;">
        <div style="font-size: 18px; font-weight: bold; color: #000000; margin-bottom: 5px;">หมายเหตุจากผู้อนุมัติ</div>
        <div style="background: #fef3c7; border: 1px solid #fcd34d; border-left: 4px solid #f59e0b; padding: 12px; border-radius: 4px; font-size: 17px; font-weight: bold; color: #000000;">
            {{ $booking->admin_notes }}
            @if($booking->approver)
            <div style="margin-top: 10px; font-size: 15px; color: #333333; font-weight: bold;">
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
                <td style="width: 30%; text-align: center; padding: 20px; vertical-align: bottom;">
                    <div style="font-size: 16px; font-weight: bold; color: #10b981; margin-bottom: 5px;">
                        ส่งคำขอผ่านระบบ
                    </div>
                    <div style="border-top: 1.5px solid #000000; width: 180px; margin-left: auto; margin-right: auto; padding-top: 8px; font-weight: bold; font-size: 18px; color: #000000;">
                       ผู้ขอใช้รถ
                    </div>
                    <div style="margin-top: 5px; font-size: 16px; font-weight: bold; color: #000000;">@if($booking->user){{ $booking->user->name }}@endif</div>
                    <div style="font-size: 15px; color: #333333; font-weight: bold;">วันที่ {{ $booking->created_at->format('d/m/Y H:i:s') }}</div>
                </td>
                <td style="width: 35%; text-align: center; padding: 20px; vertical-align: bottom;">
                    @if($booking->receiver)
                        <div style="font-size: 16px; font-weight: bold; color: #3b82f6; margin-bottom: 5px;">
                            รับเรื่องผ่านระบบ
                        </div>
                    @else
                        <div style="height: 29px;"></div>
                    @endif
                    <div style="border-top: 1.5px solid #000000; width: 180px; margin-left: auto; margin-right: auto; padding-top: 8px; font-weight: bold; font-size: 18px; color: #000000;">
                         ผู้รับเรื่อง
                    </div>
                    <div style="margin-top: 5px; font-size: 16px; font-weight: bold; color: #000000;">@if($booking->receiver){{ $booking->receiver->name }}@else ................................................@endif</div>
                    <div style="font-size: 15px; color: #333333; font-weight: bold;">วันที่ {{ $booking->received_at ? $booking->received_at->format('d/m/Y H:i:s') : '................................................' }}</div>
                </td>
                <td style="width: 35%; text-align: center; padding: 20px; vertical-align: bottom;">
                    @if($booking->approver)
                        <div style="font-size: 16px; font-weight: bold; color: @if($booking->status === 'rejected') #ef4444 @else #10b981 @endif; margin-bottom: 5px;">
                            @if($booking->status === 'rejected')
                                ไม่อนุมัติผ่านระบบ
                            @else
                                อนุมัติผ่านระบบ
                            @endif
                        </div>
                    @else
                        <div style="height: 29px;"></div>
                    @endif
                    <div style="border-top: 1.5px solid #000000; width: 180px; margin-left: auto; margin-right: auto; padding-top: 8px; font-weight: bold; font-size: 18px; color: #000000;">
                         ผู้อนุมัติ
                    </div>
                    <div style="margin-top: 5px; font-size: 16px; font-weight: bold; color: #000000;">@if($booking->approver){{ $booking->approver->name }}@else ................................................@endif</div>
                    <div style="font-size: 15px; color: #333333; font-weight: bold;">วันที่ {{ $booking->approved_at ? $booking->approved_at->format('d/m/Y H:i:s') : '................................................' }}</div>
                </td>
            </tr>
        </table>
    </div>
    
    <!-- Footer -->
    <div style="margin-top: 10px; padding-top: 5px; border-top: 1px solid #cccccc; text-align: center; font-size: 14px; color: #333333; font-weight: bold;">
        เอกสารนี้พิมพ์จากระบบขอใช้รถราชการ มหาวิทยาลัยเทคโนโลยีราชมงคลสุวรรณภูมิ
    </div>
</body>
</html>
