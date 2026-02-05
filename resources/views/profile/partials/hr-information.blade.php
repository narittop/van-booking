<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('ข้อมูลบุคลากร') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __('ข้อมูลจากระบบบุคลากร มหาวิทยาลัยเทคโนโลยีราชมงคลสุวรรณภูมิ') }}
        </p>
    </header>

    <!-- RUS-Connect LINE Notification -->
    <div class="mt-6 p-4 bg-white border border-gray-200 rounded-lg shadow-sm">
        <h3 class="text-lg font-medium text-gray-900 mb-4">
            <svg class="inline-block w-6 h-6 mr-2 text-green-500" viewBox="0 0 24 24" fill="currentColor">
                <path d="M19.365 9.863c.349 0 .63.285.63.631 0 .345-.281.63-.63.63H17.61v1.125h1.755c.349 0 .63.283.63.63 0 .344-.281.629-.63.629h-2.386c-.345 0-.627-.285-.627-.629V8.108c0-.345.282-.63.63-.63h2.386c.346 0 .627.285.627.63 0 .349-.281.63-.63.63H17.61v1.125h1.755zm-3.855 3.016c0 .27-.174.51-.432.596-.064.021-.133.031-.199.031-.211 0-.391-.09-.51-.25l-2.443-3.317v2.94c0 .344-.279.629-.631.629-.346 0-.626-.285-.626-.629V8.108c0-.27.173-.51.43-.595.06-.023.136-.033.194-.033.195 0 .375.104.495.254l2.462 3.33V8.108c0-.345.282-.63.63-.63.345 0 .63.285.63.63v4.771zm-5.741 0c0 .344-.282.629-.631.629-.345 0-.627-.285-.627-.629V8.108c0-.345.282-.63.63-.63.346 0 .628.285.628.63v4.771zm-2.466.629H4.917c-.345 0-.63-.285-.63-.629V8.108c0-.345.285-.63.63-.63.348 0 .63.285.63.63v4.141h1.756c.348 0 .629.283.629.63 0 .344-.282.629-.629.629M24 10.314C24 4.943 18.615.572 12 .572S0 4.943 0 10.314c0 4.811 4.27 8.842 10.035 9.608.391.082.923.258 1.058.59.12.301.079.766.038 1.08l-.164 1.02c-.045.301-.24 1.186 1.049.645 1.291-.539 6.916-4.078 9.436-6.975C23.176 14.393 24 12.458 24 10.314"/>
            </svg>
            การแจ้งเตือนผ่าน LINE
        </h3>
        
        <div class="flex items-center justify-between">
            <div>
                <p class="font-medium text-gray-800">รับการแจ้งเตือนผ่าน RUS-Connect</p>
                <p class="text-sm text-gray-600">Add friend LINE Official Account เพื่อรับแจ้งเตือนสถานะการจองรถ</p>
            </div>
            <a href="https://rusconnect.rmutsb.ac.th/" target="_blank" class="inline-flex items-center px-6 py-3 rounded-lg font-bold text-base text-white shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-200" style="background: linear-gradient(135deg, #00B900 0%, #00C300 100%);">
                <svg class="w-6 h-6 mr-2" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M19.365 9.863c.349 0 .63.285.63.631 0 .345-.281.63-.63.63H17.61v1.125h1.755c.349 0 .63.283.63.63 0 .344-.281.629-.63.629h-2.386c-.345 0-.627-.285-.627-.629V8.108c0-.345.282-.63.63-.63h2.386c.346 0 .627.285.627.63 0 .349-.281.63-.63.63H17.61v1.125h1.755zm-3.855 3.016c0 .27-.174.51-.432.596-.064.021-.133.031-.199.031-.211 0-.391-.09-.51-.25l-2.443-3.317v2.94c0 .344-.279.629-.631.629-.346 0-.626-.285-.626-.629V8.108c0-.27.173-.51.43-.595.06-.023.136-.033.194-.033.195 0 .375.104.495.254l2.462 3.33V8.108c0-.345.282-.63.63-.63.345 0 .63.285.63.63v4.771zm-5.741 0c0 .344-.282.629-.631.629-.345 0-.627-.285-.627-.629V8.108c0-.345.282-.63.63-.63.346 0 .628.285.628.63v4.771zm-2.466.629H4.917c-.345 0-.63-.285-.63-.629V8.108c0-.345.285-.63.63-.63.348 0 .63.285.63.63v4.141h1.756c.348 0 .629.283.629.63 0 .344-.282.629-.629.629M24 10.314C24 4.943 18.615.572 12 .572S0 4.943 0 10.314c0 4.811 4.27 8.842 10.035 9.608.391.082.923.258 1.058.59.12.301.079.766.038 1.08l-.164 1.02c-.045.301-.24 1.186 1.049.645 1.291-.539 6.916-4.078 9.436-6.975C23.176 14.393 24 12.458 24 10.314"/>
                </svg>
                Add Friend LINE
            </a>
        </div>
    </div>


    @if($hrdPerson)
    
        <div class="mt-6 space-y-4">
            <!-- Profile Photo and Name -->
            <div class="flex items-center space-x-6 mb-6">
                <div class="flex-shrink-0">
                    @if($hrdPerson->person_picture)
                        <img class="h-24 w-24 rounded-full object-cover border-4 border-indigo-100 shadow-lg" 
                             src="https://hrd.rmutsb.ac.th/upload/his/person/photo/{{ $hrdPerson->person_picture }}" 
                             alt="{{ $hrdPerson->fname_th }}"
                             onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode($hrdPerson->fname_th) }}&size=96&background=6366f1&color=fff';">
                    @else
                        <div class="h-24 w-24 rounded-full bg-indigo-100 flex items-center justify-center border-4 border-indigo-50 shadow-lg">
                            <span class="text-3xl font-bold text-indigo-600">{{ mb_substr($hrdPerson->fname_th, 0, 1) }}</span>
                        </div>
                    @endif
                </div>
                <div>
                    <h3 class="text-2xl font-bold text-gray-900">
                        {{ $hrdPerson->title_name_th }}{{ $hrdPerson->fname_th }} {{ $hrdPerson->lname_th }}
                    </h3>
                    <p class="text-gray-500">{{ $hrdPerson->pos_name_th ?? '' }}</p>
                    @if($hrdPerson->statuslist_name)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium mt-1 {{ $hrdPerson->statuslist_name == 'ปฏิบัติงาน' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                            {{ $hrdPerson->statuslist_name }}
                        </span>
                    @endif
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- ชื่อ-นามสกุล -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <dt class="text-sm font-medium text-gray-500">ชื่อ-นามสกุล</dt>
                    <dd class="mt-1 text-lg font-semibold text-gray-900">
                        {{ $hrdPerson->title_name_th }}{{ $hrdPerson->fname_th }} {{ $hrdPerson->lname_th }}
                    </dd>
                </div>

                <!-- เลขบัตรประชาชน -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <dt class="text-sm font-medium text-gray-500">เลขบัตรประชาชน</dt>
                    <dd class="mt-1 text-lg font-semibold text-gray-900">
                        {{ $hrdPerson->id_card ? substr($hrdPerson->id_card, 0, 1) . '-XXXX-XXXXX-XX-' . substr($hrdPerson->id_card, 12, 1) : '-' }}
                    </dd>
                </div>

                <!-- ประเภทบุคลากร -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <dt class="text-sm font-medium text-gray-500">ประเภทบุคลากร</dt>
                    <dd class="mt-1 text-lg font-semibold text-gray-900">
                        {{ $hrdPerson->rate_id ?? '-' }}
                    </dd>
                </div>

                <!-- ตำแหน่ง -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <dt class="text-sm font-medium text-gray-500">ตำแหน่ง</dt>
                    <dd class="mt-1 text-lg font-semibold text-gray-900">
                        {{ $hrdPerson->pos_name_th ?? '-' }}
                    </dd>
                </div>

                <!-- หน่วยงาน -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <dt class="text-sm font-medium text-gray-500">หน่วยงาน</dt>
                    <dd class="mt-1 text-lg font-semibold text-gray-900">
                        {{ $hrdPerson->unit_name_th ?? '-' }}
                    </dd>
                </div>

                <!-- คณะ/สำนัก -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <dt class="text-sm font-medium text-gray-500">คณะ/สำนัก</dt>
                    <dd class="mt-1 text-lg font-semibold text-gray-900">
                        {{ $hrdPerson->faculty_name_th ?? '-' }}
                    </dd>
                </div>

                <!-- ตำแหน่งทางวิชาการ/บริหาร -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <dt class="text-sm font-medium text-gray-500">ตำแหน่งทางวิชาการ/บริหาร</dt>
                    <dd class="mt-1 text-lg font-semibold text-gray-900">
                        {{ $hrdPerson->position_name ?? '-' }}
                    </dd>
                </div>

                <!-- สถานะ -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <dt class="text-sm font-medium text-gray-500">สถานะ</dt>
                    <dd class="mt-1 text-lg font-semibold text-gray-900">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-sm font-medium {{ $hrdPerson->statuslist_name == 'ปฏิบัติงาน' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                            {{ $hrdPerson->statuslist_name ?? '-' }}
                        </span>
                    </dd>
                </div>

                <!-- ศูนย์พื้นที่ -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <dt class="text-sm font-medium text-gray-500">ศูนย์พื้นที่</dt>
                    <dd class="mt-1 text-lg font-semibold text-gray-900">
                        {{ $hrdPerson->campus_name_th ?? '-' }}
                    </dd>
                </div>
            </div>
        </div>
    @else
        <div class="mt-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-yellow-800">ไม่พบข้อมูลบุคลากร</h3>
                    <p class="mt-2 text-sm text-yellow-700">
                        ระบบไม่พบข้อมูลบุคลากรที่ตรงกับบัญชีผู้ใช้นี้ กรุณาติดต่อฝ่ายบุคลากรหากต้องการความช่วยเหลือ
                    </p>
                </div>
            </div>
        </div>
    @endif
</section>

