<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%processor}}`.
 */
class m250606_020332_create_processor_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%processor}}', [
            'id' => $this->primaryKey(),
            'delay' => $this->smallInteger()->unsigned()->notNull()->defaultValue(5),
            'created_at' => $this->timestamp()->notNull()->defaultExpression('now()'),
			'started_at' => $this->timestamp()->notNull()->defaultExpression('now()'),
			'finished_at' => $this->timestamp()->notNull()->defaultExpression('now()'),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%processor}}');
    }
}
