<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ตรวจสอบใบจองรถ #{{ str_pad($booking->id, 6, '0', STR_PAD_LEFT) }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Noto Sans Thai', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        
        .container {
            max-width: 600px;
            margin: 0 auto;
        }
        
        .verify-badge {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            padding: 20px;
            border-radius: 20px 20px 0 0;
            text-align: center;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
        }
        
        .verify-badge svg {
            width: 60px;
            height: 60px;
            margin-bottom: 10px;
        }
        
        .verify-badge h1 {
            font-size: 20px;
            font-weight: 600;
            margin-bottom: 5px;
        }
        
        .verify-badge p {
            font-size: 14px;
            opacity: 0.9;
        }
        
        .card {
            background: white;
            border-radius: 0 0 20px 20px;
            padding: 25px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
        }
        
        .booking-header {
            text-align: center;
            padding-bottom: 20px;
            border-bottom: 2px solid #f3f4f6;
            margin-bottom: 20px;
        }
        
        .booking-id {
            display: inline-block;
            background: #eef2ff;
            color: #4f46e5;
            padding: 8px 16px;
            border-radius: 30px;
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 10px;
        }
        
        .status-badge {
            display: inline-block;
            padding: 6px 16px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
        }
        
        .status-pending { background: #fef3c7; color: #92400e; }
        .status-approved { background: #d1fae5; color: #065f46; }
        .status-rejected { background: #fee2e2; color: #991b1b; }
        .status-completed { background: #dbeafe; color: #1e40af; }
        
        .section {
            margin-bottom: 20px;
        }
        
        .section-title {
            font-size: 14px;
            font-weight: 600;
            color: #6b7280;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .section-title svg {
            width: 18px;
            height: 18px;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }
        
        .info-item {
            background: #f9fafb;
            padding: 12px;
            border-radius: 10px;
        }
        
        .info-item.full {
            grid-column: 1 / -1;
        }
        
        .info-label {
            font-size: 12px;
            color: #9ca3af;
            margin-bottom: 4px;
        }
        
        .info-value {
            font-size: 14px;
            color: #1f2937;
            font-weight: 500;
        }
        
        .footer {
            text-align: center;
            margin-top: 20px;
            padding: 15px;
            background: #f9fafb;
            border-radius: 12px;
        }
        
        .footer img {
            width: 40px;
            height: 40px;
            margin-bottom: 8px;
        }
        
        .footer p {
            font-size: 12px;
            color: #6b7280;
        }
        
        .footer strong {
            color: #374151;
        }
        
        .timestamp {
            text-align: center;
            font-size: 11px;
            color: white;
            margin-top: 15px;
            opacity: 0.8;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="verify-badge">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
            </svg>
            <h1>✓ เอกสารยืนยันแล้ว</h1>
            <p>เอกสารนี้พิมพ์จากระบบจองรถราชการ</p>
        </div>
        
        <div class="card">
            <div class="booking-header">
                <div class="booking-id">#{{ str_pad($booking->id, 6, '0', STR_PAD_LEFT) }}</div>
                <br>
                <span class="status-badge status-{{ $booking->status }}">
                    {{ $booking->status_text }}
                </span>
            </div>
            
            <div class="section">
                <div class="section-title">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    ข้อมูลผู้ขอใช้รถ
                </div>
                <div class="info-grid">
                    <div class="info-item full">
                        <div class="info-label">ชื่อผู้ขอ</div>
                        <div class="info-value">{{ $booking->user->name }}</div>
                    </div>
                    <div class="info-item full">
                        <div class="info-label">หน่วยงาน</div>
                        <div class="info-value">{{ \App\Models\Van::DEPARTMENT_LABELS[$booking->requested_department] ?? '-' }}</div>
                    </div>
                </div>
            </div>
            
            <div class="section">
                <div class="section-title">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    รายละเอียดการเดินทาง
                </div>
                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label">วันที่เริ่ม</div>
                        <div class="info-value">{{ $booking->start_date->format('d/m/Y') }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">เวลา</div>
                        <div class="info-value">{{ $booking->start_time }} น.</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">วันที่สิ้นสุด</div>
                        <div class="info-value">{{ $booking->end_date ? $booking->end_date->format('d/m/Y') : '-' }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">เวลา</div>
                        <div class="info-value">{{ $booking->end_time }} น.</div>
                    </div>
                    <div class="info-item full">
                        <div class="info-label">จุดรับ</div>
                        <div class="info-value">{{ $booking->pickup_location }}</div>
                    </div>
                    <div class="info-item full">
                        <div class="info-label">ปลายทาง</div>
                        <div class="info-value">{{ $booking->destination }}</div>
                    </div>
                </div>
            </div>
            
            @if($booking->van || $booking->driver)
            <div class="section">
                <div class="section-title">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                    </svg>
                    รถและพนักงานขับรถ
                </div>
                <div class="info-grid">
                    @if($booking->van)
                    <div class="info-item">
                        <div class="info-label">รถที่ใช้</div>
                        <div class="info-value">{{ $booking->van->name }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">ทะเบียน</div>
                        <div class="info-value">{{ $booking->van->license_plate }}</div>
                    </div>
                    @endif
                    @if($booking->driver)
                    <div class="info-item full">
                        <div class="info-label">พนักงานขับรถ</div>
                        <div class="info-value">{{ $booking->driver->name }}</div>
                    </div>
                    @endif
                </div>
            </div>
            @endif
            
            @if($booking->approver)
            <div class="section">
                <div class="section-title">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    การอนุมัติ
                </div>
                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label">ผู้อนุมัติ</div>
                        <div class="info-value">{{ $booking->approver->name }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">วันที่อนุมัติ</div>
                        <div class="info-value">{{ $booking->approved_at->format('d/m/Y H:i') }}</div>
                    </div>
                </div>
            </div>
            @endif
            
            <div class="footer">
                <img src="{{ asset('image/logorus.png') }}" alt="Logo">
                <p><strong>ระบบจองรถราชการ</strong></p>
                <p>มหาวิทยาลัยเทคโนโลยีราชมงคลสุวรรณภูมิ</p>
            </div>
        </div>
        
        <div class="timestamp">
            ตรวจสอบเมื่อ {{ now()->format('d/m/Y H:i:s') }}
        </div>
    </div>
</body>
</html>
