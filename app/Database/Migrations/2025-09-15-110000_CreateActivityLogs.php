<?php
namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateActivityLogs extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'        => ['type'=>'INT','constraint'=>11,'unsigned'=>true,'auto_increment'=>true],
            'user_id'   => ['type'=>'INT','constraint'=>11,'unsigned'=>true],
            'task_id'   => ['type'=>'INT','constraint'=>11,'unsigned'=>true,'null'=>true],
            'action'    => ['type'=>'VARCHAR','constraint'=>100],
            'meta'      => ['type'=>'TEXT','null'=>true],
            'created_at'=> ['type'=>'DATETIME','null'=>true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['user_id','task_id']);
        $this->forge->createTable('activity_logs', true);
    }

    public function down()
    {
        $this->forge->dropTable('activity_logs', true);
    }
}
