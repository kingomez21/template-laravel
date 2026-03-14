<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class TenantController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(Tenant::with('company')->get());
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'company_id' => 'required|integer|exists:pgsql.companies,id',
            'name'       => 'required|string|max:255',
            'schema'     => 'required|string|max:63|unique:pgsql.tenants,schema|regex:/^[a-z0-9_]+$/',
            'subdomain'  => 'nullable|string|max:63|unique:pgsql.tenants,subdomain|regex:/^[a-z0-9_-]+$/',
            'lang'       => 'nullable|string|max:10',
            'is_active'  => 'boolean',
        ]);

        $data['guid'] = Str::uuid()->toString();

        $tenant = Tenant::create($data);

        return response()->json($tenant->load('company'), 201);
    }

    public function show(Tenant $tenant): JsonResponse
    {
        return response()->json($tenant->load('company'));
    }

    public function update(Request $request, Tenant $tenant): JsonResponse
    {
        $data = $request->validate([
            'company_id' => 'sometimes|required|integer|exists:pgsql.companies,id',
            'name'       => 'sometimes|required|string|max:255',
            'schema'     => ['sometimes', 'required', 'string', 'max:63', 'regex:/^[a-z0-9_]+$/', Rule::unique('pgsql.tenants', 'schema')->ignore($tenant->id)],
            'subdomain'  => ['nullable', 'string', 'max:63', 'regex:/^[a-z0-9_-]+$/', Rule::unique('pgsql.tenants', 'subdomain')->ignore($tenant->id)],
            'lang'       => 'nullable|string|max:10',
            'is_active'  => 'boolean',
        ]);

        $tenant->update($data);

        return response()->json($tenant->load('company'));
    }

    public function destroy(Tenant $tenant): JsonResponse
    {
        $tenant->delete();

        return response()->json(null, 204);
    }
}
