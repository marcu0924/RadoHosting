<?php

namespace App\Services;

class SystemMetrics
{
    public function getCpuLoad(): float
    {
        // 1-minute average load over number of cores → percentage
        $load  = sys_getloadavg()[0] ?? 0.0;
        $cores = (int) shell_exec('nproc') ?: 1;

        // Normalize: load 1.0 on 4 cores ~= 25%
        return round(($load / $cores) * 100, 1);
    }

    public function getMemoryUsage(): array
    {
        // Use `free -m` to read memory in MB
        $output = [];
        exec('free -m', $output);

        // Example "free -m" output line:
        // Mem:   32040  12345   5432  ...
        $parts = preg_split('/\s+/', $output[1] ?? '');

        $total = (int)($parts[1] ?? 0);
        $used  = (int)($parts[2] ?? 0);

        $percent = $total > 0 ? round($used / $total * 100, 1) : 0;

        return [
            'total'   => $total,
            'used'    => $used,
            'percent' => $percent,
        ];
    }

    public function getDiskUsage(string $path = '/'): array
    {
        $total = disk_total_space($path);
        $free  = disk_free_space($path);
        $used  = $total - $free;

        $percent = $total > 0 ? round($used / $total * 100, 1) : 0;

        return [
            'total'   => $total,
            'used'    => $used,
            'free'    => $free,
            'percent' => $percent,
        ];
    }
}
