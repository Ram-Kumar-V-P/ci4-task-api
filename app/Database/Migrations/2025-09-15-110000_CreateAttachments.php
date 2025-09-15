<?php
namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAttachments extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'           => ['type'=>'INT','constraint'=>11,'unsigned'=>true,'auto_increment'=>true],
            'task_id'      => ['type'=>'INT','constraint'=>11,'unsigned'=>true],
            'user_id'      => ['type'=>'INT','constraint'=>11,'unsigned'=>true],
            'original_name'=> ['type'=>'VARCHAR','constraint'=>255],
            'stored_name'  => ['type'=>'VARCHAR','constraint'=>255],
            'mime_type'    => ['type'=>'VARCHAR','constraint'=>100],
            'size_bytes'   => ['type'=>'BIGINT','unsigned'=>true],
            'created_at'   => ['type'=>'DATETIME','null'=>true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('task_id');
        $this->forge->createTable('attachments', true);
    }

    public function down()
    {
        $this->forge->dropTable('attachments', true);
    }
}
