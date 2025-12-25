<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MinecraftServer;

class ServerDashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        // Admin can see all; users only see theirs
        $query = MinecraftServer::query()->latest();

        if ($user->role !== 'admin') {
            $query->where('user_id', $user->id);
        }

        $servers = $query->paginate(12);

        return view('pages.servers.index', compact('servers'));
    }

    public function show(Request $request, MinecraftServer $server)
    {
        $this->authorize('view', $server);

        // You already compute these on your admin show page:
        // $dockerStatus, $externalIp, $connectionAddress
        // Keep using your existing service/helper methods here.
        $dockerStatus = $this->dockerStatus($server);
        $externalIp = $this->externalIp();
        $connectionAddress = $externalIp . ':' . $server->port;

        return view('pages.servers.show', compact('server', 'dockerStatus', 'externalIp', 'connectionAddress'));
    }

    public function start(Request $request, MinecraftServer $server)
    {
        $this->authorize('control', $server);

        $this->dockerStart($server);

        return back()->with('status', 'Server starting...');
    }

    public function stop(Request $request, MinecraftServer $server)
    {
        $this->authorize('control', $server);

        $this->dockerStop($server);

        return back()->with('status', 'Server stopping...');
    }

    public function restart(Request $request, MinecraftServer $server)
    {
        $this->authorize('control', $server);

        $this->dockerRestart($server);

        return back()->with('status', 'Server restarting...');
    }

    public function consoleLogs(Request $request, MinecraftServer $server)
    {
        $this->authorize('view', $server);

        return response()->json([
            'output' => $this->dockerLogs($server),
        ]);
    }

    public function consoleSend(Request $request, MinecraftServer $server)
    {
        $this->authorize('control', $server);

        $data = $request->validate([
            'command' => ['required', 'string', 'max:200'],
        ]);

        $ok = $this->dockerSendCommand($server, $data['command']);

        return back()->with($ok ? 'status' : 'error', $ok ? 'Command sent.' : 'Failed to send command.');
    }

    /*
    |--------------------------------------------------------------------------
    | Replace these stubs with your existing Docker logic
    |--------------------------------------------------------------------------
    | You already have this working on admin pages; move it into a service
    | (e.g., App\Services\DockerService) and call it from both controllers.
    */
    private function dockerStatus(MinecraftServer $server): string { return 'unknown'; }
    private function externalIp(): string { return '0.0.0.0'; }
    private function dockerStart(MinecraftServer $server): void {}
    private function dockerStop(MinecraftServer $server): void {}
    private function dockerRestart(MinecraftServer $server): void {}
    private function dockerLogs(MinecraftServer $server): string { return ''; }
    private function dockerSendCommand(MinecraftServer $server, string $command): bool { return true; }
}
