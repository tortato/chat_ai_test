<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\AIService;
use App\Services\ChatService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * @OA\Info(
 *     title="Energy Market Chat API",
 *     version="1.0.0",
 *     description="API for managing chat interactions and streaming energy market-focused AI responses."
 * )
 *
 * @OA\Server(
 *     url="/api",
 *     description="API Server"
 * )
 */
class ChatController extends Controller
{
    protected $chatService;
    protected $aiService;

    public function __construct(ChatService $chatService, AIService $aiService)
    {
        $this->chatService = $chatService;
        $this->aiService = $aiService;
    }

    /**
     * @OA\Get(
     *     path="/chats",
     *     summary="Get all user chats",
     *     tags={"Chats"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="paginate",
     *         in="query",
     *         description="Pagination size",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="A list of chats",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="array", @OA\Items(
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="user_message", type="string", example="What is renewable energy?"),
     *                 @OA\Property(property="ai_response", type="string", example="Renewable energy is energy from sources that are naturally replenished."),
     *                 @OA\Property(property="created_at", type="string", format="date-time"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time")
     *             ))
     *         )
     *     )
     * )
     */
    public function index(Request $request)
    {
        $userId = auth()->id();
        return $this->chatService->getAll($userId, $request->paginate ?? null);
    }

    /**
     * @OA\Post(
     *     path="/chats",
     *     summary="Store a new chat message",
     *     tags={"Chats"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"user_message"},
     *             @OA\Property(property="user_message", type="string", example="Tell me about the energy market?")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Chat created and AI response streamed",
     *         @OA\MediaType(mediaType="text/event-stream")
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid input"
     *     )
     * )
     */
    public function store(Request $request)
    {
        $validated = $request->validate(['user_message' => 'required|string']);
        $userMessage = $validated['user_message'];
        $userId = auth()->id();

        $response = new StreamedResponse(function () use ($userMessage, $userId, $validated) {
            $fullResponse = $this->aiService->queryAIStream($userMessage, function ($chunk) {
                echo $chunk;
                ob_flush();
                flush();
            });

            $this->chatService->create([
                'user_id' => $userId,
                'user_message' => $validated['user_message'],
                'ai_response' => $fullResponse,
            ]);
        });

        $response->headers->set('Content-Type', 'text/event-stream');
        $response->headers->set('Cache-Control', 'no-cache');
        $response->headers->set('Connection', 'keep-alive');

        return $response;
    }

    /**
     * @OA\Put(
     *     path="/chats/{chatId}",
     *     summary="Update a chat message by ID",
     *     tags={"Chats"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="chatId",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"user_message"},
     *             @OA\Property(property="user_message", type="string", example="What is the latest in solar energy?")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Updated chat with AI response streamed",
     *         @OA\MediaType(mediaType="text/event-stream")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Chat not found"
     *     )
     * )
     */
    public function update($chatId, Request $request)
    {
        $this->chatService->getById(auth()->id(), $chatId);

        $validated = $request->validate(['user_message' => 'required|string']);
        $userMessage = $validated['user_message'];

        $response = new StreamedResponse(function () use ($userMessage, $chatId, $validated) {
            $fullResponse = $this->aiService->queryAIStream($userMessage, function ($chunk) {
                echo $chunk;
                ob_flush();
                flush();
            });

            $this->chatService->update($chatId, [
                'user_message' => $validated['user_message'],
                'ai_response' => $fullResponse,
            ]);
        });

        $response->headers->set('Content-Type', 'text/event-stream');
        $response->headers->set('Cache-Control', 'no-cache');
        $response->headers->set('Connection', 'keep-alive');

        return $response;
    }

    /**
     * @OA\Get(
     *     path="/chats/{id}",
     *     summary="Get a specific chat by ID",
     *     tags={"Chats"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="The requested chat",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="user_message", type="string", example="What is renewable energy?"),
     *             @OA\Property(property="ai_response", type="string", example="Renewable energy is energy from sources that are naturally replenished."),
     *             @OA\Property(property="created_at", type="string", format="date-time"),
     *             @OA\Property(property="updated_at", type="string", format="date-time")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Chat not found"
     *     )
     * )
     */
    public function show($id)
    {
        return $this->chatService->getById(auth()->id(), $id);
    }

    /**
     * @OA\Delete(
     *     path="/chats/{id}",
     *     summary="Delete a chat by ID",
     *     tags={"Chats"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Chat successfully deleted"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Chat not found"
     *     )
     * )
     */
    public function destroy($id)
    {
        return $this->chatService->deleteById(auth()->id(), $id);
    }
}
