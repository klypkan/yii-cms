<?php

use yii\db\Schema;
use yii\db\Migration;

class m181005_190108_comments_init extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%comments}}', [
            'id' => $this->primaryKey(),
            'status' => $this->smallInteger()->notNull(),
            'date' => $this->dateTime()->notNull(),
            'content' => $this->text(),
            'user_id' => $this->integer(),
            'comment_parent_id' => $this->integer(),
            'parent_id' => $this->integer()->notNull()
        ], $tableOptions);
        $this->createIndex(
            'idx-comments-user_id',
            'comments',
            'user_id'
        );
        $this->addForeignKey(
            'fk-comments-user_id',
            'comments',
            'user_id',
            'users',
            'id',
            'CASCADE'
        );
    }

    public function down()
    {
        $this->dropIndex(
            'idx-comments-user_id',
            'comments'
        );
        $this->dropForeignKey(
            'fk-comments-user_id',
            'comments'
        );
        $this->dropTable('{{%comments}}');
    }
}
