<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Attendance;
use Illuminate\Support\Facades\Storage;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (Auth::check() && Auth::user()->role === 'admin') {
                return redirect()->route('admin.dashboard');
            }
            return $next($request);
        });
    }

    public function index()
    {
        $today = Carbon::today('Asia/Jakarta');
        $attendance = Attendance::where('user_id', Auth::id())
            ->whereDate('attendance_date', $today)
            ->first();

        $settings = \App\Models\Setting::getSettings();

        return view('attendance.index', compact('attendance', 'settings'));
    }

    public function checkIn(Request $request)
    {
        $request->validate([
            'work_type' => 'required|in:WFA,WFO,WFH',
            'notes' => 'nullable|string|max:500',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
        ]);

        // Log request
        Log::info('Check-in request', [
            'user_id' => Auth::id(),
            'work_type' => $request->work_type,
            'has_location' => $request->has('latitude') && $request->has('longitude'),
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'timestamp' => now('Asia/Jakarta')->toDateTimeString()
        ]);

        $today = Carbon::today('Asia/Jakarta');

        // Check if already checked in today
        $existing = Attendance::where('user_id', Auth::id())
            ->whereDate('attendance_date', $today)
            ->first();

        if ($existing && $existing->check_in) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah check-in hari ini!'
            ], 400);
        }

        $data = [
            'user_id' => Auth::id(),
            'attendance_date' => $today,
            'work_type' => $request->work_type,
            'notes' => $request->notes,
            'check_in' => Carbon::now('Asia/Jakarta'),
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'location_valid' => true,
        ];

        // Validate location for WFO (jika lokasi tersedia)
        if ($request->work_type === 'WFO') {
            if ($request->latitude && $request->longitude) {
                $settings = \App\Models\Setting::getSettings();

                Log::info('WFO Location validation', [
                    'user_id' => Auth::id(),
                    'user_location' => ['lat' => $request->latitude, 'lng' => $request->longitude],
                    'office_location' => ['lat' => $settings->latitude, 'lng' => $settings->longitude],
                    'radius' => $settings->radius
                ]);

                if ($settings->latitude && $settings->longitude && $settings->radius) {
                    $distance = $this->calculateDistance(
                        $settings->latitude,
                        $settings->longitude,
                        $request->latitude,
                        $request->longitude
                    );

                    // Convert radius from meters to kilometers
                    $radiusKm = $settings->radius / 1000;

                    Log::info('Location distance calculation', [
                        'user_id' => Auth::id(),
                        'distance_km' => $distance,
                        'radius_km' => $radiusKm,
                        'is_valid' => $distance <= $radiusKm
                    ]);

                    if ($distance > $radiusKm) {
                        $data['location_valid'] = false;
                    }
                } else {
                    Log::warning('Office location not configured', [
                        'user_id' => Auth::id()
                    ]);
                }
            } else {
                // Jika WFO tapi tidak ada lokasi, set location_valid = false
                Log::warning('WFO check-in without location', [
                    'user_id' => Auth::id()
                ]);
                $data['location_valid'] = false;
            }
        }

        // Handle image upload to Cloudinary
        if ($request->hasFile('image')) {
            try {
                $uploadedFile = Cloudinary::upload($request->file('image')->getRealPath(), [
                    'folder' => 'attendance_images',
                    'resource_type' => 'image'
                ]);
                $data['image'] = $uploadedFile->getSecurePath();
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal mengupload gambar: ' . $e->getMessage()
                ], 500);
            }
        }

        if ($existing) {
            $existing->update($data);
            $attendance = $existing;
        } else {
            $attendance = Attendance::create($data);
        }

        $message = 'Check-in berhasil!';
        if ($request->work_type === 'WFO' && isset($data['location_valid']) && !$data['location_valid']) {
            $message = 'Check-in berhasil! Namun lokasi Anda berada di luar jangkauan kantor. Hubungi admin jika Anda merasa salah.';
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'location_valid' => $data['location_valid'] ?? true,
            'attendance' => $attendance
        ]);
    }

    /**
     * Calculate distance between two coordinates using Haversine formula
     * Returns distance in kilometers
     */
    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371; // Earth's radius in kilometers

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

    public function checkOut(Request $request)
    {
        $today = Carbon::today('Asia/Jakarta');

        $attendance = Attendance::where('user_id', Auth::id())
            ->whereDate('attendance_date', $today)
            ->first();

        if (!$attendance || !$attendance->check_in) {
            return response()->json([
                'success' => false,
                'message' => 'Anda belum check-in hari ini!'
            ], 400);
        }

        if ($attendance->check_out) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah check-out hari ini!'
            ], 400);
        }

        $attendance->update([
            'check_out' => Carbon::now('Asia/Jakarta')
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Check-out berhasil!',
            'attendance' => $attendance
        ]);
    }
}
