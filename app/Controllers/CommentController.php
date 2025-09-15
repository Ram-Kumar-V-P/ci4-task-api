<?php

namespace App\Controllers;

use App\Libraries\ActivityLogger;
use App\Models\CommentModel;
use App\Models\TaskModel;
use CodeIgniter\HTTP\ResponseInterface;

class CommentController extends BaseController
{
    public function index(int $taskId)
    {
        if (! (new TaskModel())->find($taskId)) {
            return respondFail('Task not found', ResponseInterface::HTTP_NOT_FOUND);
        }
        $rows = (new CommentModel())->where('task_id',$taskId)->orderBy('id','ASC')->findAll();
        return respondSuccess($rows);
    }

    public function create(int $taskId)
    {
        if (! (new TaskModel())->find($taskId)) {
            return respondFail('Task not found', ResponseInterface::HTTP_NOT_FOUND);
        }
        $rules = [
            'content' => 'required|min_length[1]|max_length[2000]'
        ];
        if (! $this->validate($rules)) {
            return respondFail('Validation failed', ResponseInterface::HTTP_UNPROCESSABLE_ENTITY, $this->validator->getErrors());
        }

        $input = (array)$this->request->getJSON();

        $u = service('request')->user;
        $id = (new CommentModel())->insert([
            'task_id' => $taskId,
            'user_id' => $u['id'],
            'content' => trim($input['content'] ?? ''),
        ]);
        ActivityLogger::log($u['id'], 'comment_added', $taskId, ['comment_id' => $id]);
        return respondSuccess(['id' => $id], ResponseInterface::HTTP_CREATED);
    }
}
