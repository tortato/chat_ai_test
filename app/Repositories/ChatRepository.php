<?php

namespace App\Repositories;

use App\Models\Chat;

class ChatRepository implements ChatRepositoryInterface
{
    public function getAll($userId, $pagination = 10)
    {
        return Chat::where('user_id', $userId)->latest()->paginate($pagination);
    }

    public function store(array $data)
    {
        return Chat::create($data);
    }

    public function update(int $chatId, array $data)
    {
        $chat = Chat::findOrFail($chatId);
        return $chat->update($data);
    }

    public function find($userId, $id)
    {
        return Chat::where('user_id', $userId)->where('id', $id)->firstOrFail();
    }

    public function delete($userId, $id) {
        Chat::where('id', $id)->where('user_id', $userId)->firstOrFail()->delete();
    }
}
