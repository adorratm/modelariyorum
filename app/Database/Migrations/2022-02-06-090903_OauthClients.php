<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class OauthClients extends Migration
{
    public function up()
    {
        // Create Table
        $this->forge->addField([
            'client_id'          => [
                'type'           => 'VARCHAR',
                'constraint'     => 80,
                'unsigned'       => false,
                'auto_increment' => true,
            ],
            'client_secret'      => [
                'type'           => 'VARCHAR',
                'constraint'     => 80,
                'null'           => true,
            ],
            'redirect_uri'       => [
                'type'           => 'VARCHAR',
                'constraint'     => 2000,
                'null'           => true,
            ],
            'grant_types'        => [
                'type'           => 'VARCHAR',
                'constraint'     => 80,
                'null'           => true,
            ],
            'scope'      => [
                'type'           => 'VARCHAR',
                'constraint'     => 80,
                'null'           => true,
            ],
            'user_id'      => [
                'type'           => 'VARCHAR',
                'constraint'     => 80,
                'null'           => true,
            ],
        ]);
        $attributes = ["ENGINE" => 'InnoDB','CHARACTER SET' => 'utf8mb4','COLLATE' => 'utf8mb4_general_ci'];
        $this->forge->createTable("oauth_clients",true,$attributes);
    }

    public function down()
    {
        // Drop Table
        $this->forge->dropTable("oauth_clients",true);
    }
}
