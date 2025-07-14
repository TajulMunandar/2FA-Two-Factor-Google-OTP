<?php

namespace App\Http\Controllers;

use App\Mail\OTPMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Google2FA;
use Illuminate\Support\Facades\Hash;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class TwoFactorAuthController extends Controller
{
    public function showRegistrationForm()
    {
        return view('2fa.register');
    }

    public function dashboardShow(Request $request)
    {
        $user = Auth::user();

        // Jika user punya 2FA dan belum verifikasi, arahkan ke halaman OTP
        if ($user && $user->google2fa_secret && !$request->session()->get('2fa_verified')) {
            return redirect()->route('verify')->withErrors(['message' => 'Silakan verifikasi 2FA terlebih dahulu.']);
        }

        return view('menu');
    }

    public function showLoginForm()
    {
        return view('2fa.login');
    }

    public function showVerify()
    {
        return view('2fa.verify');
    }

    public function resendOtp(Request $request)
    {
        $user = Auth::user();

        if (!$user || !$user->google2fa_secret) {
            return redirect()->route('login')->withErrors(['message' => 'Akses tidak valid.']);
        }

        $google2fa = app('pragmarx.google2fa');
        $otp = $google2fa->getCurrentOtp($user->google2fa_secret);

        // Kirim ulang email OTP
        Mail::to($user->email)->send(new OTPMail($otp));

        return back()->with('status', 'Kode OTP berhasil dikirim ulang.');
    }

    public function register(Request $request)
    {
        try {
            // Validasi input pengguna
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:5|confirmed',
            ]);

            // Membuat pengguna baru
            User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
            ]);

            // Mengarahkan ke halaman Enable 2FA
            return view('2fa.login');
        } catch (Exception $e) {
            return back()->withErrors(['registration_error' => $e->getMessage()])->withInput();
        }
    }

    public function login(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email',
                'password' => 'required',
            ]);

            // Mencari pengguna berdasarkan email
            $user = User::where('email', $request->email)->first();

            // Jika pengguna ditemukan dan password cocok
            if ($user && Hash::check($request->password, $user->password)) {
                Auth::login($user);

                // Cek apakah 2FA diaktifkan untuk pengguna
                if ($user->google2fa_secret) {
                    // Generate OTP
                    $google2fa = app('pragmarx.google2fa');
                    $otp = $google2fa->getCurrentOtp($user->google2fa_secret);

                    // Kirim OTP ke email pengguna
                    $mail = Mail::to($user->email)->send(new OTPMail($otp));

                    // Arahkan ke halaman verifikasi OTP
                    return redirect()->route('verify');  // Pastikan route verify ada
                }

                // Jika 2FA tidak diaktifkan, langsung ke dashboard
                return redirect()->route('dashboard');
            }

            return back()->withErrors(['email' => 'Email or password is incorrect.']);
        } catch (Exception $e) {
            // Tangani kesalahan dalam proses login
            return back()->withErrors(['login_error' => $e->getMessage()])->withInput();
        }
    }

    function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }

    public function enable2fa(Request $request)
    {
        try {
            $user = Auth::user();
            $google2fa = app('pragmarx.google2fa');
            $secretKey = $google2fa->generateSecretKey();
            $user->google2fa_secret = $secretKey;
            $user->save();

            // Generate QR code
            $qrCodeUrl = $google2fa->getQRCodeUrl(
                config('app.name'),
                $user->email,
                $secretKey
            );

            return view('2fa.enable', ['qrCodeUrl' => $qrCodeUrl, 'secret' => $secretKey]);
        } catch (Exception $e) {
            // Tangani kesalahan dalam proses 2FA enable
            return back()->withErrors(['enable_2fa_error' => 'An error occurred while enabling 2FA. Please try again.']);
        }
    }

    public function verify2fa(Request $request)
    {
        try {
            $request->validate([
                'code' => 'required',
            ]);

            $google2fa = app('pragmarx.google2fa');
            $user = Auth::user();

            // Verifikasi OTP yang dimasukkan dengan kode yang dihitung
            $valid = $google2fa->verifyKey($user->google2fa_secret, $request->input('code'));

            if ($valid) {
                $request->session()->put('2fa_verified', true);
                return redirect()->route('dashboard');
            }

            return back()->withErrors(['code' => 'Invalid code.']);
        } catch (Exception $e) {
            // Tangani kesalahan dalam proses verifikasi 2FA
            return back()->withErrors(['verification_error' => 'An error occurred during 2FA verification. Please try again.']);
        }
    }
}
