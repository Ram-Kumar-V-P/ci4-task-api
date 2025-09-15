<?php

namespace App\Models;

use CodeIgniter\Model;

class TaskModel extends Model
{
    protected $table      = 'tasks';
    protected $primaryKey = 'id';
    protected $allowedFields = ['title','description','status','priority','due_date','created_by','updated_by','created_at','updated_at'];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $returnType    = 'array';

    public function filtered(array $params)
    {
        $db  = $this->builder();
        // Filters
        if (!empty($params['status'])) {
            $db->where('status', $params['status']);
        }
        if (!empty($params['priority'])) {
            $db->where('priority', $params['priority']);
        }
        if (!empty($params['due_from'])) {
            $db->where('due_date >=', $params['due_from']);
        }
        if (!empty($params['due_to'])) {
            $db->where('due_date <=', $params['due_to']);
        }
        // Full-text search (title, description)
        if (!empty($params['search'])) {
            // Uses FULLTEXT index (MySQL/MariaDB)
            $search = $params['search'];
            $db->where("MATCH(title, description) AGAINST(".$this->db->escape($search)." IN NATURAL LANGUAGE MODE)", null, false);
        }
        // Pagination
        $limit  = (int)($params['limit'] ?? 20);
        $offset = (int)($params['offset'] ?? 0);
        $db->orderBy('id','DESC')->limit($limit, $offset);

        return $db->get()->getResultArray();
    }

    public function countFiltered(array $params): int
    {
        $db = $this->builder()->select('COUNT(*) AS c', false);
        if (!empty($params['status'])) {
            $db->where('status', $params['status']);
        }
        if (!empty($params['priority'])) {
            $db->where('priority', $params['priority']);
        }
        if (!empty($params['due_from'])) {
            $db->where('due_date >=', $params['due_from']);
        }
        if (!empty($params['due_to'])) {
            $db->where('due_date <=', $params['due_to']);
        }
        if (!empty($params['search'])) {
            $search = $params['search'];
            $db->where("MATCH(title, description) AGAINST(".$this->db->escape($search)." IN NATURAL LANGUAGE MODE)", null, false);
        }
        return (int)($db->get()->getRow()->c ?? 0);
    }
}
