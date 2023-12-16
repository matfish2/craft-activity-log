<?php

namespace matfish\ActivityLog\services;

class ActionSegmentsToLabel
{
    public static function convert(string $segments): string
    {
        try {
            $x = array_map(static function ($segment) {
                $p = explode('-', $segment);
                $r = array_map(static function ($s) {
                    return ucfirst($s);
                }, $p);

                return implode(' ', $r);
            }, json_decode($segments, true, 512, JSON_THROW_ON_ERROR));

            return implode(' ', $x);
        } catch (\Throwable $e) {
            return $segments;
        }
    }
}