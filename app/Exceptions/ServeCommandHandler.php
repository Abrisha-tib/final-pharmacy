<?php

namespace App\Exceptions;

use Illuminate\Support\Carbon;
use Illuminate\Foundation\Console\ServeCommand;

/**
 * Custom handler for Laravel ServeCommand errors
 * 
 * This class provides fallback handling for ServeCommand
 * regex parsing errors that can occur on different systems.
 */
class ServeCommandHandler
{
    /**
     * Handle the getDateFromLine method with error handling
     * 
     * @param string $line
     * @return \Illuminate\Support\Carbon
     */
    public static function getDateFromLine($line)
    {
        // Simplified regex that works on all systems
        $regex = '/^\[([^\]]+)\]/';

        $line = str_replace('  ', ' ', $line);
        preg_match($regex, $line, $matches);

        // Check if matches array has the expected index
        if (!isset($matches[1])) {
            // Fallback: return current time if regex doesn't match
            return Carbon::now();
        }

        try {
            return Carbon::createFromFormat('D M d H:i:s Y', $matches[1]);
        } catch (\Exception $e) {
            // Fallback: return current time if date parsing fails
            return Carbon::now();
        }
    }

    /**
     * Handle the getRequestPortFromLine method with error handling
     * 
     * @param string $line
     * @return int
     */
    public static function getRequestPortFromLine($line)
    {
        preg_match('/(\[\w+\s\w+\s\d+\s[\d:]+\s\d{4}\]\s)?:(\d+)\s(?:(?:\w+$)|(?:\[.*))/', $line, $matches);

        if (!isset($matches[2])) {
            // Fallback: return default port 8000
            return 8000;
        }

        return (int) $matches[2];
    }
}
