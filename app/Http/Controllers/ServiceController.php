<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    /**
     * Get list of all services
     */
    public function list()
    {
        $services = \App\Models\Service::select('service_id', 'service_name')
            ->orderBy('service_name')
            ->get();
            
        return response()->json($services);
    }
}
