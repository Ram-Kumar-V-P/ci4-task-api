<?php
namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTaskAssignees extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'task_id'    => ['type'=>'INT','constraint'=>11,'unsigned'=>true],
            'user_id'    => ['type'=>'INT','constraint'=>11,'unsigned'=>true],
            'created_at' => ['type'=>'DATETIME','null'=>true, 'default'=>null],
        ]);
        $this->forge->addKey(['task_id','user_id'], true);
        $this->forge->createTable('task_assignees', true);
        $this->db->query('CREATE INDEX idx_task_assignees_task ON task_assignees(task_id)');
        $this->db->query('CREATE INDEX idx_task_assignees_user ON task_assignees(user_id)');
    }

    public function down()
    {
        $this->forge->dropTable('task_assignees', true);
    }
}
