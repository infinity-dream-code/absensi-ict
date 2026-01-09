@extends('layouts.app')

@section('title', 'Absensi - Sistem Absensi')

@section('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    #locationConfirmMap {
        height: 300px;
        width: 100%;
        border-radius: 8px;
        margin: 15px 0;
        z-index: 1;
    }
    .swal2-popup {
        max-width: 90% !important;
        width: 500px !important;
    }
    @media (max-width: 640px) {
        .swal2-popup {
            width: 95% !important;
        }
        #locationConfirmMap {
            height: 250px;
        }
    }
    .attendance-container {
        display: flex;
        justify-content: center;
        align-items: flex-start;
        min-height: 80vh;
        padding: 2rem 1rem;
    }
    /* Ensure inputs/textarea don't overflow the card */
    .form-control,
    .form-textarea {
        box-sizing: border-box;
        max-width: 100%;
    }
    .attendance-wrapper {
        width: 100%;
        max-width: 42rem;
    }
    .card-main {
        background: white;
        border-radius: 1rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        border: 1px solid #e5e7eb;
        overflow: hidden;
    }
    .card-header {
        padding: 1.5rem;
        border-bottom: 1px solid #f3f4f6;
    }
    .header-content {
        display: flex;
        align-items: center;
        gap: 1rem;
    }
    .icon-box {
        width: 2.5rem;
        height: 2.5rem;
        background: #eef2ff;
        border-radius: 0.75rem;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .card-body {
        padding: 1.5rem;
    }
    .form-group {
        margin-bottom: 1.25rem;
    }
    .form-label {
        display: block;
        font-size: 0.875rem;
        font-weight: 500;
        color: #374151;
        margin-bottom: 0.5rem;
    }
    .form-control {
        width: 100%;
        padding: 0.625rem 1rem;
        border: 1px solid #d1d5db;
        border-radius: 0.5rem;
        font-size: 0.875rem;
        transition: all 0.2s;
    }
    .form-control:focus {
        outline: none;
        border-color: #6366f1;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
    }
    .form-textarea {
        width: 100%;
        padding: 0.625rem 1rem;
        border: 1px solid #d1d5db;
        border-radius: 0.5rem;
        font-size: 0.875rem;
        resize: none;
        transition: all 0.2s;
    }
    .form-textarea:focus {
        outline: none;
        border-color: #6366f1;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
    }
    .btn-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 0.75rem;
        margin-top: 1.5rem;
    }
    .btn {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        padding: 0.875rem 1rem;
        border-radius: 0.5rem;
        font-weight: 500;
        font-size: 0.875rem;
        cursor: pointer;
        border: none;
        transition: all 0.2s;
    }
    .btn-success {
        background: #10b981;
        color: white;
    }
    .btn-success:hover:not(:disabled) {
        background: #059669;
    }
    .btn-danger {
        background: #f43f5e;
        color: white;
    }
    .btn-danger:hover:not(:disabled) {
        background: #e11d48;
    }
    .btn:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }
    .status-card {
        background: white;
        border-radius: 1rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        border: 1px solid #e5e7eb;
        padding: 1.5rem;
        margin-top: 1.25rem;
    }
    .status-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
        margin-top: 1rem;
    }
    .status-item {
        background: #f9fafb;
        border-radius: 0.75rem;
        padding: 1rem;
    }
    .preview-image {
        max-width: 100%;
        height: auto;
        max-height: 12rem;
        border-radius: 0.5rem;
        margin-top: 0.75rem;
    }
    .text-small {
        font-size: 0.75rem;
        color: #6b7280;
        margin-top: 0.375rem;
    }
    
    /* SweetAlert2 Custom Styling */
    .swal2-popup {
        border-radius: 1rem !important;
    }
    
    .swal2-confirm,
    .swal2-confirm-custom {
        background-color: #6366f1 !important;
        border: none !important;
        border-radius: 0.5rem !important;
        padding: 0.75rem 2rem !important;
        font-size: 0.875rem !important;
        font-weight: 600 !important;
        color: white !important;
        box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3) !important;
        transition: all 0.2s !important;
        opacity: 1 !important;
        visibility: visible !important;
        display: inline-block !important;
        cursor: pointer !important;
    }
    
    .swal2-confirm:hover,
    .swal2-confirm-custom:hover {
        background-color: #4f46e5 !important;
        box-shadow: 0 6px 16px rgba(99, 102, 241, 0.4) !important;
        transform: translateY(-1px) !important;
    }
    
    .swal2-confirm:focus,
    .swal2-confirm-custom:focus {
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.3) !important;
        outline: none !important;
    }
    
    .swal2-cancel,
    .swal2-cancel-custom {
        background-color: #ef4444 !important;
        border: none !important;
        border-radius: 0.5rem !important;
        padding: 0.75rem 2rem !important;
        font-size: 0.875rem !important;
        font-weight: 600 !important;
        color: white !important;
        box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3) !important;
        transition: all 0.2s !important;
        opacity: 1 !important;
        visibility: visible !important;
        display: inline-block !important;
        cursor: pointer !important;
    }
    
    .swal2-cancel:hover,
    .swal2-cancel-custom:hover {
        background-color: #dc2626 !important;
        box-shadow: 0 6px 16px rgba(239, 68, 68, 0.4) !important;
        transform: translateY(-1px) !important;
    }
    
    .swal2-cancel:focus,
    .swal2-cancel-custom:focus {
        box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.3) !important;
        outline: none !important;
    }
    
    .swal2-actions {
        gap: 0.75rem !important;
        margin-top: 1.5rem !important;
    }
    
    .swal2-title {
        font-size: 1.5rem !important;
        font-weight: 600 !important;
        color: #111827 !important;
        margin-bottom: 0.5rem !important;
    }
    
    .swal2-html-container {
        font-size: 0.875rem !important;
        color: #6b7280 !important;
        margin-top: 0.5rem !important;
    }
    
    @keyframes pulse {
        0%, 100% {
            transform: scale(1);
            opacity: 1;
        }
        50% {
            transform: scale(1.1);
            opacity: 0.8;
        }
    }
    
    .swal2-loading-popup {
        text-align: center;
    }
    
    .swal2-loading-popup .swal2-loader {
        border: 4px solid #f3f4f6;
        border-top: 4px solid #667eea;
        border-radius: 50%;
        width: 40px;
        height: 40px;
        animation: spin 1s linear infinite;
        margin: 0 auto 20px;
        display: block;
    }
    
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    
    .swal2-loading-popup .swal2-html-container {
        padding: 0 !important;
        text-align: center;
    }
    
    .swal2-loading-html {
        text-align: center !important;
    }
