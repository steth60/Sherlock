<?php

namespace App\Helpers;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class UpdateHelper
{
    public static function extract($zipFile, $extractTo)
    {
        $zip = new \ZipArchive;
        if ($zip->open($zipFile) === TRUE) {
            $zip->extractTo($extractTo);
            $zip->close();
        } else {
            throw new \Exception('Failed to extract zip file');
        }
    }

    public static function backup($files)
    {
        foreach ($files as $file) {
            if (File::exists($file)) {
                $backupPath = storage_path('backups/' . basename($file));
                File::copy($file, $backupPath);
            }
        }
    }
}
