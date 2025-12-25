<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use App\Models\MinecraftServer;

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard.
     */
    public function index()
    {
        // Host resource stats for the machine running this Laravel app
        $cpuUsage    = $this->getCpuLoadPercent();
        $memoryUsage = $this->getMemoryUsagePercent();
        $diskUsage   = $this->getDiskUsagePercent('/home'); // change path if needed

        // Pull live Minecraft servers from Docker (filtered)
        $allServers = $this->getDockerServers(); // Collection

        // Simple pagination for the dashboard table
        $perPage     = 10;
        $currentPage = (int) request()->input('page', 1);

        $servers = new LengthAwarePaginator(
            $allServers->forPage($currentPage, $perPage)->values(),
            $allServers->count(),
            $perPage,
            $currentPage,
            [
                'path'  => request()->url(),
                'query' => request()->query(),
            ]
        );

        // High-level stats
        $stats = [
            'cpu_usage'       => $cpuUsage,
            'memory_usage'    => $memoryUsage,
            'disk_usage'      => $diskUsage,
            'nodes_online'    => 1, // just this host for now
            'nodes_total'     => 1,
            'total_servers'   => $allServers->count(),
            'running_servers' => $allServers->where('status', 'running')->count(),
        ];

        // No ticket system yet
        $openTickets = collect();

        return view('admin.index', compact('stats', 'servers', 'openTickets'));
    }

    /**
     * Build a collection of servers from `docker ps`, filtered to only
     * show Minecraft containers using our Minecraft images.
     */
    private function getDockerServers(): Collection
    {
        $output   = [];
        $exitCode = 0;

        // Name|Image|Ports|Status|CreatedAt
        @exec(
            "docker ps --format '{{.Names}}|{{.Image}}|{{.Ports}}|{{.Status}}|{{.CreatedAt}}' 2>&1",
            $output,
            $exitCode
        );

        if ($exitCode !== 0 || empty($output)) {
            return collect();
        }

        // Try to get public IP once per request
        $publicIp = trim((string) @shell_exec('curl -s ifconfig.me 2>/dev/null'));
        if ($publicIp === '') {
            $publicIp = 'SERVER_IP'; // fallback label
        }

        $servers = [];

        foreach ($output as $line) {
            [$containerName, $image, $ports, $statusRaw, $createdAtRaw] = array_pad(explode('|', $line), 5, '');

            $containerName = trim($containerName);
            $image         = trim($image);
            $ports         = trim($ports);
            $statusRaw     = trim($statusRaw);
            $createdAtRaw  = trim($createdAtRaw);

            // Only Minecraft server containers
            // (works for itzg/minecraft-server, rado/minecraft-server, etc.)
            if ($image === '' || ! str_contains($image, 'minecraft-server')) {
                continue;
            }

            // Status mapping
            $status = 'unknown';
            if (stripos($statusRaw, 'Up ') === 0) {
                $status = 'running';
            } elseif (stripos($statusRaw, 'Exited ') === 0) {
                $status = 'stopped';
            }

            // Extract host port, e.g. 0.0.0.0:25570->25565/tcp
            $hostPort = null;
            if ($ports !== '') {
                if (preg_match('/:(\d+)->\d+\/tcp/', $ports, $m)) {
                    $hostPort = $m[1] ?? null;
                }
            }

            $address = $hostPort
                ? $publicIp . ':' . $hostPort
                : 'N/A';

            // 🔗 Match by container_name (set when created in MinecraftServerController)
            $mc = MinecraftServer::where('container_name', $containerName)->first();

            $servers[] = [
                // used by the Blade to generate admin.minecraft.show link
                'minecraft_id' => $mc?->id,

                // Pretty name: DB name if we have it, else container name
                'name'   => $mc->name ?? ($containerName !== '' ? $containerName : 'Unnamed Container'),
                'game'   => 'Minecraft',
                'image'  => $image,
                'address'=> $address,
                'plan'   => $mc->plan ?? '—',   // if you add a plan column later
                'players'=> '—',                // placeholder until you query player counts
                'status' => $status,

                // For display: prefer DB created_at, fallback to Docker's CreatedAt string
                'created_at' => $mc?->created_at ?? $createdAtRaw,
            ];
        }

        return collect($servers);
    }

    /**
     * Get approximate CPU load as a percentage of total cores.
     */
    private function getCpuLoadPercent(): float
    {
        $loadAverages = @sys_getloadavg();

        if (! is_array($loadAverages) || ! isset($loadAverages[0])) {
            return 0.0;
        }

        $oneMinuteLoad = (float) $loadAverages[0];

        // Get number of CPU cores (Linux)
        $coresOutput = @shell_exec('nproc 2>/dev/null');
        $cores       = $coresOutput ? (int) trim($coresOutput) : 1;

        if ($cores < 1) {
            $cores = 1;
        }

        $percent = ($oneMinuteLoad / $cores) * 100;

        if ($percent < 0) {
            $percent = 0;
        }

        return round($percent, 1);
    }

    /**
     * Get approximate memory usage percentage using `free -m`.
     */
    private function getMemoryUsagePercent(): float
    {
        $output = [];
        @exec('free -m 2>/dev/null', $output);

        if (! isset($output[1])) {
            return 0.0;
        }

        $line  = trim($output[1]);
        $parts = preg_split('/\s+/', $line);

        $total = isset($parts[1]) ? (int) $parts[1] : 0;
        $used  = isset($parts[2]) ? (int) $parts[2] : 0;

        if ($total <= 0) {
            return 0.0;
        }

        $percent = ($used / $total) * 100;

        return round($percent, 1);
    }

    /**
     * Get approximate disk usage percentage for a given path.
     */
    private function getDiskUsagePercent(string $path = '/'): float
    {
        $total = @disk_total_space($path);
        $free  = @disk_free_space($path);

        if ($total === false || $free === false || $total <= 0) {
            return 0.0;
        }

        $used    = $total - $free;
        $percent = ($used / $total) * 100;

        return round($percent, 1);
    }
}
