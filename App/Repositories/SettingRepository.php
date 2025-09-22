<?php

namespace App\Repositories;

use App\Repositories\Interfaces\SettingRepositoryInterface;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

use App\Models\Memory;
use App\Models\Moment;
use App\Models\MemoryStat;
use Illuminate\Support\Facades\Log; // Import Log facade at the top
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;

class SettingRepository implements SettingRepositoryInterface
{

    protected function logError($url, $error_message)
    {
        Log::error('Error in setting repository function', [
            'url' => $url,
            'error' => $error_message
        ]);
    }

    public function encryptText($text)
    {
        $encrypt_key = env('ENCRYPT_KEY');
        $encrypt_code = env('ENCRYPT_CODE');
        // Encrypt the text
        $encryptedText = openssl_encrypt($text, 'AES-128-CBC', $encrypt_key, OPENSSL_RAW_DATA, $encrypt_code);
        if ($encryptedText == '' || $encryptedText == null) {
            return $text;
        }
        // Return the encrypted text in Base64 encoding
        return base64_encode($encryptedText);
    }

    public function decryptText($encryptedText)
    {
        $encrypt_key = env('ENCRYPT_KEY');
        $encrypt_code = env('ENCRYPT_CODE');
        $text = $encryptedText;
        // Decode the text from Base64
        $encryptedText = base64_decode($encryptedText);
        // Decrypt the text
        $decryptedText = openssl_decrypt($encryptedText, 'AES-128-CBC', $encrypt_key, OPENSSL_RAW_DATA, $encrypt_code);
        if ($decryptedText == '' || $decryptedText == null) {
            return $text;
        }
        return $decryptedText;
    }

    function generateRandomString($length/* = 4*/, $type/* = 1*/)
    {
        if (intval($type) == 1) {
            $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        } else if (intval($type) == 2) {
            $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!@#$%^&*+-/?><';
        } else if (intval($type) == 3) {
            $characters = '0123456789';
        } else if (intval($type) == 4) {
            $characters = '0123456789abcdefg';
        } else {
            $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        }

        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}