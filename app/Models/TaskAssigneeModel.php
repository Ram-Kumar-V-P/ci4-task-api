<?php

namespace App\Models;

use CodeIgniter\Model;

class TaskAssigneeModel extends Model
{
    protected $table = 'task_assignees';
    protected $allowedFields = ['task_id','user_id','created_at'];
    public    $returnType = 'array';
    public    $useTimestamps = false;
}
