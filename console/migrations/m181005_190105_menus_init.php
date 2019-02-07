<?php

use yii\db\Schema;
use yii\db\Migration;

class m181005_190105_menus_init extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%menus}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull(),
            'site_id' => $this->integer()->notNull(),
        ], $tableOptions);
        $this->createIndex(
            'idx-menus-site_id',
            'menus',
            'site_id'
        );
        $this->addForeignKey(
            'fk-menus-site_id',
            'menus',
            'site_id',
            'sites',
            'id',
            'CASCADE'
        );
        $this->createIndex(
            'idx-menus-name-site_id',
            'menus',
            ['name', 'site_id'],
            true
        );

        $this->createTable('{{%menu_items}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull(),
            'type' => $this->smallInteger()->notNull(),
            'value' => $this->string()->notNull(),
            'menu_item_order' => $this->integer()->notNull(),
            'parent_id' => $this->integer(),
            'menu_id' => $this->integer()->notNull(),
        ], $tableOptions);
        $this->createIndex(
            'idx-menu_items-menu_items_id',
            'menu_items',
            'parent_id'
        );
        $this->addForeignKey(
            'fk-menu_items-menu_items_id',
            'menu_items',
            'parent_id',
            'menu_items',
            'id',
            'CASCADE'
        );
        $this->createIndex(
            'idx-menu_items-menu_id',
            'menu_items',
            'menu_id'
        );
        $this->addForeignKey(
            'fk-menu_items-menu_id',
            'menu_items',
            'menu_id',
            'menus',
            'id',
            'CASCADE'
        );
    }

    public function down()
    {
        $this->dropIndex(
            'idx-menus-site_id',
            'menus'
        );
        $this->dropForeignKey(
            'fk-menus-site_id',
            'menus'
        );
        $this->dropIndex(
            'idx-menus-name-site_id',
            'menus'
        );
        $this->dropTable('{{%menus}}');


        $this->dropIndex(
            'idx-menu_items-menu_items_id',
            'menu_items'
        );
        $this->dropForeignKey(
            'fk-menu_items-menu_items_id',
            'menu_items'
        );
        $this->dropIndex(
            'idx-menu_items-menu_id',
            'menu_items'
        );
        $this->dropForeignKey(
            'fk-menu_items-menu_id',
            'menu_items'
        );
        $this->dropTable('{{%menu_items}}');
    }
}
