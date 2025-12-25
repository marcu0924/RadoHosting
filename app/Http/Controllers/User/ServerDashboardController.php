<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\MinecraftServer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Process;

class ServerDashboardController extends Controller
{
    /**
     * IMPORTANT:
     * Replace with your real public IP or config('services.server.public_ip')
     */
    protected string $externalIp = '216.82.35.88';

    /**
     * List servers for the logged-in user.
     * Admin can see all servers.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        $query = MinecraftServer::query()->orderByDesc('created_at');

        // Users see only their servers
        if (! $this->isAdmin($user)) {
            $query->where('user_id', $user->id);
        }

        $servers = $query->paginate(12);

        return view('pages.servers.index', compact('servers'));
    }

    /**
     * Show a single server dashboard for the owner/admin.
     */
    public function show(Request $request, MinecraftServer $server)
    {
        $this->ensureOwnerOrAdmin($request, $server);

        $dockerStatus = $this->getContainerStatus($server);
        if ($dockerStatus === null) {
            $dockerStatus = $server->running ? 'running' : 'stopped';
        }

        $externalIp = $this->externalIp;
        $connectionAddress = $externalIp . ':' . $server->port;

        return view('pages.servers.show', [
            'server'            => $server,
            'dockerStatus'      => $dockerStatus,
            'connectionAddress' => $connectionAddress,
            'externalIp'        => $externalIp,
        ]);
    }

    /**
     * Start/Stop/Restart mirror your admin controller behavior.
     */
    public function start(Request $request, MinecraftServer $server)
    {
        $this->ensureOwnerOrAdmin($request, $server);
        return $this->dockerPowerAction($server, 'start', "Server {$server->name} started.");
    }

    public function stop(Request $request, MinecraftServer $server)
    {
        $this->ensureOwnerOrAdmin($request, $server);
        return $this->dockerPowerAction($server, 'stop', "Server {$server->name} stopped.");
    }

    public function restart(Request $request, MinecraftServer $server)
    {
        $this->ensureOwnerOrAdmin($request, $server);
        return $this->dockerPowerAction($server, 'restart', "Server {$server->name} restarted.");
    }

    /**
     * Console logs (same idea as Admin\MinecraftConsoleController::logs)
     */
    public function consoleLogs(Request $request, MinecraftServer $server)
    {
        $this->ensureOwnerOrAdmin($request, $server);

        $container = $server->container_name;

        $process = Process::fromShellCommandline("docker logs --tail=200 {$container}");
        $process->run();

        if (! $process->isSuccessful()) {
            return response()->json([
                'output' => "Failed to read logs:\n" . $process->getErrorOutput(),
            ], 500);
        }

        return response()->json([
            'output' => $process->getOutput(),
        ]);
    }

    /**
     * Send command via rcon-cli inside the container (same as your admin send)
     */
    public function consoleSend(Request $request, MinecraftServer $server)
    {
        $this->ensureOwnerOrAdmin($request, $server);

        $data = $request->validate([
            'command' => 'required|string|max:255',
        ]);

        $container = $server->container_name;
        $command   = $data['command'];

        $process = Process::fromShellCommandline(
            "docker exec {$container} rcon-cli {$command}"
        );

        $process->run();

        if (! $process->isSuccessful()) {
            return back()->with('error', 'Failed to send command: ' . $process->getErrorOutput());
        }

        return back()->with('status', "Command sent: {$command}");
    }

    /*
    |--------------------------------------------------------------------------
    | Internal helpers (no AuthServiceProvider needed)
    |--------------------------------------------------------------------------
    */

    private function isAdmin($user): bool
    {
        // Match your current app style: role column used elsewhere
        return ($user->role ?? null) === 'admin';
    }

    private function ensureOwnerOrAdmin(Request $request, MinecraftServer $server): void
    {
        $user = $request->user();

        // If you haven't added user_id yet, this will block all non-admin users.
        // Make sure minecraft_servers has user_id and it's set at creation time.
        $isOwner = ($server->user_id ?? null) === $user->id;

        if (! $this->isAdmin($user) && ! $isOwner) {
            abort(403, 'You do not have access to this server.');
        }
    }

    private function dockerPowerAction(MinecraftServer $server, string $action, string $successMessage)
    {
        $container = $server->container_name;

        if (! $container) {
            return back()->withErrors(['server' => 'No container name stored for this server.']);
        }

        $cmd    = 'docker ' . $action . ' ' . escapeshellarg($container) . ' 2>&1';
        $output = [];
        $code   = 0;

        exec($cmd, $output, $code);

        if ($code !== 0) {
            Log::error("Failed to {$action} Docker container (user dashboard)", [
                'server_id'      => $server->id,
                'container_name' => $container,
                'code'           => $code,
                'output'         => implode("\n", $output),
            ]);

            return back()->withErrors([
                'server' => "Failed to {$action} container {$container}.\n\n" . implode("\n", $output),
            ]);
        }

        $state = $this->getContainerStatus($server);
        $server->update(['running' => $state === 'running']);

        return back()->with('status', $successMessage);
    }

    private function getContainerStatus(MinecraftServer $server): ?string
    {
        if (! $server->container_name) {
            return null;
        }

        $container = escapeshellarg($server->container_name);
        $output    = [];
        $code      = 0;

        exec("docker inspect -f '{{.State.Status}}' {$container} 2>&1", $output, $code);

        if ($code !== 0 || empty($output)) {
            Log::warning('Failed to get Docker status for MinecraftServer (user dashboard)', [
                'server_id'      => $server->id,
                'container_name' => $server->container_name,
                'code'           => $code,
                'output'         => implode("\n", $output),
            ]);

            return null;
        }

        return trim($output[0], " \t\n\r\0\x0B'\"");
    }
}
