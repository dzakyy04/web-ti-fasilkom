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
