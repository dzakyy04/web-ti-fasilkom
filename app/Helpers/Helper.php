<?php

namespace App\Helpers;

class Helper
{
    public static function processThumbnail($thumbnail)
    {
        // Ambil APP_URL dari .env
        $appUrl = env('APP_URL');

        // Ubah URL thumbnail jika ada
        if (isset($thumbnail)) {
            $thumbnail = str_replace('public/', 'storage/', $thumbnail);
            $thumbnail = $appUrl . '/' . $thumbnail;
        }

        return $thumbnail;
    }

    public static function processContent($content)
    {
        $contentWithoutHtml = preg_replace('/\.\s*/', '. ', str_replace(["\n", "\r"], " ", strip_tags($content)));

        if (strlen($contentWithoutHtml) > 250) {
            $truncatedContent = substr($contentWithoutHtml, 0, 250);

            $lastSpace = strrpos($truncatedContent, ' ');
            if ($lastSpace !== false) {
                $truncatedContent = substr($truncatedContent, 0, $lastSpace);
            }

            $contentWithoutHtml = $truncatedContent . '...';
        }

        return $contentWithoutHtml;
    }
}
