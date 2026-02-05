@php
    $lastInput = session('laso_last_input', []);
    $lasoResults = session('laso_results', []);

    $defaultCungStructure = [
        'can_chi_cung' => 'Đang cập nhật',
        'chinh_tinh' => 'Đang cập nhật',
        'phu_tinh_cat' => 'Đang cập nhật',
        'phu_tinh_sat' => 'Đang cập nhật'
    ];

    $cungList = [
        'menh' => [
            'name' => 'Cung Mệnh',
            'image' => 'menh.svg',
            'description' => 'Phản ánh bản chất, tính cách, điểm mạnh – yếu và xu hướng hành động.',
            'data' => $defaultCungStructure
        ],
        'huynh_de' => [
            'name' => 'Cung Huynh Đệ',
            'image' => 'huynh-de.svg',
            'description' => 'Cho thấy quan hệ anh chị em và ảnh hưởng gia đình thời trẻ.',
            'data' => $defaultCungStructure
        ],
        'phuc_duc' => [
            'name' => 'Cung Phúc Đức',
            'image' => 'phuc-duc.svg',
            'description' => 'Thể hiện nền tảng tinh thần và phúc khí gia đình.',
            'data' => $defaultCungStructure
        ],
        'tai_bach' => [
            'name' => 'Cung Tài Bạch',
            'image' => 'tai-bach.svg',
            'description' => 'Phản ánh cách kiếm tiền, sử dụng tiền và độ ổn định tài chính.',
            'data' => $defaultCungStructure
        ],
        'quan_loc' => [
            'name' => 'Cung Quan Lộc',
            'image' => 'quan-loc.svg',
            'description' => 'Phản ánh sự nghiệp, thái độ làm việc và khả năng thăng tiến.',
            'data' => $defaultCungStructure
        ],
        'tat_ach' => [
            'name' => 'Cung Tật Ách',
            'image' => 'tat-ach.svg',
            'description' => 'Cho thấy sức khỏe, tinh thần và những áp lực cần lưu ý.',
            'data' => $defaultCungStructure
        ],
        'phu_the' => [
            'name' => 'Cung Phu Thê',
            'image' => 'phu-the.svg',
            'description' => 'Nói về hôn nhân, quan điểm tình cảm và cách duy trì mối quan hệ.',
            'data' => $defaultCungStructure
        ],
        'phu_mau' => [
            'name' => 'Cung Phụ Mẫu',
            'image' => 'phu-mau.svg',
            'description' => 'Phản ánh quan hệ cha mẹ và ảnh hưởng gia đình lâu dài.',
            'data' => $defaultCungStructure
        ],
        'tu_tuc' => [
            'name' => 'Cung Tử Tức',
            'image' => 'tu-tuc.svg',
            'description' => 'Liên quan đến con cái, trách nhiệm chăm sóc và khả năng gánh vác.',
            'data' => $defaultCungStructure
        ],
        'dien_trach' => [
            'name' => 'Cung Điền Trạch',
            'image' => 'dien-trach.svg',
            'description' => 'Cho thấy nhà cửa, tài sản và sự ổn định nơi ở.',
            'data' => $defaultCungStructure
        ],
        'no_boc' => [
            'name' => 'Cung Nô Bộc',
            'image' => 'no-boc.svg',
            'description' => 'Nói về bạn bè, đồng nghiệp, đối tác và mức độ hỗ trợ.',
            'data' => $defaultCungStructure
        ],
        'thien_di' => [
            'name' => 'Cung Thiên Di',
            'image' => 'thien-di.svg',
            'description' => 'Phản ánh môi trường bên ngoài và cơ hội phát triển khi đi xa.',
            'data' => $defaultCungStructure
        ]
    ];

    // LẤY DỮ LIỆU TỪ SESSION HOẶC TỪ API CALL MỚI NHẤT
    $palacesData = null;

    // Thử lấy từ session results trước (dữ liệu mới nhất từ API)
    if (!empty($lasoResults) && isset($lasoResults['normalizedData']['laso_details']['palaces'])) {
        $palacesData = $lasoResults['normalizedData']['laso_details']['palaces'];
        \Log::info('Using palaces data from session results');
    }
    // Nếu không có trong results, thử lấy từ database (fallback)
    elseif (!empty($lastInput)) {
        $gioSinhForId = ($lastInput['dl_gio'] ?? '00') . ':' . str_pad($lastInput['dl_phut'] ?? '00', 2, '0', STR_PAD_LEFT);
        $lasoId = \App\Models\LasoLuanGiai::generateLasoId(
            $lastInput['dl_date_processed'] ?? '',
            $gioSinhForId,
            $lastInput['gioi_tinh'] ?? '',
            $lastInput['nam_xem'] ?? date('Y')
        );

        if ($lasoId) {
            $existingLaso = \App\Models\LasoLuanGiai::where('laso_id', $lasoId)->first();

            if ($existingLaso && isset($existingLaso->api_response['data']['laso_details']['palaces'])) {
                $palacesData = $existingLaso->api_response['data']['laso_details']['palaces'];
                \Log::info('Using palaces data from database');
            }
        }
    }

    // NẾU VẪN KHÔNG CÓ DỮ LIỆU, GỌI API TRỰC TIẾP
    if (!$palacesData && !empty($lastInput) && isset($lastInput['ho_ten'], $lastInput['gioi_tinh'], $lastInput['dl_date_processed'], $lastInput['dl_gio'], $lastInput['dl_phut'], $lastInput['nam_xem'])) {
        try {
            $dateProcessed = $lastInput['dl_date_processed'];
            $dateParts = explode('/', explode(' ', $dateProcessed)[0]);

            if (count($dateParts) === 3) {
                $apiData = [
                    'ho_ten' => $lastInput['ho_ten'],
                    'gioi_tinh' => $lastInput['gioi_tinh'],
                    'nam_xem' => $lastInput['nam_xem'],
                    'dl_date_processed' => $dateProcessed,
                    'calendar_type' => $lastInput['calendar_type'] ?? 'solar',
                    'dl_gio' => $lastInput['dl_gio'],
                    'dl_phut' => $lastInput['dl_phut'],
                    'dl_ngay' => (int)$dateParts[0],
                    'dl_thang' => (int)$dateParts[1],
                    'dl_nam' => (int)$dateParts[2],
                    'app_name' => 'phonglich'
                ];

                \Log::info('Calling API from blade to get palaces data');
                $response = \Illuminate\Support\Facades\Http::timeout(15)->post('https://api32.xemlicham.com/laso_v2/store_laso.php', $apiData);

                if ($response->successful()) {
                    $result = $response->json();
                    if (isset($result['success']) && $result['success'] && isset($result['data']['laso_details']['palaces'])) {
                        $palacesData = $result['data']['laso_details']['palaces'];
                        \Log::info('Successfully retrieved palaces data from API');
                    }
                }
            }
        } catch (\Exception $e) {
            \Log::error('Error calling API from blade: ' . $e->getMessage());
        }
    }

    // XỬ LÝ DỮ LIỆU 12 CUNG NẾU CÓ
    if ($palacesData) {
        // Mapping tên cung từ API
        $cungMapping = [
            'MỆNH' => 'menh',
            'HUYNH ĐỆ' => 'huynh_de',
            'PHÚC ĐỨC' => 'phuc_duc',
            'TÀI BẠCH' => 'tai_bach',
            'QUAN LỘC' => 'quan_loc',
            'TẬT ÁCH' => 'tat_ach',
            'PHU THÊ' => 'phu_the',
            'PHỤ MẪU' => 'phu_mau',
            'TỬ TỨC' => 'tu_tuc',
            'ĐIỀN TRẠCH' => 'dien_trach',
            'NÔ BỘC' => 'no_boc',
            'THIÊN DI' => 'thien_di'
        ];
        
        // Function để extract tên sao
        $extractStarNames = function($stars) {
            if (!is_array($stars)) return 'Không có';
            $names = array_filter(array_map(fn($star) => $star['name'] ?? null, $stars));
            return !empty($names) ? $names : 'Không có';
        };
        
        // Duyệt qua các cung và cập nhật dữ liệu
        foreach ($palacesData as $palaceName => $palaceData) {
            $cungChucNang = trim(preg_replace('/\s*\([^)]*\)\s*/', '', $palaceData['cung_chuc_nang'] ?? ''));
            
            if (isset($cungMapping[$cungChucNang])) {
                $key = $cungMapping[$cungChucNang];
                
                $cungList[$key]['data'] = [
                    'can_chi_cung' => str_replace('.', ' ', $palaceData['can_chi_cung'] ?? 'N/A'),
                    'chinh_tinh' => $extractStarNames($palaceData['chinh_tinh'] ?? []),
                    'phu_tinh_cat' => $extractStarNames($palaceData['phu_tinh_cat'] ?? []),
                    'phu_tinh_sat' => $extractStarNames($palaceData['phu_tinh_sat'] ?? [])
                ];
            }
        }
        
        \Log::info('Successfully populated 12 palaces data');
    } else {
        \Log::warning('No palaces data found in session or database');
    }

    // Function hiển thị giá trị sao
    $displayStars = function($stars) {
        return is_array($stars) ? implode(', ', $stars) : $stars;
    };
