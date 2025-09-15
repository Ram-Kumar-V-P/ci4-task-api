<?php
namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTasks extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'          => ['type'=>'INT','constraint'=>11,'unsigned'=>true,'auto_increment'=>true],
            'title'       => ['type'=>'VARCHAR','constraint'=>255],
            'description' => ['type'=>'TEXT','null'=>true],
            'status'      => ['type'=>'ENUM','constraint'=>['pending','in_progress','completed','cancelled'],'default'=>'pending'],
            'priority'    => ['type'=>'ENUM','constraint'=>['low','medium','high','urgent'],'default'=>'medium'],
            'due_date'    => ['type'=>'DATE'],
            'created_by'  => ['type'=>'INT','constraint'=>11,'unsigned'=>true],
            'updated_by'  => ['type'=>'INT','constraint'=>11,'unsigned'=>true,'null'=>true],
            'created_at'  => ['type'=>'DATETIME','null'=>true],
            'updated_at'  => ['type'=>'DATETIME','null'=>true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['status','priority','due_date']);
        $this->forge->createTable('tasks', true);

        // Full-text index for search
        $this->db->query('ALTER TABLE tasks ADD FULLTEXT ft_title_desc (title, description)');
    }

    public function down()
    {
        $this->forge->dropTable('tasks', true);
    }
}