</style>
@endsection

@section('content')
<div class="attendance-container">
    <div class="attendance-wrapper">
        <div class="card-main">
            <div class="card-header">
                <div class="header-content">
                    <div class="icon-box">
                        <i class="fas fa-calendar-day" style="color: #6366f1;"></i>
                    </div>
                    <div>
                        <h3 style="font-size: 1.25rem; font-weight: 600; color: #111827; margin: 0;">Absensi Hari Ini</h3>
                        <p style="font-size: 0.875rem; color: #6b7280; margin: 0.25rem 0 0 0;">
                            {{ \Carbon\Carbon::now('Asia/Jakarta')->locale('id')->isoFormat('dddd, D MMMM YYYY') }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <form id="attendanceForm">
                    <div class="form-group">
                        <label for="work_type" class="form-label">Jenis Kerja</label>
                        <select class="form-control" id="work_type" name="work_type" required>
                            <option value="">Pilih jenis kerja</option>
                            <option value="WFA" {{ old('work_type', $attendance->work_type ?? '') == 'WFA' ? 'selected' : '' }}>WFA (Work From Anywhere)</option>
                            <option value="WFO" {{ old('work_type', $attendance->work_type ?? '') == 'WFO' ? 'selected' : '' }}>WFO (Work From Office)</option>
                            <option value="WFH" {{ old('work_type', $attendance->work_type ?? '') == 'WFH' ? 'selected' : '' }}>WFH (Work From Home)</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="notes" class="form-label">Catatan</label>
                        <textarea class="form-textarea" id="notes" name="notes" rows="3" placeholder="Tambahkan catatan (opsional)">{{ old('notes', $attendance->notes ?? '') }}</textarea>
                    </div>

                    <div class="form-group">
                        <label for="image" class="form-label">Gambar (Opsional)</label>
                        <input type="file" class="form-control" id="image" name="image" accept="image/*" style="cursor: pointer;">
                        <p class="text-small">Format: JPG, PNG, GIF (Max: 2MB)</p>
                        <div id="imagePreview"></div>
                    </div>

                    <div class="btn-grid">
                        <button type="button" id="checkInBtn" class="btn btn-success" {{ isset($attendance) && $attendance->check_in ? 'disabled' : '' }}>
                            <i class="fas fa-sign-in-alt"></i>
                            <span>{{ isset($attendance) && $attendance->check_in ? 'Sudah Check-In' : 'Check-In' }}</span>
                        </button>
                        
                        <button type="button" id="checkOutBtn" class="btn btn-danger" {{ isset($attendance) && $attendance->check_out ? 'disabled' : '' }}>
                            <i class="fas fa-sign-out-alt"></i>
                            <span>{{ isset($attendance) && $attendance->check_out ? 'Sudah Check-Out' : 'Check-Out' }}</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        @if(isset($attendance))
        <div class="status-card">
            <h6 style="font-size: 1rem; font-weight: 600; color: #111827; margin: 0 0 1rem 0;">Status Absensi</h6>
            <div class="status-grid">
                <div class="status-item">
                    <p style="font-size: 0.75rem; font-weight: 500; color: #6b7280; margin: 0 0 0.25rem 0;">Check-In</p>
                    <p style="font-size: 1.25rem; font-weight: 600; margin: 0;">
                        @if($attendance->check_in)
                            <span style="color: #10b981;">{{ \Carbon\Carbon::parse($attendance->check_in)->setTimezone('Asia/Jakarta')->format('H:i:s') }}</span>
                        @else
                            <span style="color: #9ca3af;">-</span>
                        @endif
                    </p>
                </div>
                <div class="status-item">
                    <p style="font-size: 0.75rem; font-weight: 500; color: #6b7280; margin: 0 0 0.25rem 0;">Check-Out</p>
                    <p style="font-size: 1.25rem; font-weight: 600; margin: 0;">
                        @if($attendance->check_out)
                            <span style="color: #10b981;">{{ \Carbon\Carbon::parse($attendance->check_out)->setTimezone('Asia/Jakarta')->format('H:i:s') }}</span>
                        @else
                            <span style="color: #9ca3af;">-</span>
                        @endif
                    </p>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

@section('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    document.getElementById('image').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.getElementById('imagePreview');
                preview.innerHTML = `
                    <div style="position: relative; display: inline-block; margin-top: 0.75rem;">
                        <img src="${e.target.result}" alt="Preview" style="max-width: 100%; height: auto; max-height: 12rem; border-radius: 0.5rem;">
                        <button type="button" onclick="this.parentElement.parentElement.innerHTML=''" style="position: absolute; top: 0.5rem; right: 0.5rem; background: #f43f5e; color: white; border-radius: 9999px; width: 1.75rem; height: 1.75rem; display: flex; align-items: center; justify-content: center; border: none; cursor: pointer;">
                            <i class="fas fa-times" style="font-size: 0.75rem;"></i>
                        </button>
                    </div>
                `;
            };
            reader.readAsDataURL(file);
        }
    });

    document.getElementById('checkInBtn').addEventListener('click', function() {
        const workType = document.getElementById('work_type').value;
        
        if (!workType) {
            Swal.fire({
                icon: 'warning',
                title: 'Peringatan!',
                text: 'Silakan pilih jenis kerja terlebih dahulu!',
                confirmButtonColor: '#6366f1'
            });
            return;
        }

        // Request location if WFO
        if (workType === 'WFO') {
            if (navigator.geolocation) {
                let retryCount = 0;
                const maxRetries = 1; // Kurangi retry menjadi 1x saja
                const startTime = Date.now();
                
                console.log('[Geolocation] Mulai request lokasi...');
                
                function attemptGetLocation() {
                    const attemptStartTime = Date.now();
                    console.log('[Geolocation] Attempt #' + (retryCount + 1) + ' dimulai');
                    
                    Swal.fire({
                        title: 'Mengambil Lokasi',
                        html: '<div style="text-align: center; padding: 12px 0;"><div class="swal2-loader" style="margin: 0 auto 12px;"></div><p style="margin-top: 8px; color: #6b7280; font-size: 14px; margin-bottom: 0;">Mohon izinkan akses lokasi untuk validasi absensi WFO</p></div>',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        showConfirmButton: false,
                        customClass: {
                            popup: 'swal2-loading-popup',
                            htmlContainer: 'swal2-loading-html'
                        }
                    });

                    const geoOptions = {
                        enableHighAccuracy: false,
                        timeout: 10000, // Kurangi menjadi 10 detik
                        maximumAge: 300000 // Cache 5 menit
                    };
                    
                    console.log('[Geolocation] Options:', geoOptions);

                    navigator.geolocation.getCurrentPosition(
                        function(position) {
                            const elapsed = Date.now() - attemptStartTime;
                            const totalElapsed = Date.now() - startTime;
                            console.log('[Geolocation] SUCCESS - Lat:', position.coords.latitude, 'Lng:', position.coords.longitude);
                            console.log('[Geolocation] Accuracy:', position.coords.accuracy, 'meters');
                            console.log('[Geolocation] Waktu attempt:', elapsed + 'ms');
                            console.log('[Geolocation] Total waktu:', totalElapsed + 'ms');
                            
                            Swal.close();
                            
                            // Tampilkan lokasi dulu untuk konfirmasi
                            showLocationConfirmation(position.coords.latitude, position.coords.longitude, position.coords.accuracy);
                        },
                        function(error) {
                            const elapsed = Date.now() - attemptStartTime;
                            const totalElapsed = Date.now() - startTime;
                            
                            console.error('[Geolocation] ERROR - Code:', error.code);
                            console.error('[Geolocation] ERROR - Message:', error.message);
                            console.error('[Geolocation] Waktu attempt:', elapsed + 'ms');
                            console.error('[Geolocation] Total waktu:', totalElapsed + 'ms');
                            
                            retryCount++;
                            let errorMessage = 'Tidak dapat mendapatkan lokasi.';
                            let errorCode = '';
                            
                            if (error.code === error.PERMISSION_DENIED) {
                                errorCode = 'PERMISSION_DENIED';
                                errorMessage = 'Akses lokasi ditolak. Mohon izinkan akses lokasi untuk absensi WFO.';
                                console.error('[Geolocation] User menolak akses lokasi');
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Akses Lokasi Ditolak',
                                    text: errorMessage,
                                    confirmButtonColor: '#6366f1',
                                    confirmButtonText: 'OK'
                                });
                                return;
                            } else if (error.code === error.POSITION_UNAVAILABLE) {
                                errorCode = 'POSITION_UNAVAILABLE';
                                errorMessage = 'Informasi lokasi tidak tersedia.';
                                console.error('[Geolocation] Lokasi tidak tersedia');
                            } else if (error.code === error.TIMEOUT) {
                                errorCode = 'TIMEOUT';
                                errorMessage = 'Waktu permintaan lokasi habis (' + (elapsed / 1000).toFixed(1) + 's).';
                                console.error('[Geolocation] Timeout setelah', elapsed + 'ms');
                                
                                if (retryCount <= maxRetries) {
                                    console.log('[Geolocation] Retry attempt #' + (retryCount + 1));
                                    Swal.close();
                                    setTimeout(function() {
                                        attemptGetLocation();
                                    }, 1000);
                                    return;
                                }
                            }
                            
                            console.error('[Geolocation] Final error - Code:', errorCode, 'Retry count:', retryCount);
                            
                            // Jika sudah retry maksimal atau error lain, tanya user apakah mau lanjut tanpa lokasi
                            Swal.fire({
                                icon: 'warning',
                                title: 'Gagal Mengambil Lokasi',
                                html: '<p style="margin-bottom: 8px;">' + errorMessage + '</p><p style="font-size: 12px; color: #9ca3af; margin-bottom: 12px;">Error: ' + errorCode + ' (Waktu: ' + (totalElapsed / 1000).toFixed(1) + 's)</p><p style="font-size: 13px; color: #6b7280;">Lanjutkan check-in tanpa validasi lokasi?</p>',
                                showCancelButton: true,
                                confirmButtonColor: '#6366f1',
                                cancelButtonColor: '#6b7280',
                                confirmButtonText: 'Ya, Lanjutkan',
                                cancelButtonText: 'Batal',
                                reverseButtons: true
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    console.log('[Geolocation] User memilih lanjut tanpa lokasi');
                                    submitCheckIn(null, null);
                                } else {
                                    console.log('[Geolocation] User membatalkan');
                                }
                            });
                        },
                        geoOptions
                    );
                }
                
                attemptGetLocation();
            } else {
                console.error('[Geolocation] Browser tidak mendukung geolocation API');
                Swal.fire({
                    icon: 'error',
                    title: 'Browser Tidak Mendukung',
                    text: 'Browser Anda tidak mendukung geolocation. Silakan gunakan browser yang lebih baru.',
                    confirmButtonColor: '#6366f1'
                });
            }
        } else {
            // For WFA and WFH, submit without location
            console.log('[Geolocation] Work type bukan WFO, skip geolocation');
            submitCheckIn(null, null);
        }
    });

    // Fungsi untuk menghitung jarak (Haversine formula)
    function calculateDistance(lat1, lon1, lat2, lon2) {
        const R = 6371; // Radius bumi dalam km
        const dLat = (lat2 - lat1) * Math.PI / 180;
        const dLon = (lon2 - lon1) * Math.PI / 180;
        const a = 
            Math.sin(dLat/2) * Math.sin(dLat/2) +
            Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
            Math.sin(dLon/2) * Math.sin(dLon/2);
        const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
        return R * c; // Jarak dalam km
    }

    // Tampilkan konfirmasi lokasi sebelum check-in
    function showLocationConfirmation(latitude, longitude, accuracy) {
        const officeLat = {{ $settings->latitude ?? 'null' }};
        const officeLng = {{ $settings->longitude ?? 'null' }};
        const officeRadius = {{ $settings->radius ?? 100 }}; // dalam meter
        
        let locationStatus = '';
        let locationStatusColor = '#059669';
        let isLocationValid = true;
        
        // Validasi lokasi jika office location sudah dikonfigurasi
        if (officeLat && officeLng && officeRadius) {
            const distance = calculateDistance(officeLat, officeLng, latitude, longitude);
            const distanceMeters = distance * 1000;
            
            if (distanceMeters > officeRadius) {
                locationStatus = 'Lokasi Anda berada di luar jangkauan kantor (' + distanceMeters.toFixed(0) + 'm dari kantor, radius: ' + officeRadius + 'm)';
                locationStatusColor = '#dc2626';
                isLocationValid = false;
            } else {
                locationStatus = 'Lokasi Anda berada dalam jangkauan kantor (' + distanceMeters.toFixed(0) + 'm dari kantor)';
                locationStatusColor = '#059669';
                isLocationValid = true;
            }
        } else {
            locationStatus = 'Lokasi kantor belum dikonfigurasi oleh admin';
            locationStatusColor = '#6b7280';
            isLocationValid = true; // Default valid jika belum ada konfigurasi
        }
        
        // Tentukan zoom level berdasarkan jarak
        let zoomLevel = 15;
        if (officeLat && officeLng) {
            const distance = calculateDistance(officeLat, officeLng, latitude, longitude);
            if (distance > 5) zoomLevel = 12;
            else if (distance > 1) zoomLevel = 13;
            else if (distance > 0.5) zoomLevel = 14;
        }
        
        // Hitung center point untuk map (antara user dan office jika ada)
        let centerLat = latitude;
        let centerLng = longitude;
        if (officeLat && officeLng) {
            centerLat = (latitude + officeLat) / 2;
            centerLng = (longitude + officeLng) / 2;
            // Adjust zoom untuk menampilkan kedua lokasi
            const distance = calculateDistance(officeLat, officeLng, latitude, longitude);
            if (distance > 2) zoomLevel = 12;
            else if (distance > 1) zoomLevel = 13;
            else if (distance > 0.5) zoomLevel = 14;
        }
        
        Swal.fire({
            title: 'Konfirmasi Lokasi',
            html: `
                <div style="text-align: left;">
                    <div id="locationConfirmMap" style="height: 300px; width: 100%; border-radius: 8px; margin: 15px 0;"></div>
                    <p style="margin-top: 12px; margin-bottom: 8px; font-weight: 600; color: #1f2937;">Status Lokasi:</p>
                    <p style="color: ${locationStatusColor}; font-size: 14px; font-weight: 500; margin-bottom: 0;">
                        ${locationStatus}
                    </p>
                    <p style="margin-top: 8px; color: #6b7280; font-size: 12px;">
                        Akurasi: ±${Math.round(accuracy)} meter
                    </p>
                </div>
            `,
            icon: isLocationValid ? 'info' : 'warning',
            showCancelButton: true,
            confirmButtonColor: '#6366f1',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Lanjut Check-In',
            cancelButtonText: 'Batal',
            reverseButtons: true,
            allowOutsideClick: false,
            allowEscapeKey: true,
            width: '90%',
            maxWidth: '500px',
            didOpen: () => {
                // Initialize map setelah modal terbuka
                setTimeout(() => {
                    const map = L.map('locationConfirmMap').setView([centerLat, centerLng], zoomLevel);
                    
                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                    }).addTo(map);
                    
                    // Marker untuk lokasi user
                    const userIcon = L.divIcon({
                        className: 'custom-div-icon',
                        html: `<div style="background-color:${isLocationValid ? '#10b981' : '#ef4444'}; width:32px; height:32px; border-radius:50%; border:3px solid white; display:flex; align-items:center; justify-content:center; color:white; box-shadow: 0 2px 8px rgba(0,0,0,0.3);"><i class="fas fa-user" style="font-size: 16px;"></i></div>`,
                        iconSize: [32, 32],
                        iconAnchor: [16, 32],
                        popupAnchor: [0, -32]
                    });
                    
                    L.marker([latitude, longitude], {icon: userIcon}).addTo(map)
                        .bindPopup(`<b>Lokasi Anda</b><br>Lat: ${latitude.toFixed(7)}<br>Lng: ${longitude.toFixed(7)}`)
                        .openPopup();
                    
                    // Marker dan circle untuk lokasi kantor (jika ada)
                    if (officeLat && officeLng && officeRadius) {
                        // Circle untuk radius kantor
                        L.circle([officeLat, officeLng], {
                            radius: officeRadius,
                            color: '#667eea',
                            fillColor: '#667eea',
                            fillOpacity: 0.2,
                            weight: 2
                        }).addTo(map);
                        
                        // Marker untuk lokasi kantor
                        const officeIcon = L.divIcon({
                            className: 'custom-div-icon',
                            html: `<div style="background-color:#667eea; width:32px; height:32px; border-radius:50%; border:3px solid white; display:flex; align-items:center; justify-content:center; color:white; box-shadow: 0 2px 8px rgba(0,0,0,0.3);"><i class="fas fa-building" style="font-size: 16px;"></i></div>`,
                            iconSize: [32, 32],
                            iconAnchor: [16, 32],
                            popupAnchor: [0, -32]
                        });
                        
                        L.marker([officeLat, officeLng], {icon: officeIcon}).addTo(map)
                            .bindPopup(`<b>Lokasi Kantor</b><br>Radius: ${officeRadius}m`);
                        
                        // Fit bounds untuk menampilkan kedua lokasi
                        const group = new L.featureGroup([
                            L.marker([latitude, longitude]),
                            L.marker([officeLat, officeLng])
                        ]);
                        map.fitBounds(group.getBounds().pad(0.2));
                    }
                    
                    // Recalculate map size setelah modal terbuka
                    map.invalidateSize();
                }, 100);
            }
        }).then((result) => {
            if (result.isConfirmed) {
                // Lanjut check-in
                submitCheckIn(latitude, longitude, isLocationValid);
            } else {
                // User cancel, tidak ada yang terjadi
                console.log('[Geolocation] User membatalkan check-in setelah melihat lokasi');
            }
        });
    }

    function submitCheckIn(latitude, longitude, locationValid = true) {
        const formData = new FormData();
        formData.append('work_type', document.getElementById('work_type').value);
        formData.append('notes', document.getElementById('notes').value);
        
        if (latitude && longitude) {
            formData.append('latitude', latitude);
            formData.append('longitude', longitude);
        }
        
        const imageFile = document.getElementById('image').files[0];
        if (imageFile) {
            formData.append('image', imageFile);
        }

        Swal.fire({
            title: 'Memproses...',
            html: '<div style="display: flex; justify-content: center; align-items: center; padding: 20px 0;"><div class="swal2-loader"></div></div>',
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        axios.post('{{ route('attendance.checkin') }}', formData, {
            headers: {
                'Content-Type': 'multipart/form-data'
            }
        })
        .then(response => {
            let icon = 'success';
            let title = 'Berhasil!';
            let text = response.data.message;
            
            // Show warning if location is invalid (dari server atau dari frontend)
            if (response.data.location_valid === false || locationValid === false) {
                icon = 'warning';
                title = 'Peringatan!';
                text = 'Check-in berhasil! Namun lokasi Anda berada di luar jangkauan kantor. Hubungi admin jika Anda merasa salah.';
            }
            
            Swal.fire({
                icon: icon,
                title: title,
                text: text,
                timer: icon === 'warning' ? 5000 : 2000,
                showConfirmButton: icon === 'warning',
                confirmButtonColor: '#6366f1'
            }).then(() => {
                location.reload();
            });
        })
        .catch(error => {
            let message = 'Terjadi kesalahan!';
            if (error.response && error.response.data && error.response.data.message) {
                message = error.response.data.message;
            }
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: message,
                confirmButtonColor: '#6366f1'
            });
        });
    }

    document.getElementById('checkOutBtn').addEventListener('click', function() {
        Swal.fire({
            title: 'Yakin ingin check-out?',
            text: 'Pastikan Anda sudah menyelesaikan pekerjaan hari ini',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#6366f1',
            cancelButtonColor: '#ef4444',
            confirmButtonText: 'Ya, Check-Out',
            cancelButtonText: 'Batal',
            buttonsStyling: true,
            reverseButtons: false,
            focusConfirm: false,
            allowOutsideClick: false,
            allowEscapeKey: true,
            customClass: {
                confirmButton: 'swal2-confirm-custom',
                cancelButton: 'swal2-cancel-custom'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Memproses...',
                    html: '<div style="display: flex; justify-content: center; align-items: center; padding: 20px 0;"><div class="swal2-loader"></div></div>',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                axios.post('{{ route('attendance.checkout') }}')
                    .then(response => {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: response.data.message,
                            timer: 2000,
                            showConfirmButton: false,
                            confirmButtonColor: '#6366f1'
                        }).then(() => {
                            location.reload();
                        });
                    })
                    .catch(error => {
                        let message = 'Terjadi kesalahan!';
                        if (error.response && error.response.data && error.response.data.message) {
                            message = error.response.data.message;
                        }
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: message,
                            confirmButtonColor: '#6366f1'
                        });
                    });
            }
        });
    });
</script>
@endsection