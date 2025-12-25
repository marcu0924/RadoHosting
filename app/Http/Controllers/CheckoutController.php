<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use App\Models\MinecraftServer;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Process;

class CheckoutController extends Controller
{
    public function index(string $slug)
    {
        $plans = $this->minecraftPlans();

        $plan = collect($plans)->firstWhere('slug', $slug);

        if (! $plan) {
            throw new NotFoundHttpException();
        }

        return view('pages.checkout.index', compact('plan'));
    }

    public function success()
    {
        return view('pages.checkout.success');
    }

    private function minecraftPlans(): array
    {
        return [
            [
                'name' => 'Starter',
                'slug' => 'starter',
                'price' => 5.00,
                'ram' => '2GB',
                'cpu' => '1 vCPU',
                'storage' => '20GB',
                'slots' => '10',
                'description' => 'Perfect for small survival worlds with friends.',
                'popular' => false,
            ],
            [
                'name' => 'Survivor',
                'slug' => 'survivor',
                'price' => 10.00,
                'ram' => '4GB',
                'cpu' => '2 vCPU',
                'storage' => '40GB',
                'slots' => '20',
                'description' => 'Balanced performance for growing communities.',
                'popular' => true,
            ],
            [
                'name' => 'Creator',
                'slug' => 'creator',
                'price' => 18.00,
                'ram' => '8GB',
                'cpu' => '3 vCPU',
                'storage' => '80GB',
                'slots' => '50',
                'description' => 'Ideal for modded servers and content creators.',
                'popular' => false,
            ],
            [
                'name' => 'Network',
                'slug' => 'network',
                'price' => 30.00,
                'ram' => '16GB',
                'cpu' => '4 vCPU',
                'storage' => '160GB',
                'slots' => 'Unlimited',
                'description' => 'Built for large communities and server networks.',
                'popular' => false,
            ],
        ];
    }
    public function provision(Request $request, string $slug)
    {
        $plans = $this->minecraftPlans();
        $plan  = collect($plans)->firstWhere('slug', $slug);

        if (! $plan) {
            abort(404);
        }

        // Basic validation for server name (you can expand later)
        $data = $request->validate([
            'name' => ['required', 'string', 'max:40'],
        ]);

        // Convert plan strings to what your docker env expects
        // Example: "2GB" -> 2, "1 vCPU" -> 1
        $ramGb = (int) preg_replace('/\D+/', '', $plan['ram']);
        $cpu   = (int) preg_replace('/\D+/', '', $plan['cpu']);

        // Choose a port (simple version)
        $port = $this->nextAvailablePort(25565, 25650);

        $containerName = 'mc-' . Str::slug($data['name']) . '-' . Str::lower(Str::random(6));

        $env = [
            'EULA'        => 'TRUE',
            'MEMORY'      => "{$ramGb}G",
            'MAX_PLAYERS' => is_numeric($plan['slots']) ? (string)$plan['slots'] : '20',
            // add more itzg vars later (TYPE, VERSION, SEED, etc)
        ];

        try {
            // Create DB row FIRST (owned by user)
            $server = MinecraftServer::create([
                'user_id'        => $request->user()->id,   // ✅ ownership
                'name'           => $data['name'],
                'ram'            => $ramGb,
                'cpu'            => $cpu,
                'port'           => $port,
                'running'        => false,
                'environment'    => $env,
                'container_name' => $containerName,
                'world_path'     => null,
            ]);

            // Provision docker container (matches your admin approach; adjust image/options)
            $this->dockerCreateMinecraftContainer($server);

            // Update running state after creation
            $server->update(['running' => true]);

            return redirect()
                ->route('checkout.success', ['server_id' => $server->id])
                ->with('status', 'Server provisioned!');

            } catch (\Throwable $e) {
                return back()->with('error', $e->getMessage());
            }
    }
    /**
     * Creates the container using docker CLI (itzg/minecraft-server).
     * Adjust flags to match your production conventions.
     */
    private function dockerCreateMinecraftContainer(MinecraftServer $server): void
    {
        $container = escapeshellarg($server->container_name);
        $port      = (int) $server->port;

        // build -e flags
        $envFlags = '';
        foreach (($server->environment ?? []) as $k => $v) {
            $envFlags .= ' -e ' . escapeshellarg($k . '=' . $v);
        }

        // Example: persistent volume path (customize to your layout)
        // If you already do this in admin store(), copy that exactly.
        $volumePath = "/home/minecraft/{$server->container_name}";
        $volumeFlag = ' -v ' . escapeshellarg($volumePath . ':/data');

        $cmd = "docker run -d --name {$container} -p {$port}:25565 {$envFlags} {$volumeFlag} itzg/minecraft-server";

        $process = Process::fromShellCommandline($cmd);
        $process->setTimeout(120);
        $process->run();

        if (! $process->isSuccessful()) {
            // cleanup partial container
            Process::fromShellCommandline("docker rm -f " . escapeshellarg($server->container_name))
                ->run();

            throw new \RuntimeException(
                "docker run failed\n\nCMD:\n{$cmd}\n\nSTDERR:\n" . $process->getErrorOutput() . "\n\nSTDOUT:\n" . $process->getOutput()
            );
        }
    }

    /**
     * Very simple port picker. Replace later with a more robust allocator.
     */
    private function nextAvailablePort(int $start, int $end): int
    {
        $usedInDb = MinecraftServer::whereBetween('port', [$start, $end])
            ->pluck('port')
            ->all();

        $usedInDb = array_flip($usedInDb);

        for ($port = $start; $port <= $end; $port++) {
            if (isset($usedInDb[$port])) {
                continue;
            }

            // check OS-level availability
            $process = new \Symfony\Component\Process\Process([
                'bash', '-c', "ss -ltn sport = :$port | grep -q LISTEN"
            ]);
            $process->run();

            if (! $process->isSuccessful()) {
                // port is NOT listening → safe to use
                return $port;
            }
        }

        throw new \RuntimeException("No available ports between {$start}-{$end}");
    }

}
