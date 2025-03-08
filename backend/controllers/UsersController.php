<?php
namespace backend\controllers;

use backend\components\Controller;
use backend\models\UsersSearch;
use common\models\AdminUsers;
use common\models\Reviews;
use common\models\UserAttributes;
use common\models\UserDetails;
use common\models\Users;
use Yii;
use yii\data\ActiveDataProvider;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;

/**
 * AdminUserController implements the CRUD actions for AdminUser model.
 */
class UsersController extends Controller
{
    /**
     * Lists all AdminUser models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new UsersSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
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
        $model = Users::findOne($id);
        $detail = UserDetails::find()->where(['uid' => $id])->one();
        if(!$detail){
            $detail = new UserDetails();
            $detail->uid = $id;
        }

        if(Yii::$app->request->isPost){
            $form_data = Yii::$app->request->post();
            //$password = Yii::$app->request->post('password');
            if ($model->load($form_data)) {
                //if(!empty($password)){
                    //TODO 支持改密码 $model->setPassword($password);
                //}
                $model->save(0);
                $detail->load($form_data);
                $detail->save(0);
                return $this->redirect(['view' , 'id' => $id]);
            }
        }
        return $this->render('update', [
            'model' => $model,
            'detail' => $detail,
            'admins' => array_column( AdminUsers::find()->select(['id','name'])->asArray()->all() , 'name' ,'id')
        ]);
    }

    public function actionView($id)
    {
        $model = Users::findOne($id);
        return $this->render('view', [
            'model' => $model,
            'attributes' => UserAttributes::getUserAttributes($id),
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
        $user = Users::findOne($id);
        $user->status = -1;
        $user->save();
        return $this->renderJSON();
    }
}
