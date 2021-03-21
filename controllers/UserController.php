<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\Question;
use app\models\LoginForm;
use app\models\ContactForm;
use app\models\User;

class UserController extends CommonController
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

    public function beforeAction($action) {

      if(Yii::$app->user->isGuest || Yii::$app->user->identity->role != User::ROLE_USER) {
        $this->redirect('/site/index');
        return false;
      }
      return parent::beforeAction($action);
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        $this->redirect('/user/instances');
        return true;
    }

    /**
     * Show istances
     */
    public function actionInstances() {
      $user = Yii::$app->user->identity;
      $user_code = $user->joinUserCollector->code;
      $user_id = $user->id;
      $instances_to_add = $user->getAllInstances(false);
      $instances = $user->getInstances(false);
      return $this->render('instances',['instances'=>$instances,
                                   'user_code'=>$user_code,
                                   'user_id'=>$user_id,
                                   'instances_to_add'=>$instances_to_add]);
    }

    /**
     * Get Questions (with answer if available)
     * @param boolen $operator
     * @return array [true if filled false otherwise]
     */
     public function actionQuestions($flow,$date = null,$version = 0) {
       
       if(is_null($date))
         $date = date("Y-m-d");

      $user = Yii::$app->user->identity;
      $question = new Question();

      $question->scenario = "questions-get";
      $questions = $question->getQuestions([$flow],$version,$user,$date,false);


      $question_name = Yii::t('ui', 'flow'.$flow)." (".$date.")";

      $question_name = Yii::t('ui', 'flow'.$flow)." (".$date.")";
      return $this->render('questions',['questions'=>$questions,
                                        'flow'=>$flow,
                                        'date'=>$date,
                                        'user_id'=>$user->id,
                                        'role'=>'user',
                                        'question_name'=>$question_name,
                                        'user_code'=>$user->joinUserCollector->code]);
     }

     public function actionSaveAnswer() {

       $user_id = Yii::$app->request->post("user_id"); //not really used, just for check
       $question_id = Yii::$app->request->post("question_id");
       $value = Yii::$app->request->post("value");
       $option_id = Yii::$app->request->post("option_id");
       $date = Yii::$app->request->post("date");

       if(is_null($user_id) || $user_id != Yii::$app->user->identity->id || is_null($date) || is_null($question_id) || is_null($value) || !Yii::$app->request->isAjax) {
         throw new \yii\web\HttpException(403, 'Not Authorized.');
       }
       $user = Yii::$app->user->identity;
       $option = ['selected'=>true , 'value'=> $value,'id'=>$option_id];
       Question::insertAnswer($option,$question_id,$user,$date);
     }
}
