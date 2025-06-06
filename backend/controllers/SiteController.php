<?php

namespace backend\controllers;

use common\models\LoginForm;
use Yii;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use common\models\Request as RequestModel;

/**
 * Site controller
 */
class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();

        // Изменяем формат ответа
        $behaviors['contentNegotiator']['formats'] = ['application/json' => Response::FORMAT_JSON,];

        return $behaviors;
    }

    /**
     * Подача новой заявки на займ
     *
     * @return array
     */
    public function actionRequests(): array
    {
        $obj = new RequestModel();
        $obj->load((array)Yii::$app->request->getBodyParams());

        [Yii::$app->response->statusCode, $result,] = $obj->validate() && $obj->save()
            ? [201, ['result' => true, 'id' => $obj->id,],]
                : [400, ['result' => false,],];

        return $result;
    }

    /**
     * Запуск обработки заявок
     *
     * @return array
     */
    public function actionProcessor(): array
    {
        return [
            'result' => !! Yii::$app->queueService->sendTask(
                'requests'
                , ['delay' => Yii::$app->request->get('delay'),]
            ),
        ];
    }
}
