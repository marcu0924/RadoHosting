<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\MinecraftServer;
use Illuminate\Support\Facades\Log;

class MinecraftServerController extends Controller
{
    protected string $basePath = '/home/marcus/docker/servers/minecraft';

    /**
     * Display a listing of the Minecraft servers.
     */
    public function index()
    {
        $servers = MinecraftServer::orderByDesc('created_at')->get();

        return view('admin.minecraft.index', compact('servers'));
    }

    /**
     * Show a simple form to create a Minecraft server.
     */
    public function create()
    {
        // Allowed port range
        $start = 25565;
        $end   = 25650;

        // Ports already taken in the DB
        $taken = MinecraftServer::pluck('port')->toArray();

        // Build list of available ports in that range
        $availablePorts = [];
        for ($p = $start; $p <= $end; $p++) {
            if (! in_array($p, $taken)) {
                $availablePorts[] = $p;
            }
        }

        return view('admin.minecraft.create', compact('availablePorts'));
    }

    /**
     * Handle the form submission and spin up a Dockerized Minecraft server.
     */
    public function store(Request $request)
    {
        // 1. Validate input
        $data = $request->validate([
            'name'    => 'required|string|max:50',
            'port'    => 'required|integer|between:25565,25650|unique:minecraft_servers,port',
            'memory'  => 'required|integer|min:1|max:32', // GB
            'seed'    => 'nullable|string|max:191',
            'version' => 'nullable|string|max:20',        // e.g. "1.21.1"
        ]);

        // 2. Paths & naming
        $basePath = $this->basePath;

        if (! is_dir($basePath)) {
            if (! @mkdir($basePath, 0770, true) && ! is_dir($basePath)) {
                return back()->withErrors([
                    'server' => 'Failed to create base directory: '.$basePath,
                ]);
            }
        }

        $slug = Str::slug($data['name']);
        if ($slug === '') {
            $slug = 'server-'.time();
        }

        $serverDir = $basePath.'/'.$slug;

        if (is_dir($serverDir)) {
            $serverDir .= '-'.time();
        }

        if (! @mkdir($serverDir, 0770, true) && ! is_dir($serverDir)) {
            return back()->withErrors([
                'server' => 'Failed to create server directory: '.$serverDir,
            ]);
        }

        // 3. Check Docker access first (permissions / installed)
        $dockerCheckOutput = [];
        $dockerCheckCode   = 0;

        $userInfoOutput = [];
        exec('whoami 2>&1; id 2>&1; ls -l /var/run/docker.sock 2>&1', $userInfoOutput);

        exec('docker info 2>&1', $dockerCheckOutput, $dockerCheckCode);

        if ($dockerCheckCode !== 0) {
            return back()->withErrors([
                'server' =>
                    "Docker is not available or this PHP user doesn't have permission.\n\n".
                    "=== User / Groups / Socket ===\n".
                    implode("\n", $userInfoOutput)."\n\n".
                    "=== docker info output ===\n".
                    implode("\n", $dockerCheckOutput),
            ]);
        }

        // 4. Build docker run command
        $containerName = 'rado_mc_'.$slug;
        $dockerImage   = 'itzg/minecraft-server:latest';

        $port        = (int) $data['port'];
        $memoryGb    = (int) $data['memory'];
        $memoryParam = $memoryGb.'G';

        // env key/value pairs
        $env = [
            'EULA'   => 'TRUE',
            'MEMORY' => $memoryParam,
            'UID'    => 7778, // marcus
            'GID'    => 100,  // users
        ];

        if (! empty($data['seed'])) {
            $env['LEVEL_SEED'] = $data['seed'];
        }

        if (! empty($data['version'])) {
            $env['VERSION'] = $data['version'];
        }

        // Construct env flags: -e 'KEY=VALUE' ...
        $envFlags = '';
        foreach ($env as $key => $value) {
            $envFlags .= ' -e '.escapeshellarg($key.'='.$value);
        }

        $escapedContainerName = escapeshellarg($containerName);
        $escapedServerDir     = escapeshellarg($serverDir);
        $escapedImage         = escapeshellarg($dockerImage);

        $dockerCmd = "docker run -d ".
                     "--name {$escapedContainerName} ".
                     "-p {$port}:25565 ".
                     "{$envFlags} ".
                     "-v {$escapedServerDir}:/data ".
                     "--restart unless-stopped ".
                     $escapedImage;

        // 5. Execute docker run
        $output   = [];
        $exitCode = 0;

        exec($dockerCmd.' 2>&1', $output, $exitCode);

        if ($exitCode !== 0) {
            return back()->withErrors([
                'server' => "Failed to start Docker container.\n\nCommand:\n{$dockerCmd}\n\nOutput:\n".implode("\n", $output),
            ]);
        }

        // 6. Persist info so the admin panel can see it
        $server = MinecraftServer::create([
            'name'           => $data['name'],
            'ram'            => $memoryGb,
            'cpu'            => 1,
            'port'           => $port,
            'running'        => true,
            'environment'    => $env,
            'container_name' => $containerName,
            'world_path'     => $serverDir,
        ]);

        return redirect()
            ->route('admin.index')
            ->with('status', "Minecraft server '{$server->name}' is starting on port {$port}.");
    }

    public function show(MinecraftServer $server)
    {
        $dockerStatus = $this->getContainerStatus($server);

        if ($dockerStatus === null) {
            $dockerStatus = $server->running ? 'running' : 'stopped';
        }

        return view('admin.minecraft.show', [
            'server'       => $server,
            'dockerStatus' => $dockerStatus,
        ]);
    }

