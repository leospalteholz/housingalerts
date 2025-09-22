<?php
/**
 * Laravel Scheduler Cron Job Script for Cloudways
 * 
 * This script should be called every minute via cron job
 */

// Set the working directory to the Laravel application root
chdir(__DIR__);

// Run Laravel's scheduler
exec('php artisan schedule:run 2>&1', $output, $return);

// Optional: Log the output for debugging
if (!empty($output)) {
    $logEntry = "[" . date('Y-m-d H:i:s') . "] Scheduler output:\n" . implode("\n", $output) . "\n\n";
    file_put_contents('storage/logs/cron.log', $logEntry, FILE_APPEND | LOCK_EX);
}

// Return appropriate exit code
exit($return);
