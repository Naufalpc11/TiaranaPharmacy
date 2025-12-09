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
            $apiKey = $this->serviceKey ?: $this->anonKey;
            $redirectUrl = rtrim(config('app.url'), '/') . '/auth/reset-password';

            $response = Http::withHeaders([
                'apikey' => $apiKey,
                'Authorization' => "Bearer {$apiKey}",
                'Content-Type' => 'application/json',
            ])
            ->post("{$this->url}/auth/v1/recover", [
                'email' => $email,
                'redirect_to' => $redirectUrl,
                'gotrue_meta_security' => [],
            ]);

            if ($response->failed()) {
                $message = $response->json('msg')
                    ?? $response->json('error_description')
                    ?? $response->json('message')
                    ?? 'Failed to send reset email';

                \Log::error('Supabase reset email failed', [
                    'email' => $email,
                    'status' => $response->status(),
                    'body' => $response->json(),
                    'text' => $response->body(),
                ]);

                return [
                    'success' => false,
                    'message' => $message,
                ];
            }

            \Log::info('Supabase reset email success', [
                'email' => $email,
                'status' => $response->status(),
            ]);

            return [
                'success' => true,
                'message' => 'Password reset email sent successfully',
            ];
        } catch (Exception $e) {
            \Log::error('Supabase reset email exception', [
                'email' => $email,
                'error' => $e->getMessage(),
            ]);
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
            // Local expiry guard: 30 minutes max age based on JWT exp/iat
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

            // Batasi 30 menit (1800 detik)
            if (($exp && $now > $exp) || ($iat && ($now - $iat) > 1800)) {
                return [
                    'success' => false,
                    'message' => 'Link reset sudah kedaluwarsa, silakan minta ulang',
                ];
            }

            // Gunakan access_token langsung untuk update password user
            $headers = [
                'apikey' => $this->anonKey,
                'Authorization' => "Bearer {$token}",
                'Content-Type' => 'application/json',
            ];

            // Opsional: verifikasi email cocok dengan access token
            $userResponse = Http::withHeaders($headers)
                ->get("{$this->url}/auth/v1/user");

            if ($userResponse->failed()) {
                return [
                    'success' => false,
                    'message' => 'Token reset tidak valid atau sudah kedaluwarsa',
                ];
            }

            $userEmail = $userResponse->json('email') ?? null;
            if ($userEmail && strcasecmp($userEmail, $email) !== 0) {
                return [
                    'success' => false,
                    'message' => 'Email pada token tidak cocok',
                ];
            }

            $updateResponse = Http::withHeaders($headers)
                ->put("{$this->url}/auth/v1/user", [
                    'password' => $newPassword,
                ]);

            if ($updateResponse->failed()) {
                $msg = $updateResponse->json('msg')
                    ?? $updateResponse->json('message')
                    ?? 'Gagal memperbarui password';

                return [
                    'success' => false,
                    'message' => $msg,
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
