<?php

namespace console\controllers;

use enqueue\amqp_ext\AmqpContext;
use yii\console\Controller;
use common\models\{
    Request as RequestModel
    , RequestLog as RequestLogModel
};

/**
* Слушатель заданий
*/
class ProcessController extends Controller
{
    /**
    * @const LIMIT - предельное количество новых сообщений для обработки за один цикл
    */
    protected const LIMIT = 10;

    /**
    * Слушатель заданий
    *
    * @param string $queueName
    */
    public function actionListen(string $queueName)
    {
        /** @var AmqpContext $context */
        $context = \Yii::$app->queue->createContext();
        $queue = $context->createQueue($queueName);
        $context->declareQueue($queue);
        $consumer = $context->createConsumer($queue);
        
        do {
            if ($message = $consumer->receive())
                try {
                    $data = json_decode($message->getBody(), true, 0x200, \JSON_THROW_ON_ERROR | \JSON_OBJECT_AS_ARRAY);
                    $this->_processMessage($data);
                    $consumer->acknowledge($message);
                } catch (\Exception $e) {
                    $consumer->reject($message);

                    Yii::error("Queue error: " . $e->getMessage());
                }
        } while (true);
    }

    /**
    * Обработчик задания
    *
    * @param array &$data
    */
    protected function _processMessage(array &$data)
    {
        [$methodName, $args] = empty($data['id'])
            ? ['_processRequests', 'delay']
                : ['_processRequest', 'delay', 'id'];

        return $this->$methodName(... array_map(fn (string $key) => $data[$key], $args));
    }

    /**
    * Обработчик заявок
    *
    * @param int $delay
    */
    protected function _processRequests(int $delay)
    {
        foreach (RequestModel::getNewIds(static::LIMIT) as $id)
            Yii::$app->queueService->sendTask('request', ['id' => $id, 'delay' => $delay,]);
    }

    /**
    * Обработчик заявок
    *
    * @param int $delay
    * @param int $id
    *
    * @return bool
    */
    protected function _processRequest(int $delay, int $id): bool
    {
        return !!
            ($obj = RequestModel::find($id)->one())
                && $obj->isIncomplete()
                && ($logh = $obj->getLog())
                && !sleep($delay)
                && $logh->setStatusRnd();
    }
}
