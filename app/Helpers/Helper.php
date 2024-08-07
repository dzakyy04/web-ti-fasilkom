<?php

namespace App\Helpers;

class Helper
{
    public static function convertFileUrl($file)
    {
        $appUrl = env('APP_URL');

        if (isset($file)) {
            $file = str_replace('public/', 'storage/', $file);
            $file = $appUrl . '/' . $file;
        }

        return $file;
    }

    public static function processContent($content, $length = null)
    {
        $contentWithoutHtml = strip_tags($content);

        $contentWithoutHtml = str_replace(["\n", "\r"], " ", $contentWithoutHtml);

        $contentWithoutHtml = html_entity_decode($contentWithoutHtml, ENT_QUOTES | ENT_HTML5);

        $contentWithoutHtml = preg_replace('/\s+/', ' ', $contentWithoutHtml);

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
