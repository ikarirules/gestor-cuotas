<?php

namespace backend\controllers;

use backend\models\UserSearch;
use common\models\User;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * UserController implements CRUD actions for the User management panel.
 */
class UserController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all users.
     */
    public function actionIndex()
    {
        $searchModel = new UserSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single user.
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new user.
     */
    public function actionCreate()
    {
        $model = new User();
        $model->status = User::STATUS_ACTIVE;
        $password = '';

        if ($this->request->isPost) {
            $model->load($this->request->post());
            $password = (string) (Yii::$app->request->post('User', [])['password'] ?? '');

            $passwordValida = $password !== '' && strlen($password) >= 6;
            if (!$passwordValida) {
                $model->addError('password', 'La contraseña debe tener al menos 6 caracteres.');
            }

            if ($passwordValida && $model->validate()) {
                $model->setPassword($password);
                $model->generateAuthKey();
                if ($model->save(false)) {
                    Yii::$app->session->setFlash('success', 'Usuario creado correctamente.');
                    return $this->redirect(['view', 'id' => $model->id]);
                }
            }
        }

        return $this->render('create', [
            'model' => $model,
            'password' => $password,
        ]);
    }

    /**
     * Updates an existing user.
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $password = '';

        if ($this->request->isPost) {
            $model->load($this->request->post());
            $password = (string) (Yii::$app->request->post('User', [])['password'] ?? '');

            $passwordValida = $password === '' || strlen($password) >= 6;
            if (!$passwordValida) {
                $model->addError('password', 'La contraseña debe tener al menos 6 caracteres.');
            }

            if ($passwordValida && $model->validate()) {
                if ($password !== '') {
                    $model->setPassword($password);
                }
                if ($model->save(false)) {
                    Yii::$app->session->setFlash('success', 'Usuario actualizado correctamente.');
                    return $this->redirect(['view', 'id' => $model->id]);
                }
            }
        }

        return $this->render('update', [
            'model' => $model,
            'password' => $password,
        ]);
    }

    /**
     * Deletes an existing user.
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);

        if ((int) $model->id === (int) Yii::$app->user->id) {
            Yii::$app->session->setFlash('error', 'No podés eliminar tu propio usuario.');
            return $this->redirect(['index']);
        }

        $model->delete();
        Yii::$app->session->setFlash('success', 'Usuario eliminado.');

        return $this->redirect(['index']);
    }

    /**
     * Finds the User model based on its primary key value.
     *
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = User::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('La página solicitada no existe.');
    }
}
