<?php

namespace App\Http\Controllers;

use App\Models\NotificationTemplate;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationTemplateController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = NotificationTemplate::with('tenant');

        if ($request->filled('tenant_id')) {
            $query->where('tenant_id', $request->tenant_id);
        }

        return response()->json($query->get());
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'tenant_id' => 'required|integer|exists:pgsql.tenants,id',
            'code'      => 'required|string|max:100',
            'type'      => 'required|string|max:50',
            'content'   => 'required|string',
            'config'    => 'nullable|array',
            'is_active' => 'boolean',
        ]);

        $template = NotificationTemplate::create($data);

        return response()->json($template->load('tenant'), 201);
    }

    public function show(NotificationTemplate $notificationTemplate): JsonResponse
    {
        return response()->json($notificationTemplate->load('tenant'));
    }

    public function update(Request $request, NotificationTemplate $notificationTemplate): JsonResponse
    {
        $data = $request->validate([
            'tenant_id' => 'sometimes|required|integer|exists:pgsql.tenants,id',
            'code'      => 'sometimes|required|string|max:100',
            'type'      => 'sometimes|required|string|max:50',
            'content'   => 'sometimes|required|string',
            'config'    => 'nullable|array',
            'is_active' => 'boolean',
        ]);

        $notificationTemplate->update($data);

        return response()->json($notificationTemplate->load('tenant'));
    }

    public function destroy(NotificationTemplate $notificationTemplate): JsonResponse
    {
        $notificationTemplate->delete();

        return response()->json(null, 204);
    }
}