    public function start(MinecraftServer $server)
    {
        $container = $server->container_name;

        if (! $container) {
            return back()->withErrors(['server' => 'No container name stored for this server.']);
        }

        $cmd    = 'docker start ' . escapeshellarg($container) . ' 2>&1';
        $output = [];
        $code   = 0;

        exec($cmd, $output, $code);

        if ($code !== 0) {
            Log::error('Failed to start Docker container', [
                'server_id'      => $server->id,
                'container_name' => $container,
                'code'           => $code,
                'output'         => implode("\n", $output),
            ]);

            return back()->withErrors([
                'server' => "Failed to start container {$container}.\n\n" . implode("\n", $output),
            ]);
        }

        $state = $this->getContainerStatus($server);
        $server->update(['running' => $state === 'running']);

        Log::info('Started Docker container for MinecraftServer', [
            'server_id'      => $server->id,
            'container_name' => $container,
            'state'          => $state,
        ]);

        return back()->with('status', "Server {$server->name} started.");
    }

    public function stop(MinecraftServer $server)
    {
        $container = $server->container_name;

        if (! $container) {
            return back()->withErrors(['server' => 'No container name stored for this server.']);
        }

        $cmd    = 'docker stop ' . escapeshellarg($container) . ' 2>&1';
        $output = [];
        $code   = 0;

        exec($cmd, $output, $code);

        if ($code !== 0) {
            Log::error('Failed to stop Docker container', [
                'server_id'      => $server->id,
                'container_name' => $container,
                'code'           => $code,
                'output'         => implode("\n", $output),
            ]);

            return back()->withErrors([
                'server' => "Failed to stop container {$container}.\n\n" . implode("\n", $output),
            ]);
        }

        $state = $this->getContainerStatus($server);
        $server->update(['running' => $state === 'running']);

        Log::info('Stopped Docker container for MinecraftServer', [
            'server_id'      => $server->id,
            'container_name' => $container,
            'state'          => $state,
        ]);

        return back()->with('status', "Server {$server->name} stopped.");
    }

    public function restart(MinecraftServer $server)
    {
        $container = $server->container_name;

        if (! $container) {
            return back()->withErrors(['server' => 'No container name stored for this server.']);
        }

        $cmd    = 'docker restart ' . escapeshellarg($container) . ' 2>&1';
        $output = [];
        $code   = 0;

        exec($cmd, $output, $code);

        if ($code !== 0) {
            Log::error('Failed to restart Docker container', [
                'server_id'      => $server->id,
                'container_name' => $container,
                'code'           => $code,
                'output'         => implode("\n", $output),
            ]);

            return back()->withErrors([
                'server' => "Failed to restart container {$container}.\n\n" . implode("\n", $output),
            ]);
        }

        $state = $this->getContainerStatus($server);
        $server->update(['running' => $state === 'running']);

        Log::info('Restarted Docker container for MinecraftServer', [
            'server_id'      => $server->id,
            'container_name' => $container,
            'state'          => $state,
        ]);

        return back()->with('status', "Server {$server->name} restarted.");
    }

    public function destroy(MinecraftServer $server)
    {
        $name          = $server->name;
        $containerName = $server->container_name;
        $worldPath     = $server->world_path ?? null;

        // 1) Remove Docker container
        if ($containerName) {
            try {
                $cmd    = 'docker rm -f ' . escapeshellarg($containerName) . ' 2>&1';
                $output = shell_exec($cmd);

                Log::info('Docker container removed for MinecraftServer', [
                    'server_id'      => $server->id,
                    'container_name' => $containerName,
                    'output'         => $output,
                ]);
            } catch (\Throwable $e) {
                Log::error('Failed to remove Docker container for MinecraftServer', [
                    'server_id'      => $server->id,
                    'container_name' => $containerName,
                    'error'          => $e->getMessage(),
                ]);
            }
        }

        // 2) Delete server directory on disk
        if ($worldPath && is_dir($worldPath)) {
            try {
                $this->deleteDirectoryRecursive($worldPath);
            } catch (\Throwable $e) {
                Log::error('Failed to delete world directory for MinecraftServer', [
                    'server_id'  => $server->id,
                    'world_path' => $worldPath,
                    'error'      => $e->getMessage(),
                ]);
            }
        }

        // 3) Delete DB row
        $server->delete();

        return redirect()
            ->route('admin.minecraft.index')
            ->with('status', "Minecraft server '{$name}' deleted from records.");
    }

    protected function deleteDirectoryRecursive(string $path): void
    {
        if (! is_dir($path)) {
            return;
        }

        $real = realpath($path);
        if ($real === false) {
            return;
        }

        $base = $this->basePath;

        if (! str_starts_with($real, $base)) {
            Log::warning('Refused to delete directory outside base path', [
                'requested' => $path,
                'real'      => $real,
                'base'      => $base,
            ]);
            return;
        }

        $output = [];
        $code   = 0;
        $cmd    = 'rm -rf ' . escapeshellarg($real) . ' 2>&1';

        exec($cmd, $output, $code);

        Log::info('rm -rf world directory', [
            'path'   => $real,
            'code'   => $code,
            'output' => implode("\n", $output),
        ]);
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
            Log::warning('Failed to get Docker status for MinecraftServer', [
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
