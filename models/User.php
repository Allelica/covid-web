<?php

namespace app\models;

use Yii;
use app\models\Role;
use app\models\JoinUserCollector;
use yii\helpers\Security;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "user".
 *
 * @property int $id
 * @property string|null $first_name
 * @property string|null $last_name
 * @property string|null $full_name
 * @property string|null $phone
 * @property string|null $email
 * @property string|null $push_token
 * @property string|null $code unique official ID if available in the country
 * @property string $token
 * @property int $role
 * @property string|null $username
 * @property string|null $password
 *
 * @property Answer[] $answers
 * @property JoinUserCollector $joinUserCollector
 * @property Collector $collector
 * @property Role $role0
 */
class User extends \yii\db\ActiveRecord implements \yii\web\IdentityInterface

{

  const SCENARIO_OPERATOR = 'operator';
  const SCENARIO_USER = 'user';
  const ROLE_OPERATOR = 2;
  const ROLE_USER = 1;


  /**
   * {@inheritdoc}
   */
  public static function findIdentity($id)
  {
    return self::find()->where(['id'=>$id])->one();
  }

  /**
   * {@inheritdoc}
   */
  public static function findIdentityByAccessToken($token, $type = null)
  {
      return self::getUserByToken($token);
  }
  /**
   * {@inheritdoc}
   */
  public function getId()
  {
      return $this->id;
  }

  /**
   * {@inheritdoc}
   */
  public function getAuthKey()
  {
      return $this->token;
  }

  /**
   * {@inheritdoc}
   */
  public function validateAuthKey($authKey)
  {
      return $this->token === $authKey;
  }

  public function scenarios()
    {
        return [
            self::SCENARIO_OPERATOR => ['password'],
            self::SCENARIO_USER => ['full_name', 'sample_code'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['token'], 'required'],
            [['role'], 'integer'],
            [['first_name', 'last_name','full_name','phone','email'], 'string', 'max' => 100],
            [['code', 'username'], 'string', 'max' => 50],
            [['token', 'password','push_token'], 'string', 'max' => 256],
            ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'first_name' => Yii::t('app', 'First Name'),
            'last_name' => Yii::t('app', 'Last Name'),
            'full_name' => Yii::t('app', 'Full Name'),
            'code' => Yii::t('app', 'unique official ID if available in the country'),
            'token' => Yii::t('app', 'Token'),
            'role' => Yii::t('app', 'Role'),
            'username' => Yii::t('app', 'Username'),
            'password' => Yii::t('app', 'Password'),
            'push_token' => Yii::t('app', 'Password'),
        ];
    }

    /**
     * Gets query for [[Answer]].
     *
     * @return \yii\db\ActiveQuery|AnswerQuery
     */
    public function getAnswers()
    {
        return $this->hasMany(Answer::className(), ['user_id' => 'id']);
    }

    /**
     * Gets query for [[JoinUserCollector]].
     *
     * @return \yii\db\ActiveQuery|JoinUserCollectorQuery
     */
    public function getJoinUserCollector()
    {
        return $this->hasOne(JoinUserCollector::className(), ['user_id' => 'id']);
    }

    /**
     * Gets query for [[Collectors]].
     *
     * @return \yii\db\ActiveQuery|CollectorQuery
     */
    public function getCollector()
    {
        return $this->hasOne(Collector::className(), ['id' => 'collector_id'])->viaTable('join_user_collector', ['user_id' => 'id']);
    }

    /**
     * Gets query for [[Role0]].
     *
     * @return \yii\db\ActiveQuery|RoleQuery
     */
    public function getRole0()
    {
        return $this->hasOne(Role::className(), ['id' => 'role']);
    }

