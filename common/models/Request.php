<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "request".
 *
 * @property int $id
 * @property int $user_id
 * @property float $amount
 * @property int $term
 * @property string $created_at
 * @property string $updated_at
 *
 * @property RequestLog[] $requestLogs
 * @property User $user
 */
class Request extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'request';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['status'], 'default', 'value' => 'new'],
            [['status'], 'in', 'range' => ['new', 'pending', 'approved', 'declined', 'failure',], 'strict' => true, 'allowArray' => false,],
            [['user_id', 'amount', 'term'], 'required'],
            [['user_id', 'term'], 'default', 'value' => null],
            [['user_id', 'term'], 'integer'],
            [['amount'], 'number'],
            [['created_at', 'updated_at'], 'safe'],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'amount' => 'Amount',
            'term' => 'Term',
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
     * Список
     *
     * @param int $limit - предельное количество
     *
     * @return array
     */
    public static function getNewIds(int $limit): array
    {
        return static::find()
            ->select('id')
            ->where(['status' => 'new',])
            ->limit($limit)
            ->column();
    }

    /**
     * Новая запись в журнале статусов заявок
     *
     * @return ?RequestLog
     */
    public function getLog(): ?RequestLog
    {
        $obj = new RequestLog();
        $obj->request_id = $this->id;

        return $obj->save() ? $obj : null;
    }

    /**
     * Указать произвольный статус
     *
     * @param int $probability - вероятность "успеха"
     *
     * @return bool
     */
    public function setStatus(string $status): bool
    {
        $this->status = $status;

        return !!$this->save();
    }

    /**
     * Проверка статуса заявки на завершённость
     *
     * @return bool
     */
    public function isIncomplete(): bool
    {
        return ! in_array(static::find($this->id)->one()?->status, ['declined', 'approved', 'failure', 'pending',]);
    }

    /**
     * {@inheritdoc}
     */
    public function beforeSave($insert)
    {
        return !! parent::beforeSave($insert)
            && ! in_array(static::find($this->user_id)->one()?->status, ['approved',]);
    }
}
