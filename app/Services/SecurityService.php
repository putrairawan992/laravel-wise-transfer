<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use phpseclib3\Crypt\PublicKeyLoader;
use phpseclib3\Crypt\RSA;

class SecurityService
{
    protected $publicKeyPath = 'private/keys/public.pem';
    protected $privateKeyPath = 'private/keys/private.pem';

    public function __construct()
    {
        $this->ensureKeypair();
    }

    protected function ensureKeypair(): void
    {
        $disk = Storage::disk('local');

        if ($disk->exists($this->publicKeyPath) && $disk->exists($this->privateKeyPath)) {
            return;
        }

        $config = array(
            "digest_alg" => "sha512",
            "private_key_bits" => 2048,
            "private_key_type" => OPENSSL_KEYTYPE_RSA,
        );
        
        // Try to locate openssl.cnf
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $opensslConfigPath = getenv('OPENSSL_CONF') ?: 'C:\xampp\php\extras\ssl\openssl.cnf';
            if (file_exists($opensslConfigPath)) {
                $config['config'] = $opensslConfigPath;
            }
        }

        $key = openssl_pkey_new($config);

        if ($key === false) {
            throw new \Exception('RSA key generation failed: '.openssl_error_string());
        }

        $privatePem = '';
        if (!openssl_pkey_export($key, $privatePem, null, $config)) {
            throw new \Exception('RSA private key export failed: '.openssl_error_string());
        }

        $details = openssl_pkey_get_details($key);
        if (!is_array($details) || empty($details['key'])) {
            throw new \Exception('RSA public key export failed: '.openssl_error_string());
        }

        $publicPem = $details['key'];

        $disk->put($this->privateKeyPath, $privatePem);
        $disk->put($this->publicKeyPath, $publicPem);
    }

    public function encryptRSA(string $data): string
    {
        $publicKeyPem = Storage::disk('local')->get($this->publicKeyPath);

        if (!$publicKeyPem) {
            throw new \Exception('Public key not found.');
        }

        $publicKey = PublicKeyLoader::load($publicKeyPem)
            ->withPadding(RSA::ENCRYPTION_OAEP)
            ->withHash('sha256')
            ->withMGFHash('sha256');

        $ciphertext = $publicKey->encrypt($data);
        if ($ciphertext === false) {
            throw new \Exception('RSA encryption failed.');
        }

        return base64_encode($ciphertext);
    }

    public function decryptRSA(string $encryptedData): string
    {
        $privateKeyPem = Storage::disk('local')->get($this->privateKeyPath);

        if (!$privateKeyPem) {
            throw new \Exception('Private key not found.');
        }

        $decoded = base64_decode($encryptedData, true);
        if ($decoded === false) {
            throw new \Exception('Base64 decode failed.');
        }

        $privateKey = PublicKeyLoader::load($privateKeyPem)
            ->withPadding(RSA::ENCRYPTION_OAEP)
            ->withHash('sha256')
            ->withMGFHash('sha256');

        try {
            $plaintext = $privateKey->decrypt($decoded);
            if ($plaintext === false) {
                throw new \Exception('RSA decrypt returned false.');
            }
            return $plaintext;
        } catch (\Throwable $e) {
            if (openssl_private_decrypt($decoded, $fallback, $privateKeyPem, OPENSSL_PKCS1_OAEP_PADDING)) {
                return $fallback;
            }

            throw new \Exception('RSA decryption failed.');
        }
    }
}
