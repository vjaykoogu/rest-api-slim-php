<?php declare(strict_types=1);

namespace App\Repository;

use App\Exception\TaskException;

class TaskRepository extends BaseRepository
{
    public function __construct(\PDO $database)
    {
        $this->database = $database;
    }

    public function checkAndGetTask(int $taskId, int $userId)
    {
        $query = 'SELECT * FROM tasks WHERE id = :id AND userId = :userId';
        $statement = $this->getDb()->prepare($query);
        $statement->bindParam('id', $taskId);
        $statement->bindParam('userId', $userId);
        $statement->execute();
        $task = $statement->fetchObject();
        if (empty($task)) {
            throw new TaskException('Task not found.', 404);
        }

        return $task;
    }

    public function getTasks(int $userId): array
    {
        $query = 'SELECT * FROM tasks WHERE userId = :userId ORDER BY id';
        $statement = $this->getDb()->prepare($query);
        $statement->bindParam('userId', $userId);
        $statement->execute();

        return $statement->fetchAll();
    }

    public function searchTasks(string $tasksName, int $userId): array
    {
        $query = 'SELECT * FROM tasks WHERE UPPER(name) LIKE :name AND userId = :userId ORDER BY id';
        $name = '%' . $tasksName . '%';
        $statement = $this->getDb()->prepare($query);
        $statement->bindParam('name', $name);
        $statement->bindParam('userId', $userId);
        $statement->execute();
        $tasks = $statement->fetchAll();
        if (!$tasks) {
            throw new TaskException('Task name not found.', 404);
        }

        return $tasks;
    }

    public function createTask($task)
    {
        $query = 'INSERT INTO tasks (name, status, userId) VALUES (:name, :status, :userId)';
        $statement = $this->getDb()->prepare($query);
        $statement->bindParam('name', $task->name);
        $statement->bindParam('status', $task->status);
        $statement->bindParam('userId', $task->userId);
        $statement->execute();

        return $this->checkAndGetTask((int) $this->database->lastInsertId(), (int) $task->userId);
    }

    public function updateTask($task)
    {
        $query = 'UPDATE tasks SET name=:name, status=:status WHERE id=:id AND userId = :userId';
        $statement = $this->getDb()->prepare($query);
        $statement->bindParam('id', $task->id);
        $statement->bindParam('name', $task->name);
        $statement->bindParam('status', $task->status);
        $statement->bindParam('userId', $task->userId);
        $statement->execute();

        return $this->checkAndGetTask((int) $task->id, (int) $task->userId);
    }

    public function deleteTask(int $taskId, int $userId): string
    {
        $query = 'DELETE FROM tasks WHERE id = :id AND userId = :userId';
        $statement = $this->getDb()->prepare($query);
        $statement->bindParam('id', $taskId);
        $statement->bindParam('userId', $userId);
        $statement->execute();

        return 'The task was deleted.';
    }
}
