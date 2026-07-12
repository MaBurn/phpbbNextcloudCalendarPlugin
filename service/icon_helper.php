<?php

namespace maxbrenne\nextcloudcalendar\service;

/**
 * Normalises user-provided FontAwesome icon classes to a single safe "fa-*" class.
 */
class icon_helper
{
    public const DEFAULT_ICON = 'fa-calendar-plus-o';

    public static function normalise(string $icon): string
    {
        $icon = strtolower(trim($icon));
        $icon = preg_replace('/[^a-z0-9\-\s]/', '', $icon);
        $parts = preg_split('/\s+/', (string) $icon, -1, PREG_SPLIT_NO_EMPTY);

        foreach ($parts as $part)
        {
            if (strpos($part, 'fa-') === 0)
            {
                return $part;
            }
        }

        if (!empty($parts[0]) && $parts[0] !== 'fa')
        {
            return 'fa-' . $parts[0];
        }

        return self::DEFAULT_ICON;
    }
}