@endphp

<!-- SVG Gradient Definitions - Đặt ngoài để tất cả SVG trên trang đều dùng được -->
<svg width="0" height="0" style="position: absolute; pointer-events: none;" aria-hidden="true">
    <defs>
        <linearGradient id="paint0_linear_87_8320_global" x1="15.6769" y1="10.874" x2="7.07106" y2="19.5506" gradientUnits="userSpaceOnUse">
            <stop stop-color="#00C3FF"></stop>
            <stop offset="1" stop-color="#1BE2FA"></stop>
        </linearGradient>
        <linearGradient id="paint1_linear_87_8320_global" x1="20.292" y1="15.8176" x2="31.7381" y2="15.8176" gradientUnits="userSpaceOnUse">
            <stop stop-color="#FFCE00"></stop>
            <stop offset="1" stop-color="#FFEA00"></stop>
        </linearGradient>
        <linearGradient id="paint2_linear_87_8320_global" x1="7.36932" y1="30.1004" x2="22.595" y2="17.8937" gradientUnits="userSpaceOnUse">
            <stop stop-color="#DE2453"></stop>
            <stop offset="1" stop-color="#FE3944"></stop>
        </linearGradient>
        <linearGradient id="paint3_linear_87_8320_global" x1="8.10725" y1="1.90137" x2="22.5971" y2="13.7365" gradientUnits="userSpaceOnUse">
            <stop stop-color="#11D574"></stop>
            <stop offset="1" stop-color="#01F176"></stop>
        </linearGradient>
    </defs>
