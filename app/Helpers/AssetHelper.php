<?php

namespace App\Helpers;

class AssetHelper
{
    public static function buildAsset($path)
    {
        $manifestPath = public_path('build/manifest.json');
        
        if (!file_exists($manifestPath)) {
            return asset($path);
        }
        
        $manifest = json_decode(file_get_contents($manifestPath), true);
        
        if (isset($manifest[$path])) {
            return asset('build/' . $manifest[$path]['file']);
        }
        
        return asset($path);
    }
}
