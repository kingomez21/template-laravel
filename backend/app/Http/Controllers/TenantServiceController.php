<?php

namespace App\Http\Controllers;

use App\Models\TenantService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TenantServiceController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = TenantService::with('tenant');

        if ($request->filled('tenant_id')) {
            $query->where('tenant_id', $request->tenant_id);
        }

        return response()->json($query->get());
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'tenant_id' => 'required|integer|exists:pgsql.tenants,id',
            'name'      => 'required|string|max:255',
            'type'      => 'nullable|string|max:50',
            'config'    => 'nullable|array',
            'is_active' => 'boolean',
        ]);

        $service = TenantService::create($data);

        return response()->json($service->load('tenant'), 201);
    }

    public function show(TenantService $tenantService): JsonResponse
    {
        return response()->json($tenantService->load('tenant'));
    }

    public function update(Request $request, TenantService $tenantService): JsonResponse
    {
        $data = $request->validate([
            'tenant_id' => 'sometimes|required|integer|exists:pgsql.tenants,id',
            'name'      => 'sometimes|required|string|max:255',
            'type'      => 'nullable|string|max:50',
            'config'    => 'nullable|array',
            'is_active' => 'boolean',
        ]);

        $tenantService->update($data);

        return response()->json($tenantService->load('tenant'));
    }

    public function destroy(TenantService $tenantService): JsonResponse
    {
        $tenantService->delete();

        return response()->json(null, 204);
    }
}
