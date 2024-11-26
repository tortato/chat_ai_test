<?php
namespace App\Services;

use App\Repositories\ChatRepositoryInterface;

class ChatService
{
    protected $chatRepository;

    public function __construct(ChatRepositoryInterface $chatRepository)
    {
        $this->chatRepository = $chatRepository;
    }

    /**
     * Get paginate chats ordered by recent to older
     *
     * @param int $userId
     * @param $pagination
     * @return array
     */
    public function getAll(int $userId, $pagination = 10)
    {
        return $this->chatRepository->getAll($userId, $pagination);
    }

    /**
     * Create chat
     *
     * @param array $data
     * @return mixed
     */
    public function create(array $data)
    {
        return $this->chatRepository->store($data);
    }

    /**
     * Update chat
     *
     * @param int $chatId
     * @param array $data
     * @return mixed
     */
    public function update(int $chatId, array $data)
    {
        return $this->chatRepository->update($chatId, $data);
    }

    /**
     * Get chat by UserId and ChatId
     *
     * @param int $userId
     * @param int $id
     * @return mixed
     */
    public function getById(int $userId, int $id)
    {
        return $this->chatRepository->find($userId, $id);
    }

    /**
     * Delete chat
     *
     * @param int $userId
     * @param int $id
     * @return mixed
     */
    public function deleteById(int $userId, int $id)
    {
        return $this->chatRepository->delete($userId, $id);
    }
}
