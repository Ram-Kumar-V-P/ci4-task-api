<?php
namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateComments extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'        => ['type'=>'INT','constraint'=>11,'unsigned'=>true,'auto_increment'=>true],
            'task_id'   => ['type'=>'INT','constraint'=>11,'unsigned'=>true],
            'user_id'   => ['type'=>'INT','constraint'=>11,'unsigned'=>true],
            'content'   => ['type'=>'TEXT'],
            'created_at'=> ['type'=>'DATETIME','null'=>true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('task_id');
        $this->forge->createTable('comments', true);
    }

    public function down()
    {
        $this->forge->dropTable('comments', true);
    }
}