</svg>

<!-- 12 Cung Section -->
<div class="card-section my-4">
    <h3 class="card-section-title fw-bold box-title text-center">Danh sách 12 Cung</h3>
    <div class="card-grid mt-3">
        @foreach($cungList as $cungKey => $cung)
            <div class="main-card d-flex flex-column">
                <div class="card-image">
                    <img src="{{ asset('images/cung/' . $cung['image']) }}" alt="{{ $cung['name'] }}" class="card-img">
                </div>
                <div class="card-content">
                    <h4 class="cung-name m-0">{{ $cung['name'] }} <span class="can-chi-text">({{ $cung['data']['can_chi_cung'] }})</span></h4>
                    <p class="cung-description m-0">{{ $cung['description'] }}</p>
                    <div class="cung-starts">
                        <div class="star-row">
                            <span class="star-label label-green">Chính tinh: 
                                <span class="star-value">{{ $displayStars($cung['data']['chinh_tinh']) }}</span>
                            </span>
                        </div>
                        <div class="star-row">
                            <span class="star-label label-green">Phụ tinh cát: 
                                <span class="star-value">{{ $displayStars($cung['data']['phu_tinh_cat']) }}</span>
                            </span>
                        </div>
                        <div class="star-row">
                            <span class="star-label label-red">Phụ tinh sát: 
                                <span class="star-value">{{ $displayStars($cung['data']['phu_tinh_sat']) }}</span>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="card-footer border-0">
                    <a href="javascript:void(0)" class="card-btn text-center text-decoration-none" data-bs-toggle="modal" data-bs-target="#appOnlyModal">
                        Xem chi tiết
                        <img src="{{ asset('images/cung/icon-xem-them.svg') }}" alt="Xem thêm" style="width: 12px; height: 12px; margin-left: 6px; margin-bottom: 3px">
                    </a>
                </div>
            </div>
        @endforeach
    </div>

    <div class="cards-actions d-flex justify-content-center" id="cungCardsActions">
        <button class="cards-btn cards-btn-expand" onclick="expandCungCards()">Xem thêm</button>
        <button class="cards-btn cards-btn-collapse" onclick="collapseCungCards()" style="display: none;">Rút gọn</button>
    </div>
</div>