    /**
     * {@inheritdoc}
     * @return UserQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new UserQuery(get_called_class());
    }

    /**
     * Customized login for roles
     * @param string $hospital_code
     * @param string $type (operator|user)
     * @param string $user_code
     * @param string $password
     * @return User|array
     */
    public static function getUserCustom($hospital_code,$type,$sample_code,$password)
    {
      $collector_id = self::_getCollectorIdByCode($hospital_code);
      switch ($type) {
        case "operator":
          $user = self::find()
            ->joinWith("joinUserCollector")
            ->where(['role'=>USER::ROLE_OPERATOR,
                     'password'=>$password,
                     'join_user_collector.collector_id'=>$collector_id])
            ->one();
          break;
        case "user":
          $user = self::find()
            ->joinWith("joinUserCollector")
            ->where([
                'role'=>USER::ROLE_USER,
                'join_user_collector.collector_id'=>$collector_id,
                'join_user_collector.code'=>$sample_code])
            ->one();
          break;
        default:
          return ['error'=>'Invalid type'];
      }

      return $user;
    }

    /**
     * Get user by token
     * @param string $token
     * @throws HttpException
     */
     public static function getUserByToken($token) {
       $user = self::find()->where(['token'=>$token])->one();
       if(is_null($user))
         throw new \yii\web\HttpException(400, 'Bad credentials');
       return $user;
     }

    /**
     * Generate a random token (dev)
     */
     private static function _generateToken() {
       $bytes = random_bytes(128);
       return bin2hex($bytes);
     }

    /**
     * Register  a new user from external source
     * @param String
     */
     public function registration($first_name,$last_name,$code,$sample_code,$collector_code,$full_name='') {
       if($this->scenario != self::SCENARIO_USER)
        return ['error'=> 'Try to creating a not valid user'];

       $this->first_name = $first_name;
       $this->last_name = $last_name;
       $this->full_name = $full_name;
       $this->code = $code;
       $this->role = self::ROLE_USER;
       $this->token = self::_generateToken();
       $this->save();

       if($this->errors != array()) {
         return ['error'=> $this->errors];
       }

       $collector = Collector::findByCode($collector_code);

       $joinUserCollector = new JoinUserCollector();
       $joinUserCollector->user_id = $this->id;
       $joinUserCollector->collector_id = $collector->id;
       $joinUserCollector->code=$sample_code;
       $joinUserCollector->save();

       if($joinUserCollector->errors != array()) {
         return ['error'=> json_encode($joinUserCollector->errors)];
       }
       return ['token'=>$this->token];
   }

   /**
    * Get collector id by code with exception
    * @TODO this function should be moved in Controller Model
    */
    private static function _getCollectorIdByCode($collector_code) {
      $collector = new Collector();
      $collector = $collector::findByCode($collector_code);
      if(is_null($collector))
        throw new \yii\web\HttpException(400, 'Hospital not found');
      return $collector->id;
    }


    /**
     * Get all user linked to this
     * @throws HttpExcpetion if user is not Operator
     * @return array
     */
     public function getManagedUsers() {
       if($this->role != User::ROLE_OPERATOR)
        throw new \yii\web\HttpException(400, 'Invalid request');

       $users =  JoinUserCollector::find()
          ->where(['collector_id'=>$this->collector])
          ->orderby(['user_id'=>SORT_DESC])
          ->all();
       $ret = [];
       foreach($users as $user) {
         $uo = User::find()->where(['id'=>$user->user_id])->one();
         if($uo->role == User::ROLE_USER)
          $ret[] = $uo;
       }
       return $ret;
     }
   /**
    * Login user or creat a new one if not exists
    * Login Operator
    * @param string $sample_code
    * @param string $collector_code
    * @param string $name_or_passwords DEPENDS ON THE SCENARIO
    * @throws HttpException
    */
   public function loginOrCreate($sample_code,$collector_code,$name_or_password) {

     $collector_id = self::_getCollectorIdByCode($collector_code);

     if($this->scenario == self::SCENARIO_OPERATOR) {

       $password = $name_or_password;
       $user = self::find()->joinWith('joinUserCollector')
                           ->where(['join_user_collector.collector_id'=>$collector_id,'password'=>$password])
                           ->one();
       if(is_null($user))
         throw new \yii\web\HttpException(400, 'Operator with invalid credentails');

       if($user->role == self::ROLE_OPERATOR)
          return ['token'=>$user->token];

      throw new \yii\web\HttpException(400, 'Bad credentials');

     }

     if($this->scenario == self::SCENARIO_USER) {

       $name = $name_or_password;
       $user = self::getUserBySampleCode($sample_code,$collector_id);

       if(is_null($user)) //user not listed
         return $this->registration('','','',$sample_code,$collector_code,$name);

       //update name if necessary
       if(trim(strtolower($user->full_name)) != trim(strtolower($name))) {
         $user->full_name = $name;
         $user->save(false);
       }

       if($user->role == self::ROLE_USER)
        return ['token'=>$user->token];

       throw new \yii\web\HttpException(400, 'Bad credentials');

     }
  }

