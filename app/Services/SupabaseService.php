<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Exception;

class SupabaseService
{
    protected string $url;
    protected string $anonKey;
    protected string $serviceKey;

    public function __construct()
    {
        $this->url = config('services.supabase.url');
        $this->anonKey = config('services.supabase.anon_key');
        $this->serviceKey = config('services.supabase.service_key');
    }

    /**
     * Send password reset email via Supabase Auth
     */
    public function sendPasswordResetEmail(string $email): array
    {
        try {
            $response = Http::withHeaders([
                'apikey' => $this->anonKey,
                'Authorization' => "Bearer {$this->anonKey}",
                'Content-Type' => 'application/json',
            ])
            ->post("{$this->url}/auth/v1/recover", [
                'email' => $email,
                'gotrue_meta_security' => [],
            ]);

            if ($response->failed()) {
                return [
                    'success' => false,
                    'message' => $response->json('error_description') ?? 'Failed to send reset email',
                ];
            }

            return [
                'success' => true,
                'message' => 'Password reset email sent successfully',
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Reset password using token from email
     */
    public function resetPassword(string $email, string $token, string $newPassword): array
    {
        try {
            // Local expiry guard: 1 hour max age based on JWT exp/iat
            $payload = $this->decodeJwtPayload($token);
            if (! $payload) {
                return [
                    'success' => false,
                    'message' => 'Token tidak valid',
                ];
            }

            $now = time();
            $exp = $payload['exp'] ?? null;
            $iat = $payload['iat'] ?? null;

            if (($exp && $now > $exp) || ($iat && ($now - $iat) > 3600)) {
                return [
                    'success' => false,
                    'message' => 'Link reset sudah kedaluwarsa, silakan minta ulang',
                ];
            }

            $response = Http::withHeaders([
                'apikey' => $this->anonKey,
                'Content-Type' => 'application/json',
            ])
            ->post("{$this->url}/auth/v1/verify", [
                'type' => 'recovery',
                'token' => $token,
            ]);

            if ($response->failed()) {
                return [
                    'success' => false,
                    'message' => 'Token reset tidak valid atau sudah kedaluwarsa',
                ];
            }

            $session = $response->json('session');
            if (!$session || !isset($session['access_token'])) {
                return [
                    'success' => false,
                    'message' => 'Gagal memverifikasi token',
                ];
            }

            // Update password
            $updateResponse = Http::withHeaders([
                'apikey' => $this->anonKey,
                'Authorization' => "Bearer {$session['access_token']}",
                'Content-Type' => 'application/json',
            ])
            ->put("{$this->url}/auth/v1/user", [
                'password' => $newPassword,
            ]);

            if ($updateResponse->failed()) {
                return [
                    'success' => false,
                    'message' => 'Gagal memperbarui password',
                ];
            }

            return [
                'success' => true,
                'message' => 'Password berhasil direset',
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Decode JWT payload without verifying signature
     */
    protected function decodeJwtPayload(string $jwt): ?array
    {
        $parts = explode('.', $jwt);
        if (count($parts) < 2) {
            return null;
        }

        $payload = $parts[1];
        $decoded = json_decode(base64_decode(strtr($payload, '-_', '+/')), true);

        return is_array($decoded) ? $decoded : null;
    }

    /**
     * Sign up new user dengan email verification
     */
    public function signUp(string $email, string $password): array
    {
        try {
            $response = Http::withHeaders([
                'apikey' => $this->anonKey,
                'Content-Type' => 'application/json',
            ])
            ->post("{$this->url}/auth/v1/signup", [
                'email' => $email,
                'password' => $password,
                'data' => [
                    'email_verified' => false,
                ],
            ]);

            if ($response->failed()) {
                return [
                    'success' => false,
                    'message' => $response->json('error_description') ?? 'Signup failed',
                ];
            }

            return [
                'success' => true,
                'message' => 'Signup successful. Verification email sent to ' . $email,
                'user' => $response->json('user'),
                'session' => $response->json('session'),
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Sign in user
     */
    public function signIn(string $email, string $password): array
    {
        try {
            $response = Http::withHeaders([
                'apikey' => $this->anonKey,
                'Content-Type' => 'application/json',
            ])
            ->post("{$this->url}/auth/v1/token?grant_type=password", [
                'email' => $email,
                'password' => $password,
            ]);

            if ($response->failed()) {
                return [
                    'success' => false,
                    'message' => 'Invalid email or password',
                ];
            }

            return [
                'success' => true,
                'message' => 'Login successful',
                'session' => $response->json(),
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ];
        }
    }
}
