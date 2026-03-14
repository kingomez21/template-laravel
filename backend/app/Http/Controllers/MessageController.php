<?php

namespace App\Http\Controllers;

use App\Models\Message;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MessageController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(Message::all());
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'code'       => 'required|string|max:100|unique:pgsql.messages,code',
            'msg_es'     => 'required|string',
            'msg_en'     => 'nullable|string',
            'is_active'  => 'boolean',
            'created_by' => 'nullable|string',
            'updated_by' => 'nullable|string',
        ]);

        $message = Message::create($data);

        return response()->json($message, 201);
    }

    public function show(Message $message): JsonResponse
    {
        return response()->json($message);
    }

    public function update(Request $request, Message $message): JsonResponse
    {
        $data = $request->validate([
            'code'       => ['sometimes', 'required', 'string', 'max:100', Rule::unique('pgsql.messages', 'code')->ignore($message->id)],
            'msg_es'     => 'sometimes|required|string',
            'msg_en'     => 'nullable|string',
            'is_active'  => 'boolean',
            'updated_by' => 'nullable|string',
        ]);

        $message->update($data);

        return response()->json($message);
    }

    public function destroy(Message $message): JsonResponse
    {
        $message->delete();

        return response()->json(null, 204);
    }
}
