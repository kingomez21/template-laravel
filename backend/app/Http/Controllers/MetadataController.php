<?php

namespace App\Http\Controllers;

use App\Models\Metadata;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MetadataController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Metadata::query();

        if ($request->filled('tenant')) {
            $query->where('tenant', $request->tenant);
        }

        if ($request->filled('entity')) {
            $query->where('entity', $request->entity);
        }

        return response()->json($query->get());
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $data = $request->validate([
                'tenant'     => 'required|string|max:100',
                'entity'     => 'required|string|max:100',
                'field'      => 'required|string|max:100',
                'name'       => 'required|string|max:255',
                'value'      => 'nullable|string',
                'type'       => 'nullable|string|max:50',
                'icon'       => 'nullable|string|max:100',
                'color'      => 'nullable|string|max:50',
                'notes'      => 'nullable|string',
                'config'     => 'nullable|array',
                'is_active'  => 'boolean',
                'created_by' => 'nullable|string',
                'updated_by' => 'nullable|string',
            ]);

            //validar request
            if (!$data)
                return response()->json(['message' => 'Invalid data'], 422);

            $metadata = Metadata::create($data);

            return response()->json($metadata, 201);
        } catch (\Throwable $th) {
            //throw $th;
             return response()->json(['message' => 'Error creating metadata', 'error' => $th->getMessage()], 500);
        }

    }

    public function show(Metadata $metadata): JsonResponse
    {
        return response()->json($metadata);
    }

    public function update(Request $request, Metadata $metadata): JsonResponse
    {
        try {
            $data = $request->validate([
                'tenant'     => 'sometimes|required|string|max:100',
                'entity'     => 'sometimes|required|string|max:100',
                'field'      => 'sometimes|required|string|max:100',
                'name'       => 'sometimes|required|string|max:255',
                'value'      => 'nullable|string',
                'type'       => 'nullable|string|max:50',
                'icon'       => 'nullable|string|max:100',
                'color'      => 'nullable|string|max:50',
                'notes'      => 'nullable|string',
                'config'     => 'nullable|array',
                'is_active'  => 'boolean',
                'updated_by' => 'nullable|string',
            ]);

            $metadata->update($data);

            return response()->json($metadata);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Error updating metadata', 'error' => $th->getMessage()], 500);
        }
    }

    public function destroy(Metadata $metadata): JsonResponse
    {
        $metadata->delete();

        return response()->json(null, 204);
    }
}
