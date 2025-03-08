<?php
namespace backend\controllers;

use backend\components\Controller;
use common\models\AdminUsers;
use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

/**
 * AdminUserController implements the CRUD actions for AdminUser model.
 */
class AdminsController extends Controller
{
    /**
     * Lists all AdminUser models.
     * @return mixed
     */
    public function actionIndex()
    {
        return $this->render('index', [
            'dataProvider' => new ActiveDataProvider(['query' => AdminUsers::find()]),
        ]);
    }

    /**
     * Creates a new AdminUser model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new AdminUsers();

        if ($model->load(Yii::$app->request->post())) {
            $model->setPassword($model->password);
            $model->status = 1;
            $model->created_at = time();
            if($model->save()){
                return $this->renderJSON([],'创建');
            }else{
                return $this->renderJSON(['error' => $model->getErrors()],'save failed',-2);
            }
        }
        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing AdminUser model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = AdminUsers::findOne($id);

        if(Yii::$app->request->isPost){
            $form_data = Yii::$app->request->post($model->formName());
            if($form_data['password'] == ''){
                unset($form_data['password']);
            }
            if ($model->load($form_data,'')) {
                if(!empty($form_data['password'])){
                    $model->setPassword($form_data['password']);
                }
                if($model->save()){
                    return $this->renderJSON([],'更新成功');
                }else{
                    return $this->renderJSON(['error' => $model->getErrors()],'save failed',-2);
                }
            }
        }
        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * 删除后台管理员
     * @param $id
     * @return \yii\web\Response
     * @throws NotFoundHttpException
     * @throws \yii\db\StaleObjectException
     */
    public function actionBan($id)
    {
        $user = AdminUsers::findOne($id);
        $user->status = -1;
        $user->save();
        return $this->renderJSON();
    }

    public function actionResetGa($id)
    {
        $user = AdminUsers::findOne($id);
        $user->ga_secret = '';
        $user->save();
        return $this->renderJSON();
    }
}
