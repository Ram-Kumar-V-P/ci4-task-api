<?php
namespace App\Database\Seeds;

use App\Models\UserModel;
use CodeIgniter\Database\Seeder;

class DevSeeder extends Seeder
{
    public function run()
    {
        $users = [
            ['name'=>'Admin','email'=>'admin@example.com','password_hash'=>password_hash('Admin@123', PASSWORD_DEFAULT)],
        ];
        $um = new UserModel();
        foreach ($users as $u) { $um->insert($u); }
    }
}
