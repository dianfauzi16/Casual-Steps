<?php

namespace App\Core;

use Cloudinary\Configuration\Configuration;
use Cloudinary\Api\Upload\UploadApi;
use Exception;

class CloudinaryHelper {
    private static $isConfigured = false;

    private static function configure() {
        if (self::$isConfigured) {
            return true;
        }

        require_once dirname(dirname(__DIR__)) . '/vendor/autoload.php';
        
        $cloudinaryUrl = getenv('CLOUDINARY_URL') ?: ($_ENV['CLOUDINARY_URL'] ?? ($_SERVER['CLOUDINARY_URL'] ?? null));
        
        if (empty($cloudinaryUrl)) {
            $envPath = dirname(dirname(__DIR__)) . '/.env';
            if (file_exists($envPath)) {
                $envLines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
                foreach ($envLines as $line) {
                    if (strpos(trim($line), 'CLOUDINARY_URL=') === 0) {
                        $cloudinaryUrl = trim(str_replace('"', '', substr(trim($line), 15)));
                        break;
                    }
                }
            }
        }

        if ($cloudinaryUrl) {
            Configuration::instance($cloudinaryUrl);
            self::$isConfigured = true;
            return true;
        }

        return false;
    }

    /**
     * Upload an image to Cloudinary
     * 
     * @param string $filePath The temporary path of the uploaded file
     * @param string $folder The folder in Cloudinary to upload to
     * @return string|false Returns the secure URL if successful, false otherwise
     * @throws Exception If upload fails
     */
    public static function upload($filePath, $folder = 'casual_steps_products') {
        if (!self::configure()) {
            throw new Exception("CLOUDINARY_URL belum dikonfigurasi.");
        }

        $uploadApi = new UploadApi();
        $uploadResult = $uploadApi->upload($filePath, [
            'folder' => $folder
        ]);

        if (isset($uploadResult['secure_url'])) {
            return $uploadResult['secure_url'];
        }

        return false;
    }
}
