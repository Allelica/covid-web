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
use app\models\Question;

class OperatorController extends CommonController
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
      if(Yii::$app->user->isGuest || Yii::$app->user->identity->role != User::ROLE_OPERATOR) {
         $this->redirect('/site/index');
         return false;
      }
      return parent::beforeAction($action);;
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        return $this->redirect('/operator/user-list');
    }

    /**
     * Show user list
     */
    public function actionUserList() {
      $users = Yii::$app->user->identity->getManagedUsers();
      return $this->render('user-list',['user_list'=>$users]);
    }

    /**
     * Check if the user is in the admin list
     * @param User $operator
     * @param User $user
     * @throws HttpException if not
     */
    private static function _checkAdmin($operator,$user) {
      if($operator->role != User::ROLE_OPERATOR || $operator->collector->id != $user->collector->id) {
        throw new \yii\web\HttpException(403, 'Not Authorized.');
      }
    }
    /**
     * Show Instances
     */
    public function actionInstances($id) {
      $admin_user = Yii::$app->user->identity;
      $user = User::findIdentity($id);
      self::_checkAdmin($admin_user,$user);

      $instances_to_add = $user->getAllInstances(true);
      $instances = $user->getInstances(true);
      return $this->render('instances',['instances'=>$instances,
                                   'user_code'=>$user->joinUserCollector->code,
                                   'user_id'=>$user->id,
                                   'instances_to_add'=>$instances_to_add]);
    }

    /**
     * Get Questions (with answer if available)
     * @param boolen $operator
     * @return array [true if filled false otherwise]
     * @TODO midnight bug: if start to compile a questionary before midnight and
     *       finish after the behaviour is unexcpected
     */
     public function actionQuestions($id,$flow,$date = null,$version = 0) {

       if(is_null($date))
         $date = date("Y-m-d");


      $admin_user = Yii::$app->user->identity;
      $user = User::findIdentity($id);
      self::_checkAdmin($admin_user,$user);

      //no date passed, but last question requested
      if($date=='last') {
        $instances = $user->getInstances(true);
        foreach($instances as $inst) {
          if($inst->flow = $flow)
            $date = $inst->date;
        }
      }
      
      $question = new Question();

      $question->scenario = "questions-get";
      $questions = $question->getQuestions([$flow],$version,$user,$date,true);
      $question_name = Yii::t('ui', 'flow'.$flow)." (".$date.")";
      return $this->render('questions',['questions'=>$questions,
                                        'flow'=>$flow,
                                        'date'=>$date,
                                        'user_id'=>$user->id,
                                        'role'=>'operator',
                                        'question_name'=>$question_name,
                                        'user_code'=>$user->joinUserCollector->code]);
     }

     /**
      * View Questions
      * @param boolen $operator
      * @return array [true if filled false otherwise]
      * @TODO midnight bug: if start to compile a questionary before midnight and
      *       finish after the behaviour is unexcpected
      */
      public function actionQuestionsView($id,$flow,$date,$version = 0) {

       $admin_user = Yii::$app->user->identity;
       $user = User::findIdentity($id);
       self::_checkAdmin($admin_user,$user);

       $question = new Question();

       $question->scenario = "questions-get";
       $questions = $question->getQuestions([$flow],$version,$user,$date,true);

       $question_name = Yii::t('ui', 'flow'.$flow)." (".$date.")"." ".Yii::t('ui', 'View only');
       return $this->render('questionsView',['questions'=>$questions,
                                         'flow'=>$flow,
                                         'date'=>$date,
                                         'user_id'=>$user->id,
                                         'role'=>'operator',
                                         'question_name'=>$question_name,
                                         'user_code'=>$user->joinUserCollector->code]);
      }

     public function actionNewPatient() {
       $id = Yii::$app->request->post("code");
       $name = Yii::$app->request->post("name");
       $collector_code = Yii::$app->user->identity->collector->code;
       if(is_null($id) || !Yii::$app->request->isAjax) {
         throw new \yii\web\HttpException(403, 'Not Authorized.');
       }
       $user = new User();
       $user->scenario = User::SCENARIO_USER;
       $ret = $user->registration('','','',$id,$collector_code,$name);
       if(array_key_exists("error",$ret)) {
         echo $ret['error'];
       }
     }

     public function actionSaveAnswer() {
       $user_id = Yii::$app->request->post("user_id");
       $question_id = Yii::$app->request->post("question_id");
       $value = Yii::$app->request->post("value");
       $option_id = Yii::$app->request->post("option_id");
       $date = Yii::$app->request->post("date");
       if(is_null($user_id) || is_null($date) || is_null($question_id) || is_null($value) || !Yii::$app->request->isAjax) {
         throw new \yii\web\HttpException(403, 'Not Authorized.');
       }
       $user = new User();
       $user = $user->find()->where(['id'=>$user_id])->one();
       self::_checkAdmin(Yii::$app->user->identity,$user);
       $option = ['selected'=>true , 'value'=> $value,'id'=>$option_id];
       Question::insertAnswer($option,$question_id,$user,$date);
     }
}
