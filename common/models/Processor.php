<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "processor".
 *
 * @property int $id
 * @property int $delay
 * @property string $created_at
 * @property string $started_at
 * @property string $finished_at
 */
class Processor extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'processor';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['delay'], 'default', 'value' => 5],
            [['delay'], 'integer'],
            [['created_at', 'started_at', 'finished_at'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'delay' => 'Delay',
            'created_at' => 'Created At',
            'started_at' => 'Started At',
            'finished_at' => 'Finished At',
        ];
    }


    /**
     * {@inheritdoc}
     */
	public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_CREATE => ['created_at'],
                ],
                'value' => fn() => date('c'),
            ],
        ];
    }
}
