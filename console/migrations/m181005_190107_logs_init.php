<?php

use yii\db\Schema;
use yii\db\Migration;

class m181005_190107_logs_init extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%logs}}', [
            'id' => $this->primaryKey(),
            'event' => $this->smallInteger()->notNull(),
            'created_at' => $this->integer()->notNull(),
            'source' => $this->text(),
            'message' => $this->text(),
            'user_id' => $this->integer()
        ], $tableOptions);
        $this->createIndex(
            'idx-logs-user_id',
            'logs',
            'user_id'
        );
        $this->addForeignKey(
            'fk-logs-user_id',
            'logs',
            'user_id',
            'users',
            'id',
            'CASCADE'
        );
    }

    public function down()
    {
        $this->dropIndex(
            'idx-logs-user_id',
            'logs'
        );
        $this->dropForeignKey(
            'fk-logs-user_id',
            'logs'
        );
        $this->dropTable('{{%logs}}');
    }
}
