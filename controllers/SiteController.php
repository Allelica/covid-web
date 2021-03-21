<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use app\models\User;

class SiteController extends CommonController
{
    public $enableCsrfValidation = false;

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'denyCallback' => function ($rule, $action) {
                    return $this->redirect('/site/index');
                },
                'rules' => [
                    [
                        'actions' => ['login','index'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'allow' => false,
                        'roles' => ['?'],
                    ],
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }


    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        if(Yii::$app->user->isGuest)
          return $this->render('index');

        if(Yii::$app->user->identity->role == User::ROLE_OPERATOR) {
            $this->redirect('/operator/user-list');
            return false;
        }
        $this->redirect('/user/instances');
        return false;
    }

    public function actionLogin() {

      $hospital_code = Yii::$app->request->post("hospital-code");
      $pws = Yii::$app->request->post("password");
      $type = Yii::$app->request->post("type");
      $sample_code = Yii::$app->request->post("sample-code");
      $user = User::getUserCustom($hospital_code,$type,$sample_code,$pws);
      if(is_null($user) || is_array($user)) {
        $msg = Yii::t('ui', 'Invalid user');
        if(is_array($user))
          $msg = $user['error'];
        return $this->render('index',['error'=>$msg]);
      }

      Yii::$app->user->login($user);

      if($user->role == User::ROLE_OPERATOR)
        return $this->redirect('/operator/user-list');
      return $this->redirect('/user/instances');
    }



    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }


    /**
     * Displays contact page.
     *
     * @return Response|string
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }
        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout()
    {
        return $this->render('about');
    }
}
