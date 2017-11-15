<?php

namespace common\modules\article\actions;

use Yii;
use yii\web\Response;
use yii\helpers\ArrayHelper;

/**
 * autocomplete mező számára adja vissza a lehetséges cimkéket.
 *
 * @author makszipeter
 */
class GetLabelAction extends \yii\base\Action {

    /**
     * 
     * @return string[]
     */
    public function run() {
        if (Yii::$app->request->isAjax) {
            $data = Yii::$app->getRequest()->getQueryParam('term');

            $avaibleLabelModels = \common\modules\label\models\Label::find()
                    ->select('label_name')
                    ->from('label')
                    ->where(['like', 'label_name', $data])
                    ->distinct()
                    ->asArray()
                    ->all();
            \Yii::$app->response->format = Response::FORMAT_JSON;
            return ArrayHelper::getColumn($avaibleLabelModels, 'label_name');
        } else {
            $this->controller->render('@frontend/views/site/error');
        }
    }

}
