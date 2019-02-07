<?php

use yii\db\Schema;
use yii\db\Migration;

class m181005_190101_sites_init extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%sites}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull(),
            'url' => $this->string()->notNull(),
            'language' => $this->string()->notNull(),
            'path'=> $this->string()->notNull(),
        ], $tableOptions);
        $this->createIndex(
            'idx-sites-url',
            'sites',
            'url',
            true
        );
    }

    public function down()
    {
        $this->dropIndex(
            'idx-sites-url',
            'sites'
        );
        $this->dropTable('{{%sites}}');
    }
}
