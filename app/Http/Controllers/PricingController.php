<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PricingController extends Controller
{
    public function minecraft()
    {
        // Hardcoded for now (DB later)
        $plans = [
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

        return view('pages.pricing.minecraft', compact('plans'));
    }
}
