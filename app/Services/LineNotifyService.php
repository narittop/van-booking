<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\HrdPerson;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LineNotifyService
{
    protected string $userApiUrl;
    protected string $groupApiUrl;

    // Mapping of requested_department to Group IDs
    protected array $groupIds = [
        'gad' => 'Ce65de46d953144fb6698202b81ec0125',       // กองบริหารทรัพยากร
        'subnon' => 'Ca1ec012412b55d7880026b0eb806cac6',    // ศูนย์นนทบุรี
        'subwa' => 'C8ac3aa73215c06ec48329d064c2bea57',     // ศูนย์วาสุกรี
        'subsu' => 'C42bfddd041af40370685e1c10cb969d9',     // ศูนย์พระนครศรีอยุธยา หันตรา
    ];

    public function __construct()
    {
        // User: RUS-Connect API
        $this->userApiUrl = 'https://rusconnect.rmutsb.ac.th/api/send-message';
        // Group: RUS-Connect API
        $this->groupApiUrl = 'https://rusconnect.rmutsb.ac.th/api/send-group-message';
    }

    /**
     * Send a message to User via RUS-Connect API
     */
    public function sendToUser(string $personId, string $message): bool
    {
        if (empty($personId)) {
            Log::warning('RUS-Connect: person_id is empty');
            return false;
        }

        try {
            $response = Http::post($this->userApiUrl, [
                'person_id' => $personId,
                'message' => $message,
            ]);

            if ($response->successful()) {
                Log::info('RUS-Connect message sent successfully to person_id: ' . $personId);
                return true;
            }

            Log::error('RUS-Connect failed: ' . $response->body());
            return false;
        } catch (\Exception $e) {
            Log::error('RUS-Connect exception: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send a message to LINE Group via RUS-Connect API
     */
    public function sendToLineGroup(string $groupId, string $message): bool
    {
        if (empty($groupId)) {
            Log::warning('RUS-Connect group: group_id is empty');
            return false;
        }
        
        try {
            $response = Http::post($this->groupApiUrl, [
                'group_id' => $groupId,
                'message' => $message,
            ]);

            if ($response->successful()) {
                Log::info('RUS-Connect group message sent successfully to: ' . $groupId);
                return true;
            }

            Log::error('RUS-Connect group message failed: ' . $response->body());
            return false;
        } catch (\Exception $e) {
            Log::error('RUS-Connect group message exception: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get RUS-Connect Person ID from User's ID Card
     */
    protected function getPersonId(string $idCard): ?string
    {
        try {
            $hrdPerson = HrdPerson::findByIdCard($idCard);
            // Use person_id from HrdPerson (e.g., 1615) instead of id_card
            return $hrdPerson ? (string) $hrdPerson->person_id : null;
        } catch (\Exception $e) {
            Log::warning('Failed to find HRD person: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get Group ID based on requested department
     */
    protected function getGroupId(Booking $booking): ?string
    {
        // Get requested department from booking
        $dept = $booking->requested_department;
        
        // Return mapped Group ID or null if not found
        return $this->groupIds[$dept] ?? null;
    }

    /**
     * Notify when a new booking is created
     */
    public function notifyNewBooking(Booking $booking): bool
    {
        $message = "\n🚐 [คำขอจองรถใหม่]\n";
        $message .= "━━━━━━━━━━━━━━━━━\n";
        $message .= "📋 เลขที่: #" . str_pad($booking->id, 6, '0', STR_PAD_LEFT) . "\n";
        $message .= "👤 ผู้ขอ: {$booking->user->name}\n";
        $message .= "🏢 หน่วยงาน: {$this->getThaiDepartmentName($booking->requested_department)}\n";
        $message .= "📅 วันที่: " . $booking->start_date->format('d/m/Y');
        
        if ($booking->start_date->ne($booking->end_date)) {
            $message .= " - " . $booking->end_date->format('d/m/Y');
        }
        
        $message .= "\n⏰ เวลา: {$booking->start_time} - {$booking->end_time}\n";
        $message .= "📍 จุดรับ: {$booking->pickup_location}\n";
        $message .= "🎯 ปลายทาง: {$booking->destination}\n";
        $message .= "👥 จำนวน: {$booking->seats_requested} ที่นั่ง\n";
        $message .= "━━━━━━━━━━━━━━━━━\n";
        $message .= "⏳ สถานะ: รอการอนุมัติ";

        // Send to Group
        $groupId = $this->getGroupId($booking);
        if ($groupId) {
            $this->sendToLineGroup($groupId, $message);
        } else {
             // Fallback to default group if dept not found (optional, using subsu as fallback or log warning)
             // Using subsu (Huntra) as fallback for safety based on user context
             $this->sendToLineGroup($this->groupIds['subsu'], $message);
        }

        // Send to User
        if ($booking->user->idcard) {
            $personId = $this->getPersonId($booking->user->idcard);
            if ($personId) {
                $this->sendToUser($personId, $message);
            } else {
                Log::warning("RUS-Connect: Could not find person_id for idcard {$booking->user->idcard}");
            }
        }

        return true;
    }

    /**
     * Notify when a booking is approved
     */
    public function notifyBookingApproved(Booking $booking): bool
    {
        $message = "\n✅ [อนุมัติการจอง]\n";
        $message .= "━━━━━━━━━━━━━━━━━\n";
        $message .= "📋 เลขที่: #" . str_pad($booking->id, 6, '0', STR_PAD_LEFT) . "\n";
        $message .= "👤 ผู้ขอ: {$booking->user->name}\n";
        $message .= "📅 วันที่: " . $booking->start_date->format('d/m/Y');
        
        if ($booking->start_date->ne($booking->end_date)) {
            $message .= " - " . $booking->end_date->format('d/m/Y');
        }
        
        $message .= "\n⏰ เวลา: {$booking->start_time} - {$booking->end_time}\n";
        $message .= "📍 จุดรับ: {$booking->pickup_location}\n";
        $message .= "🎯 ปลายทาง: {$booking->destination}\n";
        
        if ($booking->van) {
            $message .= "🚐 รถ: {$booking->van->license_plate}\n";
        }
        
        if ($booking->driver) {
            $message .= "🧑‍✈️ คนขับ: {$booking->driver->name}\n";
        }
        
        $message .= "━━━━━━━━━━━━━━━━━\n";
        $message .= "✅ สถานะ: อนุมัติแล้ว";
        
        // Send to Group
        $groupId = $this->getGroupId($booking);
        if ($groupId) {
            $this->sendToLineGroup($groupId, $message);
        } else {
             $this->sendToLineGroup($this->groupIds['subsu'], $message);
        }

        // Send to User
        if ($booking->user->idcard) {
            $personId = $this->getPersonId($booking->user->idcard);
            if ($personId) {
                $this->sendToUser($personId, $message);
            }
        }

        return true;
    }

    /**
     * Notify when a booking is received (รับเรื่องแล้ว)
     */
    public function notifyBookingReceived(Booking $booking): bool
    {
        $message = "\n📋 [รับเรื่องการจอง]\n";
        $message .= "━━━━━━━━━━━━━━━━━\n";
        $message .= "📋 เลขที่: #" . str_pad($booking->id, 6, '0', STR_PAD_LEFT) . "\n";
        $message .= "👤 ผู้ขอ: {$booking->user->name}\n";
        $message .= "📅 วันที่: " . $booking->start_date->format('d/m/Y');
        
        if ($booking->start_date->ne($booking->end_date)) {
            $message .= " - " . $booking->end_date->format('d/m/Y');
        }
        
        $message .= "\n⏰ เวลา: {$booking->start_time} - {$booking->end_time}\n";
        $message .= "📍 จุดรับ: {$booking->pickup_location}\n";
        $message .= "🎯 ปลายทาง: {$booking->destination}\n";
        
        if ($booking->van) {
            $message .= "🚐 รถ: {$booking->van->license_plate}\n";
        }
        
        if ($booking->driver) {
            $message .= "🧑‍✈️ คนขับ: {$booking->driver->name}\n";
        }
        
        $message .= "━━━━━━━━━━━━━━━━━\n";
        $message .= "📋 สถานะ: รับเรื่องแล้ว (รอผู้อำนวยการอนุมัติ)";
        
        // Send to Group
        $groupId = $this->getGroupId($booking);
        if ($groupId) {
            $this->sendToLineGroup($groupId, $message);
        } else {
             $this->sendToLineGroup($this->groupIds['subsu'], $message);
        }

        // Send to User
        if ($booking->user->idcard) {
            $personId = $this->getPersonId($booking->user->idcard);
            if ($personId) {
                $this->sendToUser($personId, $message);
            }
        }

        return true;
    }

    /**
     * Notify when a booking is rejected
     */
    public function notifyBookingRejected(Booking $booking): bool
    {
        $message = "\n❌ [ไม่อนุมัติการจอง]\n";
        $message .= "━━━━━━━━━━━━━━━━━\n";
        $message .= "📋 เลขที่: #" . str_pad($booking->id, 6, '0', STR_PAD_LEFT) . "\n";
        $message .= "👤 ผู้ขอ: {$booking->user->name}\n";
        $message .= "📅 วันที่: " . $booking->start_date->format('d/m/Y');
        
        if ($booking->start_date->ne($booking->end_date)) {
            $message .= " - " . $booking->end_date->format('d/m/Y');
        }
        
        $message .= "\n🎯 ปลายทาง: {$booking->destination}\n";
        
        if ($booking->admin_notes) {
            $message .= "📝 หมายเหตุ: {$booking->admin_notes}\n";
        }
        
        $message .= "━━━━━━━━━━━━━━━━━\n";
        $message .= "❌ สถานะ: ไม่อนุมัติ";
        
        // Send to Group
        $groupId = $this->getGroupId($booking);
        if ($groupId) {
            $this->sendToLineGroup($groupId, $message);
        } else {
             $this->sendToLineGroup($this->groupIds['subsu'], $message);
        }

        // Send to User
        if ($booking->user->idcard) {
            $personId = $this->getPersonId($booking->user->idcard);
            if ($personId) {
                $this->sendToUser($personId, $message);
            }
        }

        return true;
    }

    /**
     * Notify when a booking is completed
     */
    public function notifyBookingCompleted(Booking $booking): bool
    {
        $message = "\n🏁 [การเดินทางเสร็จสิ้น]\n";
        $message .= "━━━━━━━━━━━━━━━━━\n";
        $message .= "📋 เลขที่: #" . str_pad($booking->id, 6, '0', STR_PAD_LEFT) . "\n";
        $message .= "👤 ผู้ขอ: {$booking->user->name}\n";
        $message .= "📅 วันที่: " . $booking->start_date->format('d/m/Y');
        
        if ($booking->start_date->ne($booking->end_date)) {
            $message .= " - " . $booking->end_date->format('d/m/Y');
        }
        
        $message .= "\n🎯 ปลายทาง: {$booking->destination}\n";
        
        if ($booking->van) {
            $message .= "🚐 รถ: {$booking->van->license_plate}\n";
        }
        
        $message .= "━━━━━━━━━━━━━━━━━\n";
        $message .= "🏁 สถานะ: เสร็จสิ้น";
        
        // Send to Group
        $groupId = $this->getGroupId($booking);
        if ($groupId) {
            $this->sendToLineGroup($groupId, $message);
        } else {
             $this->sendToLineGroup($this->groupIds['subsu'], $message);
        }

        // Send to User
        if ($booking->user->idcard) {
            $personId = $this->getPersonId($booking->user->idcard);
            if ($personId) {
                $this->sendToUser($personId, $message);
            }
        }

        return true;
    }
    
    /**
     * Helper to get Thai Department name
     */
    protected function getThaiDepartmentName($code) {
        $names = [
            'gad' => 'กองบริหารทรัพยากร',
            'subnon' => 'ศูนย์นนทบุรี',
            'subwa' => 'ศูนย์วาสุกรี',
            'subsu' => 'ศูนย์พระนครศรีอยุธยา หันตรา',
        ];
        return $names[$code] ?? $code;
    }
}
