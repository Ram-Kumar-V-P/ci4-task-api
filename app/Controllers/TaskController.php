<?php

namespace App\Controllers;

use App\Libraries\ActivityLogger;
use App\Libraries\MailerService;
use App\Models\TaskModel;
use App\Models\UserModel;
use App\Models\TaskAssigneeModel;
use CodeIgniter\HTTP\ResponseInterface;

class TaskController extends BaseController
{
    public function index()
    {
        // Filters: status, priority, due_from, due_to; Pagination: limit, offset; Search: q
        $params = [
            'status'   => $this->request->getGet('status'),
            'priority' => $this->request->getGet('priority'),
            'due_from' => $this->request->getGet('due_from'),
            'due_to'   => $this->request->getGet('due_to'),
            'search'        => $this->request->getGet('search'),
            'limit'    => $this->request->getGet('limit'),
            'offset'   => $this->request->getGet('offset'),
        ];
        $model = new TaskModel();
        $rows  = $model->filtered($params);
        $count = $model->countFiltered($params);

        return respondSuccess([
            'items'  => $rows,
            'total'  => $count,
            'limit'  => (int)($params['limit'] ?? 20),
            'offset' => (int)($params['offset'] ?? 0),
        ]);
    }

    public function create()
    {
        $rules = [
            'title'       => 'required|min_length[3]|max_length[255]',
            'description' => 'permit_empty|max_length[5000]',
            'status'      => 'required|in_list[pending,in_progress,completed,cancelled]',
            'priority'    => 'required|in_list[low,medium,high,urgent]',
            'due_date'    => 'required|valid_date[Y-m-d]',
        ];
        if (! $this->validate($rules)) {
            return respondFail('Validation failed', ResponseInterface::HTTP_UNPROCESSABLE_ENTITY, $this->validator->getErrors());
        }

        $input = (array)$this->request->getJSON();

        $u = service('request')->user;

        $id = (new TaskModel())->insert([
            'title'       => trim($input['title']),
            'description' => trim((string)$input['description']),
            'status'      => $input['status'],
            'priority'    => $input['priority'],
            'due_date'    => $input['due_date'],
            'created_by'  => $u['id'],
            'updated_by'  => $u['id'],
        ]);

        ActivityLogger::log($u['id'], 'task_created', $id);
        return respondSuccess(['id' => $id], ResponseInterface::HTTP_CREATED);
    }

    public function show(int $id)
    {
        $task = (new TaskModel())->find($id);
        if (! $task) return respondFail('Task not found', ResponseInterface::HTTP_NOT_FOUND);
        return respondSuccess($task);
    }

    public function update(int $id)
    {
        $rules = [
            'title'       => 'if_exist|min_length[3]|max_length[255]',
            'description' => 'if_exist|max_length[5000]',
            'status'      => 'if_exist|in_list[pending,in_progress,completed,cancelled]',
            'priority'    => 'if_exist|in_list[low,medium,high,urgent]',
            'due_date'    => 'if_exist|valid_date[Y-m-d]',
        ];
        if (! $this->validate($rules)) {
            return respondFail('Validation failed', ResponseInterface::HTTP_UNPROCESSABLE_ENTITY, $this->validator->getErrors());
        }
        $task = (new TaskModel())->find($id);
        if (! $task) return respondFail('Task not found', ResponseInterface::HTTP_NOT_FOUND);

        $input = (array)$this->request->getJSON();

        $u = service('request')->user;
        $data = array_filter([
            'title'       => $input['title'] ?? null,
            'description' => $input['description'] ?? null,
            'status'      => $input['status'] ?? null,
            'priority'    => $input['priority'] ?? null,
            'due_date'    => $input['due_date'] ?? null,
            'updated_by'  => $u['id'],
        ], fn($v) => $v !== null);

        (new TaskModel())->update($id, $data);
        ActivityLogger::log($u['id'], 'task_updated', $id, $data);

        return respondSuccess(['id' => $id]);
    }

    public function delete(int $id)
    {
        $task = (new TaskModel())->find($id);
        if (! $task) return respondFail('Task not found', ResponseInterface::HTTP_NOT_FOUND);

        (new TaskModel())->delete($id);
        ActivityLogger::log(service('request')->user['id'], 'task_deleted', $id);
        return respondSuccess(['id' => $id]);
    }

    public function assign(int $id)
    {
        $task = (new TaskModel())->find($id);
        if (! $task) return respondFail('Task not found', ResponseInterface::HTTP_NOT_FOUND);

        $input = (array)$this->request->getJSON();

        $userIds = $input['user_ids'] ?? [];
        if (! is_array($userIds) || empty($userIds)) {
            return respondFail('user_ids must be a non-empty array', ResponseInterface::HTTP_UNPROCESSABLE_ENTITY);
        }

        $assigneeModel = new TaskAssigneeModel();
        $userModel     = new UserModel();
        $mailer        = new MailerService();
        $assignerName  = service('request')->user['name'];

        // de-dup & sanitize
        $userIds = array_values(array_unique(array_map('intval', $userIds)));

        // insert if not exists
        foreach ($userIds as $uid) {
            if (! $userModel->find($uid)) continue;
            $exists = $assigneeModel->where(['task_id' => $id, 'user_id' => $uid])->first();
            if (! $exists) {
                $assigneeModel->insert(['task_id' => $id, 'user_id' => $uid]);
                // notify assignee
                $to = $userModel->find($uid)['email'] ?? null;
                if ($to) $mailer->notifyTaskAssigned($to, $task['title'], $assignerName);
            }
        }
        ActivityLogger::log(service('request')->user['id'], 'task_assigned', $id, ['user_ids' => $userIds]);
        return respondSuccess(['task_id' => $id, 'assigned_to' => $userIds]);
    }

    public function assignees(int $id)
    {
        $assignees = (new TaskAssigneeModel())
            ->select('users.id, users.name, users.email')
            ->join('users','users.id=task_assignees.user_id','inner')
            ->where('task_id',$id)->findAll();
        return respondSuccess($assignees);
    }
}
