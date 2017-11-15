<?php

namespace common\modules\article\actions;

use common\modules\article\models\Article;

/**
 * Description of DeleteAction
 *
 * @author makszipeter
 */
class DeleteAction extends \yii\base\Action {

    public function run($id) {
        $model = $this->controller->findModel($id);

        if (!$model || \Yii::$app->user->getIdentity()->id !== $model->user_id) {
            return $this->controller->render('@frontend/views/site/error');
        }
        //fizikai törlés a törlés dátuma után egy hónappal történik, addig csak státuszváltoztatás van
        if ($model->status == Article::DELETED) {
            $model->status = Article::DRAFT;
        } else {
            $model->status = Article::DELETED;
            $model->deleted_at = time();
        }

        $model->save();
        return $this->controller->redirect(\Yii::$app->request->referrer ?: \Yii::$app->homeUrl);
    }

}
