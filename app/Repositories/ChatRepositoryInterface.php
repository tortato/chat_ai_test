<?php

namespace App\Repositories;

interface ChatRepositoryInterface
{
    public function getAll(int $userId, $paginate);
    public function find(int $userId, int $chatId);
    public function store(array $data);
    public function update(int $chatId, array $data);
    public function delete(int $userId, int $chatId);
}
