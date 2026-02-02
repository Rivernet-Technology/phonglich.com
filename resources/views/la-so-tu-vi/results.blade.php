@extends('welcome')
@section('content')
    @push('styles')
        <link rel="stylesheet" href="{{ asset('/css/vanilla-daterangepicker.css?v=11.6') }}">
        <link rel="stylesheet" href="{{ asset('/css/la-so.css?v=11.6') }}">
        <style>
            .main-content-wrapper {
                background-image: url(../images/Quy_Trinh_Bg.png);
                background-repeat: no-repeat;
                background-position: top center;
                background-size: 100% auto;
                /* Ảnh full-width, giữ đúng tỉ lệ */
                background-color: #ffffff;
                /* Màu nền phía dưới */
            }

            .alert-danger {
                color: #721c24;
                background-color: #f8d7da;
                border-color: #f5c6cb;
            }

            .alert {
                position: relative;
                padding: .75rem 1.25rem;
                margin-bottom: 1rem;
                border: 1px solid transparent;
                border-radius: .25rem;
            }

            .shimmer {
                position: relative;
                overflow: hidden;
                background: #f0f0f0;
                animation: pulse 1.5s infinite;
                border-radius: 8px;
            }

            @keyframes pulse {
                0% {
                    opacity: 0.75;
                }

                50% {
                    opacity: 1;
                }

                100% {
                    opacity: 0.75;
                }
            }

            .shimmer::after {
                content: "";
                position: absolute;
                top: 0;
                left: -100%;
                width: 100%;
                height: 100%;
                background: linear-gradient(120deg,
                        rgba(255, 255, 255, 0.0) 0%,
                        rgba(255, 255, 255, 0.5) 50%,
                        rgba(255, 255, 255, 0.0) 100%);
                animation: shimmer 1.5s infinite;
            }

            @keyframes shimmer {
                0% {
                    left: -100%;
                }

                100% {
                    left: 100%;
                }
            }

            /* Luan Giai Expand Styles */
            .luan-giai-container {
                position: relative;
            }

            .luan-giai-content {
                max-height: 300px;
                overflow: hidden;
                transition: max-height 0.5s ease-in-out;
            }

            .luan-giai-content.expanded {
                max-height: none;
            }

            .luan-giai-overlay {
                position: absolute;
                bottom: 0;
                left: 0;
                right: 0;
                height: 100px;
                background: linear-gradient(transparent, rgba(255, 255, 255, 0.9));
                display: flex;
                align-items: flex-end;
                justify-content: center;
                padding-bottom: 15px;
                transition: opacity 0.3s ease-in-out;
            }

            .luan-giai-overlay.hidden {
                opacity: 0;
                pointer-events: none;
            }

            .xem-them-btn {
                background: linear-gradient(45deg, #2254AB, #4a90e2);
                border: none;
                padding: 10px 20px;
                border-radius: 25px;
                font-weight: 500;
                color: white;
                box-shadow: 0 4px 15px rgba(34, 84, 171, 0.3);
                transition: all 0.3s ease;
            }

            .xem-them-btn:hover {
                transform: translateY(-2px);
                box-shadow: 0 6px 20px rgba(34, 84, 171, 0.4);
                color: white;
            }
        </style>
    @endpush

    <div class="bg-la-so">
        <div class="container-setup">
            <nav aria-label="breadcrumb" class="content-title-detail">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('home') }}" style="color: #2254AB; text-decoration: underline;">Trang chủ</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('laso.create') }}" style="color: #2254AB; text-decoration: underline;">Lá số tử
                            vi</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                        Kết quả lá số
                    </li>
                </ol>
            </nav>
            <h1 class="content-title-home-lich " style="color: #192E52">
                @if (isset($normalizedData['ho_ten']) && $normalizedData['ho_ten'])
                    Tổng Quan Lá Số Tử Vi Của {{ $normalizedData['ho_ten'] }}
                @else
                    Tổng Quan Lá Số Tử Vi Của Bạn
                @endif
            </h1>
            <div class="mt-3">
                <div class="row g-0 g-lg-3">
                    <div class="col-xl-9 col-sm-12 col-12 ">
                        @if (isset($imageUrl) && $imageUrl)
                            <!-- Tiêu đề Số tử vi của tên -->
                            <div class="box--bg-thang mb-3">
                                <div class=" text-center">
                                    <div class="d-flex justify-content-center">
                                        <div class="img-zoom-container" id="img-zoom-container" style="position: relative;">

                                            <!-- Ảnh khung (placeholder) -->
                                            <img src="/images/la_so_news1.png" id="laso-frame" class="img-fluid shimmer"
                                                style="display:block;">

                                            <!-- Ảnh lá số thật — KHÔNG load trực tiếp -->
                                            <img id="laso-image" class="img-fluid" style="display:none;">

                                            <!-- Loading spinner -->
                                            <div id="laso-loading"
                                                style="position:absolute;top:50%;left:50%; transform:translate(-50%, -50%); font-weight:bold;">
                                                Đang tải lá số...
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-3 ">
                                        <a href="{{ route('laso.download', ['url' => $imageUrl, 'ho_ten' => $normalizedData['ho_ten'] ?? '']) }}"
                                            class="btn btn-success me-2 mt-2 "
                                            style="background: linear-gradient(45deg, #2254AB, #4a90e2); border: none; padding: 10px 20px; border-radius: 25px; font-weight: 500;color: white; box-shadow: 0 4px 15px rgba(34, 84, 171, 0.3);"
                                            download>
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                fill="currentColor" class="bi bi-cloud-download" viewBox="0 0 16 16">
                                                <path
                                                    d="M4.406 1.342A5.53 5.53 0 0 1 8 0c2.69 0 4.923 2 5.166 4.579C14.758 4.804 16 6.137 16 7.773 16 9.569 14.502 11 12.687 11H10a.5.5 0 0 1 0-1h2.688C13.979 10 15 8.988 15 7.773c0-1.216-1.02-2.228-2.313-2.228h-.5v-.5C12.188 2.825 10.328 1 8 1a4.53 4.53 0 0 0-2.941 1.1c-.757.652-1.153 1.438-1.153 2.055v.448l-.445.049C2.064 4.805 1 5.952 1 7.318 1 8.785 2.23 10 3.781 10H6a.5.5 0 0 1 0 1H3.781C1.708 11 0 9.366 0 7.318c0-1.763 1.266-3.223 2.942-3.593.143-.863.698-1.723 1.464-2.383" />
                                                <path
                                                    d="M7.646 15.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 14.293V5.5a.5.5 0 0 0-1 0v8.793l-2.146-2.147a.5.5 0 0 0-.708.708z" />
                                            </svg> Tải lá số
                                        </a>


                                        <a href="{{ route('laso.create') }}"
                                            style="background: linear-gradient(45deg, #2254AB, #4a90e2);border: none;padding: 10px 20px; border-radius: 25px;font-weight: 500; color: white; box-shadow: 0 4px 15px rgba(34, 84, 171, 0.3);"
                                            class="btn btn-primary me-2  mt-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                fill="currentColor" class="bi bi-cloud-plus" viewBox="0 0 16 16">
                                                <path fill-rule="evenodd"
                                                    d="M8 5.5a.5.5 0 0 1 .5.5v1.5H10a.5.5 0 0 1 0 1H8.5V10a.5.5 0 0 1-1 0V8.5H6a.5.5 0 0 1 0-1h1.5V6a.5.5 0 0 1 .5-.5" />
                                                <path
                                                    d="M4.406 3.342A5.53 5.53 0 0 1 8 2c2.69 0 4.923 2 5.166 4.579C14.758 6.804 16 8.137 16 9.773 16 11.569 14.502 13 12.687 13H3.781C1.708 13 0 11.366 0 9.318c0-1.763 1.266-3.223 2.942-3.593.143-.863.698-1.723 1.464-2.383m.653.757c-.757.653-1.153 1.44-1.153 2.056v.448l-.445.049C2.064 6.805 1 7.952 1 9.318 1 10.785 2.23 12 3.781 12h8.906C13.98 12 15 10.988 15 9.773c0-1.216-1.02-2.228-2.313-2.228h-.5v-.5C12.188 4.825 10.328 3 8 3a4.53 4.53 0 0 0-2.941 1.1z" />
                                            </svg> Tạo lá số mới
                                        </a>


                                    </div>
                                </div>
                            </div>
                        @else
                            <!-- Trường hợp đang load từ share link -->
                            <div class="box--bg-thang mb-3">
                                <div class="text-center py-5">
                                    <div class="loading-animation mb-4">
                                        <div class="brain-loading">
                                            <i class="fas fa-brain fa-3x text-primary pulse-animation"></i>
                                        </div>
                                        <div class="dots-loading mt-3">
                                            <span class="dot"></span>
                                            <span class="dot"></span>
                                            <span class="dot"></span>
                                        </div>
                                    </div>
                                    <h4 class="text-primary mb-2 fade-in">Đang tải lá số được chia sẻ...</h4>
                                    <p class="text-muted">Vui lòng đợi trong giây lát</p>
                                </div>
                            </div>
                        @endif
                        @include('la-so-tu-vi.thay')
                        <div id="luanGiaiResults"></div>


                        <!-- App Download Banner -->
                        @include('la-so-tu-vi.app')
                    </div>
                    @include('tools.siderbardetail')
                </div>
            </div>
        </div>
    </div>

    <!-- Customer Info Modal -->
    <div class="modal fade" id="customerModal" tabindex="-1" aria-labelledby="customerModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content" style="border-radius: 20px; overflow: hidden;">
                <!-- Modal Header -->
                <div class="modal-header"
                    style="background: linear-gradient(to right, #2268D3, #409BF1); color: white; padding: 20px 40px; border-bottom: none; display: flex; justify-content: center; align-items: center; position: relative;">
                    <h5 class="modal-title" id="customerModalLabel"
                        style="font-weight: 600; font-size: 20px; margin: 0; text-align: center; flex: 1;">
                        ĐĂNG KÝ NHẬN LUẬN GIẢI TỬ VI 1-1 CÙNG THẦY
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"
                        style="font-size: 20px;position: absolute;top: 14px;right: 12px;"></button>
                </div>

                <!-- Modal Body -->
                <div class="modal-body" style="padding: 0; background: #f8f9fa;">

                    <div class="row g-0">
                        <!-- Left Column - Form -->
                        <div class="col-lg-7" style="padding: 20px; position: relative;">

                            <div class="popup_rel">
                                Vui lòng điền thông tin để chúng tôi gửi luận giải chi tiết cho bạn
                            </div>
                            <form id="customerForm">
                                <!-- Họ tên và Giới tính -->
                                <div class="row mb-2">
                                    <div class="col-md-6">
                                        <label for="ho_ten" class="form-label"
                                            style="color: #333; font-weight: 500; margin-bottom: 8px;">Họ tên</label>
                                        <input type="text" class="form-control" id="ho_ten" name="ho_ten"
                                            placeholder="Nhập họ tên"
                                            style="border-radius: 8px; padding: 12px; border: 1px solid #ddd; font-size: 14px;"
                                            value="{{ $normalizedData['ho_ten'] ?? '' }}" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="gioi_tinh" class="form-label"
                                            style="color: #333; font-weight: 500; margin-bottom: 8px;">
                                            <i class="fas fa-venus-mars"
                                                style="margin-right: 8px; color: #4a90e2;"></i>Giới tính
                                        </label>
                                        <select class="form-select" id="gioi_tinh" name="gioi_tinh"
                                            style="border-radius: 8px; padding: 12px; border: 1px solid #ddd; font-size: 14px;"
                                            required>
                                            <option value="">Nữ</option>
                                            <option value="nam"
                                                {{ isset($normalizedData['gioi_tinh']) && $normalizedData['gioi_tinh'] == 'Nam' ? 'selected' : '' }}>
                                                Nam</option>
                                            <option value="nu"
                                                {{ isset($normalizedData['gioi_tinh']) && $normalizedData['gioi_tinh'] == 'Nữ' ? 'selected' : '' }}>
                                                Nữ</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Giờ sinh và Năm xem -->
                                <div class="row mb-2">
                                    <div class="col-md-6">
                                        <label for="gio_sinh" class="form-label"
                                            style="color: #333; font-weight: 500; margin-bottom: 8px;">
                                            <i class="fas fa-clock" style="margin-right: 8px; color: #4a90e2;"></i>Giờ
                                            sinh
                                        </label>
                                        <select class="form-select" id="gio_sinh" name="gio_sinh"
                                            style="border-radius: 8px; padding: 12px; border: 1px solid #ddd; font-size: 14px;">
                                            <option value="">Giờ Tý ( 23h - 1h )</option>
                                            <option value="Tý sớm"
                                                {{ isset($normalizedData['gio_am_sinh_chi_am']) && trim($normalizedData['gio_am_sinh_chi_am']) === 'Tý sớm' ? 'selected' : '' }}>
                                                Tý sớm (00:00 - 00:59)</option>
                                            <option value="Sửu"
                                                {{ isset($normalizedData['gio_am_sinh_chi_am']) && trim($normalizedData['gio_am_sinh_chi_am']) === 'Sửu' ? 'selected' : '' }}>
                                                Sửu (01:00 - 02:59)</option>
                                            <option value="Dần"
                                                {{ isset($normalizedData['gio_am_sinh_chi_am']) && trim($normalizedData['gio_am_sinh_chi_am']) === 'Dần' ? 'selected' : '' }}>
                                                Dần (03:00 - 04:59)</option>
                                            <option value="Mão"
                                                {{ isset($normalizedData['gio_am_sinh_chi_am']) && trim($normalizedData['gio_am_sinh_chi_am']) === 'Mão' ? 'selected' : '' }}>
                                                Mão (05:00 - 06:59)</option>
                                            <option value="Thìn"
                                                {{ isset($normalizedData['gio_am_sinh_chi_am']) && trim($normalizedData['gio_am_sinh_chi_am']) === 'Thìn' ? 'selected' : '' }}>
                                                Thìn (07:00 - 08:59)</option>
                                            <option value="Tỵ"
                                                {{ isset($normalizedData['gio_am_sinh_chi_am']) && trim($normalizedData['gio_am_sinh_chi_am']) === 'Tỵ' ? 'selected' : '' }}>
                                                Tỵ (09:00 - 10:59)</option>
                                            <option value="Ngọ"
                                                {{ isset($normalizedData['gio_am_sinh_chi_am']) && trim($normalizedData['gio_am_sinh_chi_am']) === 'Ngọ' ? 'selected' : '' }}>
                                                Ngọ (11:00 - 12:59)</option>
                                            <option value="Mùi"
                                                {{ isset($normalizedData['gio_am_sinh_chi_am']) && trim($normalizedData['gio_am_sinh_chi_am']) === 'Mùi' ? 'selected' : '' }}>
                                                Mùi (13:00 - 14:59)</option>
                                            <option value="Thân"
                                                {{ isset($normalizedData['gio_am_sinh_chi_am']) && trim($normalizedData['gio_am_sinh_chi_am']) === 'Thân' ? 'selected' : '' }}>
                                                Thân (15:00 - 16:59)</option>
                                            <option value="Dậu"
                                                {{ isset($normalizedData['gio_am_sinh_chi_am']) && trim($normalizedData['gio_am_sinh_chi_am']) === 'Dậu' ? 'selected' : '' }}>
                                                Dậu (17:00 - 18:59)</option>
                                            <option value="Tuất"
                                                {{ isset($normalizedData['gio_am_sinh_chi_am']) && trim($normalizedData['gio_am_sinh_chi_am']) === 'Tuất' ? 'selected' : '' }}>
                                                Tuất (19:00 - 20:59)</option>
                                            <option value="Hợi"
                                                {{ isset($normalizedData['gio_am_sinh_chi_am']) && trim($normalizedData['gio_am_sinh_chi_am']) === 'Hợi' ? 'selected' : '' }}>
                                                Hợi (21:00 - 22:59)</option>
                                            <option value="Tý muộn"
                                                {{ isset($normalizedData['gio_am_sinh_chi_am']) && trim($normalizedData['gio_am_sinh_chi_am']) === 'Tý muộn' ? 'selected' : '' }}>
                                                Tý muộn (23:00 - 23:59)</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="nam_xem" class="form-label"
                                            style="color: #333; font-weight: 500; margin-bottom: 8px;">
                                            <i class="fas fa-calendar-alt"
                                                style="margin-right: 8px; color: #4a90e2;"></i>Năm xem
                                        </label>
                                        <select class="form-select" id="nam_xem" name="nam_xem"
                                            style="border-radius: 8px; padding: 12px; border: 1px solid #ddd; font-size: 14px;"
                                            required>
                                            @php
                                                $currentYear = 1900;
                                                $years = range($currentYear, 2100);
                                            @endphp
                                            @foreach ($years as $year)
                                                <option value="{{ $year }}"
                                                    {{ isset($normalizedData['nam_xem']) && $normalizedData['nam_xem'] == $year ? 'selected' : ($year == $currentYear ? 'selected' : '') }}>
                                                    {{ $year }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <!-- Ngày sinh -->
                                <div class="row g-2 mb-2">
                                    <div class="col-4">
                                        <label class="form-label"
                                            style="color: #333; font-weight: 500; margin-bottom: 8px;">
                                            <i class="fas fa-birthday-cake"
                                                style="margin-right: 8px; color: #4a90e2;"></i>Ngày sinh
                                        </label>
                                        <select class="form-select" name="ngay"
                                            style="border-radius: 8px; padding: 12px; border: 1px solid #ddd; font-size: 14px;">
                                            @for ($i = 1; $i <= 31; $i++)
                                                <option value="{{ $i }}"
                                                    {{ isset($normalizedData['duong_lich']['day']) && $normalizedData['duong_lich']['day'] == $i ? 'selected' : '' }}>
                                                    {{ $i }}</option>
                                            @endfor
                                        </select>
                                    </div>
                                    <div class="col-4">
                                        <label class="form-label"
                                            style="color: #333; font-weight: 500; margin-bottom: 8px;">&nbsp;</label>
                                        <select class="form-select" name="thang"
                                            style="border-radius: 8px; padding: 12px; border: 1px solid #ddd; font-size: 14px;">
                                            @for ($i = 1; $i <= 12; $i++)
                                                <option value="{{ $i }}"
                                                    {{ isset($normalizedData['duong_lich']['month']) && $normalizedData['duong_lich']['month'] == $i ? 'selected' : '' }}>
                                                    {{ $i }}</option>
                                            @endfor
                                        </select>
                                    </div>
                                    <div class="col-4">
                                        <label class="form-label"
                                            style="color: #333; font-weight: 500; margin-bottom: 8px;">&nbsp;</label>
                                        <select class="form-select" name="nam"
                                            style="border-radius: 8px; padding: 12px; border: 1px solid #ddd; font-size: 14px;">
                                            @for ($i = 2025; $i >= 1950; $i--)
                                                <option value="{{ $i }}"
                                                    {{ isset($normalizedData['duong_lich']['year']) && $normalizedData['duong_lich']['year'] == $i ? 'selected' : '' }}>
                                                    {{ $i }}</option>
                                            @endfor
                                        </select>
                                    </div>
                                </div>

                                <!-- Số điện thoại và Email -->
                                <div class="row mb-2">
                                    <div class="col-md-6">
                                        <label for="so_dien_thoai" class="form-label"
                                            style="color: #333; font-weight: 500; margin-bottom: 8px;">
                                            Số điện thoại <span style="color: red;">*</span>
                                        </label>
                                        <input type="tel" class="form-control" id="so_dien_thoai"
                                            name="so_dien_thoai" placeholder="Nhập số điện thoại" pattern="[0-9]{10,12}"
                                            maxlength="12" inputmode="numeric"
                                            style="border-radius: 8px; padding: 12px; border: 1px solid #ddd; font-size: 14px;"
                                            required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="email" class="form-label"
                                            style="color: #333; font-weight: 500; margin-bottom: 8px;">Email</label>
                                        <input type="email" class="form-control" id="email" name="email"
                                            placeholder="Nhập email"
                                            style="border-radius: 8px; padding: 12px; border: 1px solid #ddd; font-size: 14px;">
                                    </div>
                                </div>
                                <div class="mb-2" style="font-size: 12px; font-style: italic;">
                                    <div>- Số điện thoại dùng để Bộ phận tư vấn liên hệ kết nối bạn với Thầy</div>
                                    <div>- Thông tin của bạn được bảo mật tuyệt đối, chỉ sử dụng cho mục đích tư vấn và luận
                                        giải</div>
                                </div>

                                <!-- Submit Button -->
                                <button type="button" id="submitCustomerForm"
                                    style="background: #2B7EE5; color: white; border: none; border-radius: 25px; padding: 15px 0; width: 100%; font-size: 16px; font-weight: 600; transition: all 0.3s ease;">
                                    Gửi đăng ký nhận luận giải
                                </button>

                                <!-- Message div - Position absolute để không đẩy layout -->
                                <div id="submitMessage"
                                    style="
                                    display: none;
                                    position: absolute;
                                    top: 10px;
                                    right: 10px;
                                    left: 10px;
                                    z-index: 1050;
                                    border-radius: 8px;
                                    padding: 12px;
                                    font-size: 14px;
                                    box-shadow: 0 4px 20px rgba(0,0,0,0.15);
                                ">
                                </div>
                                <div class="pt-2">
                                    <div class="text-center">
                                        -- HOẶC --
                                    </div>
                                    <div class="text-center pb-1">
                                        Trao đổi nhanh với bộ phận tư vấn
                                    </div>
                                    <div class="row g-2">
                                        <div class="col-lg-6">
                                            <a target="_blank" href="https://zalo.me/2874857294377143746">
                                                <div class="bocxx-z_contacrt">
                                                    <div>
                                                        <img src="{{ asset('/images/zalo.png') }}" class="img-fluid">
                                                    </div>
                                                    <div>
                                                        Đăng ký qua Zalo
                                                    </div>
                                                </div>
                                            </a>

                                        </div>
                                        <div class="col-lg-6">
                                            <a target="_blank" href="https://www.m.me/phonglich.official">
                                                <div class="bocxx-z_contacrt">
                                                    <div>
                                                        <img src="{{ asset('/images/messenger.png') }}" class="img-fluid">
                                                    </div>
                                                    <div>
                                                        Đăng ký qua Facebook
                                                    </div>
                                                </div>
                                            </a>

                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <!-- Right Column - Image -->
                        <div class="col-lg-5">

                            <div style="padding: 32px" class="h-100 w-100">
                                <div class="d-flex align-items-center justify-content-center h-100 w-100"
                                    style=" background-image: url(../images/form_laso.svg);
                                    background-repeat: no-repeat;
                                    background-size: cover;
                                    align-items: normal;
                                    background-position: center center;
                                    overflow: hidden;
                                    border-radius: 12px;
                                  
                                    ">

                                </div>
                            </div>
                            {{-- <img src="{{ asset('img/astrology-cosmic.jpg') }}" alt="Tử vi"
                                    style="max-width: 300px; width: 100%; border-radius: 15px; box-shadow: 0 20px 40px rgba(0,0,0,0.3);"
                                    onerror="this.style.display='none'"> --}}



                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Kiểm tra query parameters trực tiếp
            const urlParams = new URLSearchParams(window.location.search);
            const hasUserData = urlParams.has('ten') && urlParams.has('gt') && urlParams.has('ns');

            // Nếu không có session results nhưng không có user data, redirect về form
            @if (!isset($imageUrl) || !$imageUrl)
                if (!hasUserData) {
                    window.location.href = "{{ route('laso.create') }}";
                    return;
                }
            @endif

            // Image zoom functionality (if needed) - chỉ khi có ảnh
            @if (isset($imageUrl) && $imageUrl)
                const image = document.getElementById('laso-image');
                const container = document.getElementById('img-zoom-container');
                const lens = document.getElementById('img-zoom-lens');
                const result = document.getElementById('img-zoom-result');
                const realImgUrl = "{{ route('laso.image_proxy', ['url' => $imageUrl]) }}";

                const img = new Image();
                img.src = realImgUrl;

                img.onload = function() {
                    // Gắn src vào ảnh hiển thị
                    image.src = realImgUrl;
                    image.style.display = "block";
                    // Ẩn khung
                    document.getElementById("laso-frame").style.display = "none";
                    // Ẩn loading
                    document.getElementById("laso-loading").style.display = "none";
                };
            @endif

            // Kiểm tra DB trước, có thì hiển thị, không có thì gọi API
            @if (isset($cachedLuanGiai) && $cachedLuanGiai)
                // Có trong DB - hiển thị ngay
                setTimeout(function() {

                    const cachedContent = {
                        responseObject: @json($cachedLuanGiai->luan_giai_content)
                    };
                    showLuanGiaiResults(cachedContent);
                }, 500);
            @else
                // Không có trong DB - gọi API luận giải
                setTimeout(function() {

                    autoRunLuanGiai();
                }, 500);
            @endif

            // Function tự động chạy luận giải - gọi API
            function autoRunLuanGiai() {
                const luanGiaiBtn = document.getElementById('luanGiaiBtn');
                if (luanGiaiBtn) {
                    // Set button thành trạng thái loading
                    luanGiaiBtn.disabled = true;
                    luanGiaiBtn.innerHTML =
                        '<i class="fas fa-spinner fa-spin"></i>Bạn đợi một chút nhé! Chúng tôi đang phân tích lá số và chuẩn bị luận giải cho bạn';
                }

                // Hiển thị thông báo đang xử lý
                showLuanGiaiLoading();

                // Gọi API luận giải (server sẽ kiểm tra database cache)
                fetch('{{ route('laso.luan_giai') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({})
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Hiển thị kết quả luận giải
                            showLuanGiaiResults(data.data);


                        } else {
                            let errorMsg = 'Lỗi: ' + data.message;
                            if (data.debug) {
                                console.error('API Debug Info:', data.debug);
                                if (data.debug.status) {
                                    errorMsg += '\nHTTP Status: ' + data.debug.status;
                                }
                                if (data.debug.missing_field) {
                                    errorMsg += '\nThiếu trường: ' + data.debug.missing_field;
                                }
                            }
                            showLuanGiaiError(errorMsg);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showLuanGiaiError('Đã xảy ra lỗi khi luận giải. Vui lòng thử lại.');
                    })
                    .finally(() => {
                        // Khôi phục button
                        if (luanGiaiBtn) {
                            luanGiaiBtn.disabled = false;
                            luanGiaiBtn.innerHTML = '<i class="fas fa-brain"></i> Luận giải lại';
                        }
                    });
            }

            // Luận giải functionality cho nút click
            const luanGiaiBtn = document.getElementById('luanGiaiBtn');
            if (luanGiaiBtn) {
                luanGiaiBtn.addEventListener('click', function() {
                    autoRunLuanGiai();
                });
            }


            function showLuanGiaiLoading() {
                let resultsSection = document.getElementById('luanGiaiResults');
                if (!resultsSection) {
                    resultsSection = document.createElement('div');
                    resultsSection.id = 'luanGiaiResults';
                    resultsSection.className = 'card mt-4';

                    // Thêm vào vị trí cố định
                    const targetElement = document.getElementById('luanGiaiResults');
                    if (targetElement) {
                        targetElement.parentNode.replaceChild(resultsSection, targetElement);
                    }
                }

                // Hiển thị loading
                const loadingHtml = `
                    <div class="box--bg-thang mt-3 mb-3">
                        <div class="loading-container text-center py-5">
                            <div class="loading-animation mb-4">
                                <div class="brain-loading">
                                    <i class="fas fa-brain fa-3x text-primary pulse-animation"></i>
                                </div>
                                <div class="dots-loading mt-3">
                                    <span class="dot"></span>
                                    <span class="dot"></span>
                                    <span class="dot"></span>
                                </div>
                            </div>
                            <h4 class="text-primary mb-2 fade-in">Bạn đợi một chút nhé! Chúng tôi đang phân tích lá số và chuẩn bị luận giải cho bạn</h4>
                           
                        </div>
                    </div>

                    <style>
                       

                        .dots-loading {
                            display: flex;
                            justify-content: center;
                            gap: 8px;
                        }

                        .dot {
                            width: 8px;
                            height: 8px;
                            border-radius: 50%;
                            background-color: #2254AB;
                            animation: dotPulse 1.5s infinite ease-in-out;
                        }
                    </style>
                `;

                resultsSection.innerHTML = loadingHtml;

                // Scroll đến loading section

            }

            function showLuanGiaiError(errorMsg) {
                let resultsSection = document.getElementById('luanGiaiResults');
                if (!resultsSection) return;

                // Hiển thị error
                const errorHtml = `
                    <div class="box--bg-thang mt-3 mb-3">
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Lỗi luận giải:</strong><br>
                           Có lỗi xảy ra bạn vui lòng thử lại sau nhé!
                        </div>
                        <div class="text-center">
                            <button onclick="autoRunLuanGiai()" class="btn btn-outline-primary">
                                <i class="fas fa-redo"></i> Thử lại
                            </button>
                        </div>
                    </div>
                `;

                resultsSection.innerHTML = errorHtml;
            }

            function showLuanGiaiResults(data) {
                // Tạo hoặc cập nhật section hiển thị kết quả luận giải
                let resultsSection = document.getElementById('luanGiaiResults');
                if (!resultsSection) {
                    resultsSection = document.createElement('div');
                    resultsSection.id = 'luanGiaiResults';
                    resultsSection.className = 'card mt-4';

                    // Thêm vào vị trí cố định
                    const targetElement = document.getElementById('luanGiaiResults');
                    if (targetElement) {
                        targetElement.parentNode.replaceChild(resultsSection, targetElement);
                    }
                }


                // Parse nội dung luận giải từ responseObject
                let luanGiaiContent = '';
                if (typeof data === 'string') {
                    luanGiaiContent = data;
                } else if (data && data.responseObject) {
                    luanGiaiContent = data.responseObject;
                } else if (data && typeof data === 'object') {
                    luanGiaiContent = JSON.stringify(data, null, 2);
                } else {
                    luanGiaiContent = 'Không có nội dung luận giải';
                }

                // Format nội dung - chuyển đổi từ markdown/text thành HTML
                const formattedContent = formatLuanGiaiContent(luanGiaiContent);

                // Tạo HTML hiển thị kết quả
                const resultsHtml = `

                    <div class="box--bg-thang mt-3 mb-3">
                        <h3 class="text-center mb-4" style="color: #2254AB; font-weight: 600;">
                            <i class="fas fa-brain me-2"></i>Luận Giải Tổng Quan
                        </h3>
                        <div class="luan-giai-container">
                            <div class="text-box-tong-quan luan-giai-content" id="luanGiaiContent">
                                ${formattedContent}
                            </div>
                            <div class="luan-giai-overlay" id="luanGiaiOverlay">
                                <button class="btn btn-primary xem-them-btn" onclick="showFullLuanGiai()">
                                     Xem thêm <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye" viewBox="0 0 16 16">
  <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8M1.173 8a13 13 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5s3.879 1.168 5.168 2.457A13 13 0 0 1 14.828 8q-.086.13-.195.288c-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5s-3.879-1.168-5.168-2.457A13 13 0 0 1 1.172 8z"/>
  <path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5M4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0"/>
</svg>
                                </button>
                            </div>
                        </div>
                    </div>
                `;

                resultsSection.innerHTML = resultsHtml;

                // Kiểm tra xem có cần hiển thị nút "Xem thêm" không
                setTimeout(() => {
                    checkIfNeedExpandButton();
                }, 100);
            }

            // Function để kiểm tra và hiển thị nút xem thêm
            function checkIfNeedExpandButton() {
                const content = document.getElementById('luanGiaiContent');
                const overlay = document.getElementById('luanGiaiOverlay');

                if (content && overlay) {
                    // Kiểm tra nếu nội dung cao hơn max-height
                    if (content.scrollHeight > 300) {
                        overlay.style.display = 'flex';
                    } else {
                        overlay.style.display = 'none';
                    }
                }
            }


            function formatLuanGiaiContent(content) {
                if (!content || typeof content !== 'string') {
                    return '<p class="text-muted">Không có nội dung luận giải</p>';
                }

                // Escape HTML để tránh XSS
                const escapeHtml = (text) => {
                    const div = document.createElement('div');
                    div.textContent = text;
                    return div.innerHTML;
                };

                const escaped = escapeHtml(content);

                // Format the content
                let formatted = escaped
                    // Convert **text** thành <strong>text</strong>
                    .replace(/\*\*(.*?)\*\*/g, '<strong class="text-primary">$1</strong>')
                    // Convert đoạn văn thành paragraphs
                    .replace(/\n\n/g, '</p><p>')
                    // Xử lý line breaks
                    .replace(/\n/g, '<br>');

                // Wrap trong paragraphs
                formatted = '<p>' + formatted + '</p>';

                // Cleanup empty paragraphs
                formatted = formatted.replace(/<p><\/p>/g, '');
                formatted = formatted.replace(/<p><br><\/p>/g, '');

                return formatted;
            }

            // Thêm query parameters vào URL khi load trang nếu có
            @if (isset($urlParams) && !empty($urlParams))
                if (!hasUserData) {
                    @foreach ($urlParams as $key => $value)
                        @if ($value)
                            urlParams.set('{{ $key }}', '{{ $value }}');
                        @endif
                    @endforeach
                    window.history.replaceState({}, document.title, window.location.pathname + '?' + urlParams
                        .toString());
                }
            @endif

            // Customer Modal Functions
            window.openCustomerModal = function() {
                // Debug: Log the current values
                @if (isset($normalizedData['gio_am_sinh_chi_am']))
                    console.log('Debug - gio_am_sinh_chi_am value:',
                        '{{ $normalizedData['gio_am_sinh_chi_am'] }}');
                    console.log('Debug - Trimmed value:', '{{ trim($normalizedData['gio_am_sinh_chi_am']) }}');
                @endif

                @if (isset($normalizedData['gio_am_sinh_am']))
                    console.log('Debug - gio_am_sinh_am value:', '{{ $normalizedData['gio_am_sinh_am'] }}');
                @endif

                const modal = new bootstrap.Modal(document.getElementById('customerModal'));
                modal.show();

                // Check which option is selected after modal opens
                setTimeout(() => {
                    const gioSinhSelect = document.getElementById('gio_sinh');
                    console.log('Debug - Selected value:', gioSinhSelect.value);
                    console.log('Debug - Selected index:', gioSinhSelect.selectedIndex);
                }, 100);
            };

            // Add event listeners to remove error styling when user starts typing
            const inputFields = ['ho_ten', 'gioi_tinh', 'nam_xem', 'so_dien_thoai', 'email', 'gio_sinh'];
            inputFields.forEach(fieldId => {
                const field = document.getElementById(fieldId);
                if (field) {
                    field.addEventListener('input', function() {
                        this.classList.remove('is-invalid');
                    });
                    field.addEventListener('change', function() {
                        this.classList.remove('is-invalid');
                    });
                }
            });

            // Add phone number validation - only numbers and max 12 digits
            const phoneField = document.getElementById('so_dien_thoai');
            if (phoneField) {
                phoneField.addEventListener('input', function(e) {
                    // Remove non-numeric characters
                    let value = e.target.value.replace(/[^0-9]/g, '');

                    // Limit to 12 digits
                    if (value.length > 12) {
                        value = value.substring(0, 12);
                    }

                    e.target.value = value;
                });

                phoneField.addEventListener('keypress', function(e) {
                    // Only allow numeric keys, backspace, delete, tab, etc.
                    if (!/[0-9]/.test(e.key) && !['Backspace', 'Delete', 'Tab', 'ArrowLeft', 'ArrowRight']
                        .includes(e.key)) {
                        e.preventDefault();
                    }
                });
            }

            // Add event listeners for birth date dropdowns (by name)
            const birthDateFields = ['ngay', 'thang', 'nam'];
            birthDateFields.forEach(fieldName => {
                const field = document.querySelector(`select[name="${fieldName}"]`);
                if (field) {
                    field.addEventListener('change', function() {
                        this.classList.remove('is-invalid');
                    });
                }
            });

            // Submit customer form
            document.getElementById('submitCustomerForm').addEventListener('click', function() {
                submitCustomerForm();
            });

            function submitCustomerForm() {
                const form = document.getElementById('customerForm');
                const formData = new FormData(form);
                const submitBtn = document.getElementById('submitCustomerForm');
                const messageDiv = document.getElementById('submitMessage');

                // Validate required fields
                const requiredFields = ['ho_ten', 'gioi_tinh', 'nam_xem', 'so_dien_thoai'];
                let isValid = true;
                let errorMessage = 'Vui lòng điền đầy đủ thông tin bắt buộc';

                requiredFields.forEach(field => {
                    const input = document.getElementById(field);
                    if (!input || !input.value.trim()) {
                        if (input) input.classList.add('is-invalid');
                        isValid = false;

                        // Specific error message for phone number
                        if (field === 'so_dien_thoai') {
                            errorMessage = 'Vui lòng nhập số điện thoại';
                        }
                    } else {
                        input.classList.remove('is-invalid');
                    }
                });

                // Additional phone number validation
                const phoneInput = document.getElementById('so_dien_thoai');
                if (phoneInput && phoneInput.value.trim()) {
                    const phoneValue = phoneInput.value.trim();
                    // Check if phone number is only digits and between 10-12 characters
                    if (!/^[0-9]{10,12}$/.test(phoneValue)) {
                        phoneInput.classList.add('is-invalid');
                        isValid = false;
                        errorMessage = 'Số điện thoại phải có từ 10-12 chữ số';
                    }
                }

                if (!isValid) {
                    showMessage(errorMessage, 'error');
                    return;
                }

                // Disable button and show loading
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Đang gửi...';

                // Prepare data for API
                const ngay = formData.get('ngay');
                const thang = formData.get('thang');
                const nam = formData.get('nam');
                const ngay_sinh = `${nam}-${thang.toString().padStart(2, '0')}-${ngay.toString().padStart(2, '0')}`;

                const customerData = {
                    ho_ten: formData.get('ho_ten'),
                    gioi_tinh: formData.get('gioi_tinh'),
                    ngay_sinh: ngay_sinh,
                    gio_sinh: formData.get('gio_sinh') || null,
                    nam_xem: parseInt(formData.get('nam_xem')),
                    so_dien_thoai: formData.get('so_dien_thoai'),
                    email: formData.get('email') || null
                };

                // Send data to API
                fetch('/api/customer', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify(customerData)
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            showMessage(
                                'Thông tin đã được gửi thành công! Chúng tôi sẽ liên hệ với bạn sớm nhất.',
                                'success');
                            setTimeout(() => {
                                const modal = bootstrap.Modal.getInstance(document.getElementById(
                                    'customerModal'));
                                modal.hide();
                                form.reset();
                            }, 2000);
                        } else {
                            let errorMessage = 'Có lỗi xảy ra: ' + data.message;
                            if (data.errors) {
                                const errorList = Object.values(data.errors).flat();
                                errorMessage += '<br>' + errorList.join('<br>');
                            }
                            showMessage(errorMessage, 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showMessage('Có lỗi xảy ra khi gửi thông tin. Vui lòng thử lại.', 'error');
                    })
                    .finally(() => {
                        // Restore button
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = '<i class="fas fa-paper-plane me-2"></i>Gửi thông tin';
                    });
            }

            function showMessage(message, type) {
                const messageDiv = document.getElementById('submitMessage');
                messageDiv.style.display = 'block';
                messageDiv.className = `alert alert-${type === 'success' ? 'success' : 'danger'}`;
                messageDiv.innerHTML = '<i class="fas fa-' + (type === 'success' ? 'check-circle' :
                    'exclamation-triangle') + ' me-2"></i>' + message;

                // Auto hide messages
                const hideTimeout = type === 'success' ? 3000 : 5000; // Success: 3s, Error: 5s
                setTimeout(() => {
                    messageDiv.style.display = 'none';
                }, hideTimeout);
            }

        });

        // Global function để xử lý nút "Xem thêm"
        function showFullLuanGiai() {
            const content = document.getElementById('luanGiaiContent');
            const overlay = document.getElementById('luanGiaiOverlay');

            if (content && overlay) {
                content.classList.add('expanded');
                overlay.classList.add('hidden');

                // Scroll xuống một chút để người dùng thấy nội dung mở rộng
                setTimeout(() => {
                    content.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }, 100);
            }
        }
    </script>
@endpush
