<?php
namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateUsers extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'            => ['type'=>'INT','constraint'=>11,'unsigned'=>true,'auto_increment'=>true],
            'name'          => ['type'=>'VARCHAR','constraint'=>120],
            'email'         => ['type'=>'VARCHAR','constraint'=>191,'unique'=>true],
            'password_hash' => ['type'=>'VARCHAR','constraint'=>255],
            'created_at'    => ['type'=>'DATETIME','null'=>true],
            'updated_at'    => ['type'=>'DATETIME','null'=>true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('users', true);
        $this->db->query('CREATE INDEX idx_users_email ON users(email)');
    }

    public function down()
    {
        $this->forge->dropTable('users', true);
    }
}
