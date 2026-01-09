@extends('layouts.app')

@section('title', 'Ganti Password - Sistem Absensi')

@section('styles')
<style>
    .change-password-container {
        display: flex;
        justify-content: center;
        align-items: flex-start;
        min-height: 80vh;
        padding: 2rem 1rem;
    }
    
    .change-password-wrapper {
        width: 100%;
        max-width: 32rem;
        box-sizing: border-box;
    }
    
    .card-main {
        background: white;
        border-radius: 1rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        border: 1px solid #e5e7eb;
        overflow: hidden;
        box-sizing: border-box;
    }
    
    .card-body {
        box-sizing: border-box;
        overflow-x: hidden;
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
    
    .required {
        color: #dc2626;
    }
    
    .form-control {
        width: 100%;
        max-width: 100%;
        padding: 0.625rem 1rem;
        border: 1px solid #d1d5db;
        border-radius: 0.5rem;
        font-size: 0.875rem;
        transition: all 0.2s;
        box-sizing: border-box;
    }
    
    .form-control:focus {
        outline: none;
        border-color: #6366f1;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
    }
    
    .form-control.error {
        border-color: #dc2626;
    }
    
    .error-message {
        color: #dc2626;
        font-size: 0.875rem;
        margin-top: 0.375rem;
    }
    
    .form-help {
        font-size: 0.75rem;
        color: #6b7280;
        margin-top: 0.375rem;
    }
    
    .form-actions {
        display: flex;
        justify-content: flex-end;
        gap: 0.75rem;
        margin-top: 1.5rem;
    }
    
    .btn {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        padding: 0.875rem 1.5rem;
        border-radius: 0.5rem;
        font-weight: 500;
        font-size: 0.875rem;
        cursor: pointer;
        border: none;
        transition: all 0.2s;
        text-decoration: none;
    }
    
    .btn-secondary {
        background: white;
        color: #374151;
        border: 1px solid #d1d5db;
    }
    
    .btn-secondary:hover {
        background: #f9fafb;
    }
    
    .btn-primary {
        background: #6366f1;
        color: white;
    }
    
    .btn-primary:hover {
        background: #4f46e5;
    }
    
    .info-box {
        background: #eff6ff;
        border-left: 4px solid #3b82f6;
        padding: 1rem;
        border-radius: 0.5rem;
        margin-bottom: 1.25rem;
    }
    
    .info-box p {
        color: #1e40af;
        font-size: 0.875rem;
        margin: 0;
    }
</style>
@endsection

@section('content')
<div class="change-password-container">
    <div class="change-password-wrapper">
        <div class="card-main">
            <div class="card-header">
                <div class="header-content">
                    <div class="icon-box">
                        <i class="fas fa-key" style="color: #6366f1;"></i>
                    </div>
                    <div>
                        <h3 style="font-size: 1.25rem; font-weight: 600; color: #111827; margin: 0;">Ganti Password</h3>
                        <p style="font-size: 0.875rem; color: #6b7280; margin: 0.25rem 0 0 0;">Ubah password akun Anda</p>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <div class="info-box">
                    <p><i class="fas fa-info-circle"></i> Pastikan password baru minimal 6 karakter dan mudah diingat</p>
                </div>
                
                <form action="{{ route('profile.change-password') }}" method="POST">
                    @csrf
                    
                    <div class="form-group">
                        <label for="current_password" class="form-label">
                            Password Lama <span class="required">*</span>
                        </label>
                        <input type="password" 
                               id="current_password" 
                               name="current_password" 
                               class="form-control @error('current_password') error @enderror"
                               required>
                        @error('current_password')
                            <p class="error-message">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="form-group">
                        <label for="new_password" class="form-label">
                            Password Baru <span class="required">*</span>
                        </label>
                        <input type="password" 
                               id="new_password" 
                               name="new_password" 
                               class="form-control @error('new_password') error @enderror"
                               required>
                        @error('new_password')
                            <p class="error-message">{{ $message }}</p>
                        @enderror
                        <p class="form-help">Minimal 6 karakter</p>
                    </div>
                    
                    <div class="form-group">
                        <label for="new_password_confirmation" class="form-label">
                            Konfirmasi Password Baru <span class="required">*</span>
                        </label>
                        <input type="password" 
                               id="new_password_confirmation" 
                               name="new_password_confirmation" 
                               class="form-control @error('new_password_confirmation') error @enderror"
                               required>
                        @error('new_password_confirmation')
                            <p class="error-message">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="form-actions">
                        <a href="{{ route('attendance.index') }}" class="btn btn-secondary">
                            Batal
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i>
                            <span>Simpan</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection