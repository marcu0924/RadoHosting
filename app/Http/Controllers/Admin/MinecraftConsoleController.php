<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MinecraftServer;
use Illuminate\Http\Request;
use Symfony\Component\Process\Process;

class MinecraftConsoleController extends Controller
{
    public function logs(MinecraftServer $server)
    {
        $container = $server->container_name; // adjust to your column name

        // Get the last 200 lines of logs
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

    public function send(MinecraftServer $server, Request $request)
    {
        $data = $request->validate([
            'command' => 'required|string|max:255',
        ]);

        $container = $server->container_name;
        $command   = $data['command'];

        // Use rcon-cli inside the container
        $process = Process::fromShellCommandline(
            "docker exec {$container} rcon-cli {$command}"
        );

        $process->run();

        if (! $process->isSuccessful()) {
            return back()->with('error', 'Failed to send command: ' . $process->getErrorOutput());
        }

        return back()->with('status', "Command sent: {$command}");
    }
}
