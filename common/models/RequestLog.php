<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "request_log".
 *
 * @property int $id
 * @property int $request_id
 * @property string|null $request
 * @property string|null $response
 * @property string $status
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Request $request0
 */
class RequestLog extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'request_log';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['request', 'response'], 'default', 'value' => null],
            [['status'], 'default', 'value' => 'pending'],
            [['status'], 'in', 'range' => ['pending', 'approved', 'declined', 'failure',], 'strict' => true, 'allowArray' => false,],
            [['request_id'], 'required'],
            [['request_id'], 'default', 'value' => null],
            [['request_id'], 'integer'],
            [['request', 'response'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['request_id'], 'exist', 'skipOnError' => true, 'targetClass' => Request::class, 'targetAttribute' => ['request_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'request_id' => 'Request ID',
            'request' => 'Request',
            'response' => 'Response',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
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
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['updated_at'],
                    ActiveRecord::EVENT_BEFORE_CREATE => ['created_at'],
                ],
                'value' => fn() => date('c'),
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function beforeSave($insert)
    {
        if (!parent::beforeSave($insert)) return false;
        if ($this->isNewRecord) return true;

        return !! static::find($this->request_id)->setStatus($this->status);
    }

    /**
     * Указать произвольный статус
     *
     * @param int $probability - вероятность "успеха"
     *
     * @return bool
     */
    public function setStatusRnd(int $probability = 10): bool
    {
        $this->status = rand(1, $probability) < $probability ? 'declined' : 'approved';

        return !!$this->save();
    }
}
