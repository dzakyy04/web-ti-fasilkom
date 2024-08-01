<?php

namespace App\Helpers;

class Helper
{
    public static function convertImageUrl($image)
    {
        $appUrl = env('APP_URL');

        if (isset($image)) {
            $image = str_replace('public/', 'storage/', $image);
            $image = $appUrl . '/' . $image;
        }

        return $image;
    }

    public static function processContent($content, $length = null)
    {
        $contentWithoutHtml = preg_replace('/\.\s*/', '. ', str_replace(["\n", "\r"], " ", strip_tags($content)));

        if ($length !== null && strlen($contentWithoutHtml) > $length) {
            $truncatedContent = substr($contentWithoutHtml, 0, $length);

            $lastSpace = strrpos($truncatedContent, ' ');
            if ($lastSpace !== false) {
                $truncatedContent = substr($truncatedContent, 0, $lastSpace);
            }

            $contentWithoutHtml = $truncatedContent . '...';
        }

        return $contentWithoutHtml;
    }

}