<div class="modal fade" id="appOnlyModal" tabindex="-1" aria-labelledby="appOnlyModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header header-close border-0">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body cung-body pt-2">
                <p class="app-desc fw-bold text-uppercase text-center mb-1">Chức năng này chỉ có trên ứng dụng</p>
                <p class="app-desc mb-1">Nội dung <strong>luận giải chi tiết lá số Tử Vi</strong> hiện được cung cấp <strong>độc quyền trên ứng dụng Lịch Âm</strong>, bao gồm phân tích từng cung, vận hạn theo năm và lời khuyên cá nhân hóa theo lá số.</p>
                <p class="app-desc mb-1">Vui lòng <strong>tải ứng dụng Lịch Âm miễn phí</strong> để tiếp tục xem đầy đủ nội dung luận giải.</p>

                <div class="card qr-card border-0">
                    <div class="card-body d-flex flex-column align-items-center gap-3">
                        <div class="qr-background"></div>
                        
                        <div class="d-flex gap-2 flex-wrap justify-content-center">
                            <a href="https://apps.apple.com/vn/app/l%E1%BB%8Bch-%C3%A2m-l%E1%BB%8Bch-v%E1%BA%A1n-ni%C3%AAn-2025/id6499255314?l=vi" class="store-btn app-store-link d-flex align-items-center justify-content-center gap-1 text-decoration-none">
                                <svg fill="#ffffff" height="22" width="22" version="1.1" id="Capa_1"
                            xmlns="http://www.w3.org/2000/svg" style="min-width:30px" viewBox="0 0 22.773 22.773">
                            <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                            <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                            <g id="SVGRepo_iconCarrier">
                                <g>
                                    <g>
                                        <path
                                            d="M15.769,0c0.053,0,0.106,0,0.162,0c0.13,1.606-0.483,2.806-1.228,3.675c-0.731,0.863-1.732,1.7-3.351,1.573 c-0.108-1.583,0.506-2.694,1.25-3.561C13.292,0.879,14.557,0.16,15.769,0z">
                                        </path>
                                        <path
                                            d="M20.67,16.716c0,0.016,0,0.03,0,0.045c-0.455,1.378-1.104,2.559-1.896,3.655c-0.723,0.995-1.609,2.334-3.191,2.334 c-1.367,0-2.275-0.879-3.676-0.903c-1.482-0.024-2.297,0.735-3.652,0.926c-0.155,0-0.31,0-0.462,0 c-0.995-0.144-1.798-0.932-2.383-1.642c-1.725-2.098-3.058-4.808-3.306-8.276c0-0.34,0-0.679,0-1.019 c0.105-2.482,1.311-4.5,2.914-5.478c0.846-0.52,2.009-0.963,3.304-0.765c0.555,0.086,1.122,0.276,1.619,0.464 c0.471,0.181,1.06,0.502,1.618,0.485c0.378-0.011,0.754-0.208,1.135-0.347c1.116-0.403,2.21-0.865,3.652-0.648 c1.733,0.262,2.963,1.032,3.723,2.22c-1.466,0.933-2.625,2.339-2.427,4.74C17.818,14.688,19.086,15.964,20.67,16.716z">
                                        </path>
                                    </g>
                                </g>
                            </g>
                        </svg>
                                <span>App Store (iOS)</span>
                            </a>
                            <a href="https://play.google.com/store/apps/details?id=com.rvn.licham&hl=vi" class="store-btn d-flex align-items-center justify-content-center gap-1 text-decoration-none">
                                <svg width="22" height="22" viewBox="0 0 32.00 32.00" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <g>
                                        <path d="M7.63473 28.5466L20.2923 15.8179L7.84319 3.29883C7.34653 3.61721 7 4.1669 7 4.8339V27.1664C7 27.7355 7.25223 28.2191 7.63473 28.5466Z" fill="url(#paint0_linear_87_8320_global)"></path>
                                        <path d="M30.048 14.4003C31.3169 15.0985 31.3169 16.9012 30.048 17.5994L24.9287 20.4165L20.292 15.8175L24.6923 11.4531L30.048 14.4003Z" fill="url(#paint1_linear_87_8320_global)"></path>
                                        <path d="M24.9292 20.4168L20.2924 15.8179L7.63477 28.5466C8.19139 29.0232 9.02389 29.1691 9.75635 28.766L24.9292 20.4168Z" fill="url(#paint2_linear_87_8320_global)"></path>
                                        <path d="M7.84277 3.29865L20.2919 15.8177L24.6922 11.4533L9.75583 3.23415C9.11003 2.87878 8.38646 2.95013 7.84277 3.29865Z" fill="url(#paint3_linear_87_8320_global)"></path>
                                    </g>
                                </svg>
                                <span>CH Play (Android)</span>
                            </a>
                        </div>
                    </div>
                </div>

                <p class="app-note mb-0 mt-3 fst-italic">Ứng dụng cam kết <strong>bảo mật thông tin cá nhân</strong> và chỉ sử dụng dữ liệu phục vụ việc lập và luận giải lá số.</p>
            </div>
        </div>
    </div>
</div>

<script>
    function expandCungCards() {
        const cardGrid = document.querySelector('.card-grid');
        const expandBtn = document.querySelector('.cards-btn-expand');
        const collapseBtn = document.querySelector('.cards-btn-collapse');

        if (cardGrid) cardGrid.classList.add('show-all');
        if (expandBtn) expandBtn.style.display = 'none';
        if (collapseBtn) collapseBtn.style.display = 'inline-block';
    }

    function collapseCungCards() {
        const cardGrid = document.querySelector('.card-grid');
        const expandBtn = document.querySelector('.cards-btn-expand');
        const collapseBtn = document.querySelector('.cards-btn-collapse');

        if (cardGrid) cardGrid.classList.remove('show-all');
        if (expandBtn) expandBtn.style.display = 'inline-block';
        if (collapseBtn) collapseBtn.style.display = 'none';
    }
</script>