     /**
      * Add email and phone after question
      * @param string $phone
      * @param string $email
      * @param User $user
      */
      public static function completeInfo($phone,$email,$user,$push_token=null) {
        if(!is_null($phone))
          $user->phone = $phone;
        if(!is_null($email))
          $user->email = $email;
        if(!is_null($push_token)) {
          $user->push_token = $push_token;
        }
        $user->save(false);
        if($user->errors != array())
          return ['errors'=>json_encode($this->errors)];
        return ['result'=>'OK'];
      }

      /**
       * Get user by sample_code
       * @param string $sample_code
       * @return User|null
       */
      public static function getUserBySampleCode($sample_code,$collector_id) {
        $user = self::find()->joinWith('joinUserCollector')
                            ->where(['join_user_collector.collector_id'=>$collector_id, 'join_user_collector.code'=>$sample_code])
                            ->one();
        return $user;
      }

      /**
       * Create a new user given just the sample code and collector id
       * It is useful when an operator insert a new Sample
       * @param string Sample Code (unique among the same collector id)
       * @param string Collector Code (the unique collector code)
       */
      public static function createBySampleCode($sample_code,$collector_code) {
        $user = new User();
        $user->scenario = self::SCENARIO_USER;
        $user->registration('','','',$sample_code,$collector_code,$full_name='');
        return $user;
      }

      /**
       * Build a data structure to retrive the answers given
       * @return array
       */
      public function parseAnswers($date) {
          $ret = ['value'=>[],'data_coding_id'=>[]];
          foreach($this->answers as $answer) {
            if($answer->date == $date) {
              $ret['value'][$answer->question_id] = $answer->value;
              $ret['data_coding_id'][$answer->question_id] = $answer->data_coding_id;
            }
          }
          return $ret;
      }


      /**
       * Get the available set of answer for user
       * @param User $user
       * @return array
       */
       public function getInstances($operator = false) {

         $flows = Question::getAllFlows($operator);

         $instances = [];
         foreach($this->answers as $answer) {
           if(in_array($answer->question->flow,$flows)) {
             $instance = new \StdClass();
             $instance->date = $answer->date;
             $instance->flow = $answer->question->flow;
             $instances[$answer->date." ".$answer->question->flow] = $instance;
           }
         }
         krsort($instances);
         return array_values($instances);
       }

       /**
        * Check if the user already has answer for a given flow
        * A follow-up questionary is present only if it is of the same day
        * @param int $flow
        * @param array $instances The instance build internally
        * @return boolean
        */
        private static function _hasFlow($flow,$instances) {
          foreach($instances as $instance) {
            if($instance->flow == $flow) {
              if(!in_array($flow,Question::FLOW_FOLLOWUP))
                return true;
              if($instance->date == date("Y-m-d"))
                return true;
            }
          }
          return false;
        }

        /**
         * Get All the instances for the actual day
         * @param boolen $operator
         * @return array [true if filled false otherwise]
         */
         public function getAllInstances($operator) {
           $ret = [];
           $date = date("Y-m-d");
           $instances = $this->getInstances($operator);
           $flows = Question::getAllFlows($operator);
           foreach($flows as $flow) {
               $ret[$flow] = self::_hasFlow($flow,$instances);
           }
           return $ret;
         }



}
