<?php

use yii\db\Migration;

class m250605_153137_create_table_request extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%request}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'amount' => $this->decimal(10, 2)->notNull(),
            'term' => $this->smallInteger()->unsigned()->notNull(),
            'status' => $this->string()->notNull()->defaultValue('new'),
            'created_at' => $this->timestamp()->notNull()->defaultExpression('now()'),
            'updated_at' => $this->timestamp()->notNull()->defaultExpression('now()'),
        ]);

        $this->createIndex('{{%idx-request-created_at}}', '{{%request}}', 'created_at');
        $this->createIndex('{{%idx-request-updated_at}}', '{{%request}}', 'updated_at');
        $this->addForeignKey('{{%fk-request-user_id}}', '{{%request}}', 'user_id', '{{%user}}', 'id', 'CASCADE', 'RESTRICT');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('{{%idx-request-created_at}}', '{{%request}}');
        $this->dropIndex('{{%idx-request-updated_at_status}}', '{{%request}}');
        $this->dropTable('{{%request}}');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250605_153137_create_table_request cannot be reverted.\n";

        return false;
    }
    */
}
