<?php

namespace App\Controller\Task;

use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Get All Tasks Controller.
 */
class GetAllTasks extends BaseTask
{
    /**
     * Get all tasks.
     *
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     */
    public function __invoke($request, $response, $args)
    {
        $this->setParams($request, $response, $args);
        $input = $this->getInput();
        $tasks = $this->getTaskService()->getTasks($input['decoded']->sub);

        return $this->jsonResponse('success', $tasks, 200);
    }
}
