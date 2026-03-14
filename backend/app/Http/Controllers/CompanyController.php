<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CompanyController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(Company::all());
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name'      => 'required|string|max:255',
            'nit'       => 'required|string|max:100|unique:pgsql.companies,nit',
            'address'   => 'nullable|string|max:255',
            'phone'     => 'nullable|string|max:50',
            'email'     => 'required|email|max:255|unique:pgsql.companies,email',
            'website'   => 'nullable|url|max:255',
            'logo'      => 'nullable|string|max:255',
            'ceo_name'  => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ]);

        $company = Company::create($data);

        return response()->json($company, 201);
    }

    public function show(Company $company): JsonResponse
    {
        return response()->json($company);
    }

    public function update(Request $request, Company $company): JsonResponse
    {
        $data = $request->validate([
            'name'      => 'sometimes|required|string|max:255',
            'nit'       => ['sometimes', 'required', 'string', 'max:100', Rule::unique('pgsql.companies', 'nit')->ignore($company->id)],
            'address'   => 'nullable|string|max:255',
            'phone'     => 'nullable|string|max:50',
            'email'     => ['sometimes', 'required', 'email', 'max:255', Rule::unique('pgsql.companies', 'email')->ignore($company->id)],
            'website'   => 'nullable|url|max:255',
            'logo'      => 'nullable|string|max:255',
            'ceo_name'  => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ]);

        $company->update($data);

        return response()->json($company);
    }

    public function destroy(Company $company): JsonResponse
    {
        $company->delete();

        return response()->json(null, 204);
    }
}
