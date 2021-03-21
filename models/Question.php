<?php

namespace app\models;

use Yii;
use app\models\Lang;

/**
 * This is the model class for table "question".
 *
 * @property int $id
 * @property int $version
 * @property int|null $parent
 * @property int $ord
 * @property int $flow
 * @property int $class
 *
 * @property Answer[] $answers
 * @property DataCoding[] $dataCodings
 * @property QuestionText[] $questionTexts
 * @property QuestionTrigger $questionTrigger
 */
class Question extends \yii\db\ActiveRecord
{

    const FLOW_OPERATOR = [2];
    const FLOW_USER = [1,3];
    const FLOW_FOLLOWUP = [3];

    private $_answers = [];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'question';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['version', 'ord', 'flow', 'class'], 'required'],
            [['version', 'parent', 'ord', 'flow', 'class'], 'integer'],
            [['class'], 'exist', 'skipOnError' => true, 'targetClass' => DataCoding::className(), 'targetAttribute' => ['class' => 'class']],
            [['question_trigger'], 'exist', 'skipOnError' => true, 'targetClass' => QuestionTrigger::className(), 'targetAttribute' => ['id' => 'conditional']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'version' => Yii::t('app', 'Version'),
            'parent' => Yii::t('app', 'Parent'),
            'ord' => Yii::t('app', 'Ord'),
            'flow' => Yii::t('app', 'Flow'),
        ];
    }

    /**
     * Gets query for [[Answers]].
     *
     * @return \yii\db\ActiveQuery|AnswerQuery
     */
    public function getAnswers()
    {
        return $this->hasMany(Answer::className(), ['question_id' => 'id']);
    }


    /**
     * Gets query for [[Class]].
     *
     * @return \yii\db\ActiveQuery|DataCodingQuery
     */
    public function getDataCodings()
    {
        return $this->hasMany(DataCoding::className(), ['class' => 'class']);
    }

    /**
     * Gets query for [[QuestionTexts]].
     *
     * @return \yii\db\ActiveQuery|QuestionTextQuery
     */
    public function getQuestionTexts()
    {
        return $this->hasMany(QuestionText::className(), ['question_id' => 'id']);
    }

    /**
     * Gets query for [[QuestionTrigger]].
     * Return the trigger when this is the triggered question
     *
     * @return \yii\db\ActiveQuery|QuestionTrigger
     */
    public function getQuestionTrigger()
    {
        return $this->hasOne(QuestionTrigger::className(), ['triggered' => 'id']);
    }

    /**
     * Set the answers as an array decoded from POST variable
     * @param string JSON
     * @return null
     */
     public function setAnswers($json) {
       $this->_answers = json_decode($json,true);
     }
    /**
     * Transform empty string to null
     * @param string $string
     * @return string|null
     */
    private static function _stringToNull($string) {
      if ($string === '')
        return null;
      return $string;
    }

    /**
     * @todo check if performance can be increased with
     * a punctual query rather than a foreach
     */
     private function _getTexts() {
       if(array_key_exists('language',Yii::$app->params)) {
         $lang = Yii::$app->params['language'];
       } else {
         //It comes from the webapp
         $lang = Yii::$app->language;
       }
       foreach($this->questionTexts as $t) {
         if($t->language_id == $lang)
          return ['title'=>self::_stringToNull($t->title),
                  'subtitle'=>self::_stringToNull($t->subtitle),
                  'info'=>self::_stringToNull($t->info)];
       }
     }

    /**
     * {@inheritdoc}
     * @return QuestionQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new QuestionQuery(get_called_class());
    }

    /**
     * Get all the available flow
     * @param bool $operator
     * @return array
     */
     public static function getAllFlows($operator) {
       if(!$operator) {
         $flow = self::FLOW_USER;
       } else {
         $flow = self::FLOW_OPERATOR;
         $flow = array_merge($flow,self::FLOW_USER);
       }
       return $flow;
     }

    /**
     * Get all the questions of a given flow
     * @param int $flow
     * @param int $user_role
     * @return Question[]
     */
     private static function _getQuestionByFlow($flow,$user_role) {
       if(is_null($flow)) {
         if($user_role == User::ROLE_USER)
          return self::find()
                            ->where(['in','flow',SELF::FLOW_USER])
                            ->orderBy(['parent'=>SORT_ASC,'ord'=>SORT_ASC])->all();

         return self::find()->orderBy(['parent'=>SORT_ASC,'ord'=>SORT_ASC])->all();
       }
       $flow = implode(",",$flow);
       return self::find()->where(['in','flow',$flow])->orderBy(['parent'=>SORT_ASC,'ord'=>SORT_ASC])->all();
     }

     /**
      * Recursively (??) bui the tree of questions
      * @param User $user
      * @return array
      */
      private function wrap($userAnswers,$date) {
        $ret = [];
        $ret['id'] = $this->id;
        $options = [];
        foreach($this->dataCodings as $code) {
          $code->scenario = $this->scenario;
          $options[$code->id] = $code->wrap($userAnswers,$this->id,$date);
        }
        $ret['options'] = $options;
        $texts = $this->_getTexts();
        $ret['title'] = $texts['title'];
        $ret['subtitle'] = $texts['subtitle'];
        $ret['info'] = $texts['info'];
        return $ret;
      }

     /**
      * Build the question data structure
      * @param string $flow
      * @param User $user
      * @param string (Y-m-d) $date
      * @param bool $operator
      * @return array Formatted questionary
      * @TODO remove the assumption of only one level of depth
      *       and making it recursive
      */
      private function buildQuestions($flow,$userAnswers,$date,$operator) {

        if($operator) {
          $user_role = User::ROLE_OPERATOR;
        } else {
          $user_role = User::ROLE_USER;
        }

        $meta_question = self::_getQuestionByFlow($flow,$user_role);
        $questions = [];
        foreach($meta_question as $question) {
          $question->scenario = $this->scenario;
          if(is_null($question->parent)) {
            $questions[$question->id] = $question->wrap($userAnswers,$date);
          } else {
            $conditional_id = $question->questionTrigger->conditional0->id;
            $questions[$question->parent]['options'][$conditional_id]['questions'][$question->id] = $question->wrap($userAnswers,$date);
          }
        }
        return $questions;
      }

     /**
      * Get all the questions versioned
      * @param int $flow
      * @param int $version i
      * @param User $user
      * @param string (Y-m-d) $date
      * @param bool $oeprator if the call is done by operator or patient
      * @TODO versioning
      * @TODO fixing with recursivity
      * @return array
      */
      public function getQuestions($flow, $version,$user,$date,$operator) {

        $version = 0;
        $userAnswers = $user->parseAnswers($date);
        $questions = $this->buildQuestions($flow,$userAnswers,$date,$operator);

        $res = [];
        foreach($questions as $q) {
          $options = [];
          foreach($q['options'] as $o) {
            if(array_key_exists('questions',$o)) {
              $inside_questions = [];
              foreach($o['questions'] as $iq) {
                $inside_options = [];
                foreach($iq['options'] as $io) {
                  $inside_options[] = $io;
                }
                $iq['options'] = $inside_options;
                $inside_questions[] = $iq;
              }
              $o['questions'] = $inside_questions;
            }
            $options[] = $o;
          }
          $q['options'] = $options;
          $res[] = $q;
        }
        return ['questions'=>$res];
      }

      /**
       * Check if other answer was already set in the same date
       * @param int $user_id
       * @param int $question_id
       * @param string $date yyyy-mm-dd
       * @return Answer|null
       */
       private static function checkOldAnswer($user_id,$question_id,$date) {
         $oldanswer = Answer::find()
                         ->where(['user_id'=>$user_id,
                                  'question_id'=>$question_id,
                                  'date'=>$date])->one();
        return $oldanswer;
       }

      /**
       * Insert an answer
       * @param array $option
       * @param int $question_id
       * @param User $user
       * @throws HttpException if an error arise
       */
      public static function insertAnswer($option,$question_id,$user,$date) {

        if($option['selected']) {
          //is the answer

          if(is_null($date)) {
            //no date given
            $date = date("Y-m-d");
          }

          //check if this answer was already set in the given date or today
          $oldanswer = self::checkOldAnswer($user->id,$question_id,$date);
          if(!is_null($oldanswer)) {
               //and the answer is presents
               $oldanswer_obj = $oldanswer;
               $oldanswer->delete();
          }

          $answer = new Answer();
          $answer->question_id = $question_id;
          $answer->user_id = $user->id;
          $answer->data_coding_id = $option['id'];
          $answer->value = (string) $option['value'];
          $answer->date = $date; //today or given date
          $answer->save();
          if($answer->errors != array()) {
            $error = json_encode($answer->errors);
            if(isset($oldanswer_obj))
              $oldanswer_obj->save();
            throw new \yii\web\HttpException(404, 'Error: '.$error);
          }
        }
      }


      /**
       * Side function for the recursive part of parse()
       * @param array $arr The parent array
       * @param int $x Just to remember the correct recursive behaviour
       * @param User $user
       */
      private static function _parseBranch($arr,$x,$user,$date) {
        foreach($arr as $question) {
          foreach($question['options'] as $option) {
            if(array_key_exists('questions',$option)) {
              $x = self::_parseBranch($option['questions'],$x,$user,$date);
            }
            self::insertAnswer($option,$question['id'],$user,$date);
          }
        }
        return $x;
      }

    /**
     * Parse the questions structure to retirve answers
     * @return array
     */
    public function parse($user,$date) {
      $ret = [];$x=0;
      if($this->scenario == 'questions-post') {
        foreach($this->_answers as $answer) {
            $x = self::_parseBranch($answer,$x,$user,$date);
          }
        }
        return $ret;
    }
}
