<?php
namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\Question;
use app\models\Answer;
use app\models\Label;
use app\models\Lang;
use app\models\User;

class ApiController extends CommonController {

   public $enableCsrfValidation = false;
   private $_token = '';

  /**
   * set all the response as Json
   * remove the csrf verification
   * setting the language
   * get token
   */
   public function beforeAction($action) {

    parent::beforeAction($action);

    if(!in_array($_SERVER['HTTP_USER_AGENT'],Yii::$app->params['AgentWhiteList']))
      throw new \yii\web\HttpException(403, 'Not Authorized.');

    $headers = apache_request_headers();
    foreach($headers as $header=>$value) {
      if($header == 'Authorization') {
        if(strlen(trim($value)) != 256)
          throw new \yii\web\HttpException(403, 'Not Authorized.');
        $this->_token = trim($value);
      }
    }

     if (!parent::beforeAction($action)) {
       return false;
     }



     Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
     return true;
   }

  /**
   * Get/Post question
   * Get no parameter required
   * Post requires Token in GET and question in _POST as json
   * @param array|int|null $flow
   * @param string|null $lang
   * @param string|null $date
   * @param string|null $sample_code
   *
   * @return string
   */
  public function actionQuestion(array $flow = null, $lang = null,$date = null,$sample_code = null)
  {

    $version = 0;
    $question = new Question();
    $user = User::getUserByToken($this->_token);
    $operator = false;

    if($user->role==User::ROLE_OPERATOR) {

      $operator_user = $user;
      if(Yii::$app->request->isPost)
        $sample_code = Yii::$app->request->post('sample_code');
      $user = User::getUserBySampleCode($sample_code,$operator_user->collector->id);
      $operator = true;
      if(is_null($user)) {
        $user = User::createBySampleCode($sample_code,$operator_user->collector->code);
      }
    }

    if(!Yii::$app->request->isPost) {

      $question->scenario = "questions-get";
      return $question->getQuestions($flow,$version,$user,$date,$operator);

    } else {

      $answers = Yii::$app->request->post('answers');
      if(!is_null(Yii::$app->request->post('date'))) {
        $date = Yii::$app->request->post('date');
      }
      $question->scenario = "questions-post";
      $question->setAnswers($answers);
      $ret = $question->parse($user,$date);
      return ['result'=>'OK'];

    }
  }

  /**
   * Get the list of the languages
   */
   public function actionLanguages() {
     return  Lang::getList();
   }

   /**
    * Get the list of labelse for the langauge
    */
    public function actionLabels() {
      return Label::getList();
    }

    /**
     * Register a new user
     * @param POST ['first_name','last_name','code','sample_code']
     */
     public function actionRegistration() {

       $first_name = Yii::$app->request->post('first_name');
       $last_name = Yii::$app->request->post('last_name');
       $code = Yii::$app->request->post('code');
       $sample_code = Yii::$app->request->post('sample_code');
       $collector_code = Yii::$app->request->post('collector_code');
       $user = new User();
       $result = $user->registration($first_name,$last_name,$code,$sample_code, $collector_code);
       if(array_key_exists('error',$result))
           throw new \yii\web\HttpException(400, 'Error occurred:'. json_encode($result['error']));
        return $result;
     }

     /**
      * Login with differentiate accounts
      */
      public function actionLogin() {
        $sample_code = Yii::$app->request->post('sample_code');
        $collector_code = Yii::$app->request->post('collector_code');
        $full_name = Yii::$app->request->post('full_name');
        $password = Yii::$app->request->post('password');
        $user = new User();

        if(is_null($password)) {
          $user->scenario = User::SCENARIO_USER;
          $result = $user->loginOrCreate($sample_code,$collector_code,$full_name);
        } else {
          $user->scenario = User::SCENARIO_OPERATOR;
          $result = $user->loginOrCreate($sample_code,$collector_code,$password);
        }
        if(array_key_exists('error',$result))
            throw new \yii\web\HttpException(400, 'An error occurred:'. json_encode($result['error']));
         return $result;
      }

      /**
       * Add email and phone number after question
       * @param string token
       * @requires phone,email in POST
       */
       public function actionCompleteInfo() {
         $user = User::getUserByToken($this->_token);
         $phone = Yii::$app->request->post('phone');
         $email = Yii::$app->request->post('email');
         $push_token = Yii::$app->request->post('push_token');
         $result =  User::completeInfo($phone,$email,$user,$push_token);
         if(array_key_exists('error',$result))
             throw new \yii\web\HttpException(400, 'An error occurred:'. json_encode($result['error']));
        return ['result'=>'OK'];
       }

       /**
        * Get Instances (set of answers available for user)
        */
       public function actionInstances() {

         $user = User::getUserByToken($this->_token);
         $sample_code = Yii::$app->request->post('sample_code');
         $operator = false;

         if($user->role==User::ROLE_OPERATOR) {

           if(is_null($sample_code))
            throw new \yii\web\HttpException(400, 'No sample code provided.');

           $operator_user = $user;
           $user = User::getUserBySampleCode($sample_code,$operator_user->collector->id);
           $operator = true;

         } else {

           if(strtolower($user->joinUserCollector->code) != strtolower(Yii::$app->request->post('sample_code')))
            throw new \yii\web\HttpException(400, 'An error occurred [sc]');
         }
         if(is_null($user))
          return [];
         $result = $user->getInstances($operator);

         if(array_key_exists('error',$result))
             throw new \yii\web\HttpException(400, 'An error occurred:'. json_encode($result['error']));
         return $result;
       }

       public function actionEcho() {
         $headers = apache_request_headers();
         var_dump($headers);
         var_dump($_SERVER);
       }
}
