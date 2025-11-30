<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\App;

if (! function_exists('localized_date')) {
    /**
     * Format a date according to the current locale.
     *
     * @param  string  $format  Format key: short_date, long_date, datetime, time
     */
    function localized_date(Carbon|string|null $date, string $format = 'long_date'): string
    {
        if (! $date) {
            return '';
        }

        if (is_string($date)) {
            $date = Carbon::parse($date);
        }

        $locale = App::getLocale();
        $carbonLocale = match ($locale) {
            'no' => 'nb',  // Norwegian Bokmål
            'uk' => 'uk',  // Ukrainian
            default => $locale,
        };

        $date->locale($carbonLocale);

        $formats = config('app.date_formats');
        $localeFormats = $formats[$locale] ?? $formats['en'];
        $dateFormat = $localeFormats[$format] ?? $format;

        // Use translatedFormat for localized day/month names
        return $date->translatedFormat($dateFormat);
    }
}

if (! function_exists('localized_date_range')) {
    /**
     * Format a date range according to the current locale.
     */
    function localized_date_range(Carbon|string|null $start, Carbon|string|null $end): string
    {
        if (! $start) {
            return '';
        }

        if (is_string($start)) {
            $start = Carbon::parse($start);
        }

        if (is_string($end)) {
            $end = Carbon::parse($end);
        }

        $locale = App::getLocale();
        $carbonLocale = match ($locale) {
            'no' => 'nb',  // Norwegian Bokmål
            'uk' => 'uk',  // Ukrainian
            default => $locale,
        };

        $start->locale($carbonLocale);
        $end?->locale($carbonLocale);

        $formats = config('app.date_formats');
        $localeFormats = $formats[$locale] ?? $formats['en'];

        $shortFormat = $localeFormats['short_date'];
        $longFormat = $localeFormats['long_date'];

        if ($end) {
            return $start->translatedFormat($shortFormat).' - '.$end->translatedFormat($longFormat);
        }

        return $start->translatedFormat($longFormat);
    }
}
