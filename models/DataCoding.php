<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "data_coding".
 *
 * @property int $id
 * @property int $class The classification group
 * @property int $value
 * @property string $type
 * @property boolean $exclusive
 *
 * @property QuestionTrigger[] $questionTriggers
 * @property Answer $answer
 * @property DataCodingText[] $dataCodingTexts
 * @property Question[] $questions
  */
class DataCoding extends \yii\db\ActiveRecord
{

    const ANSWERS_NO_OPTION = ['text','numeric','date'];
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'data_coding';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['class', 'value',], 'required'],
            [['class', 'value','exclusive'], 'integer'],
            [['type','string']],
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
            'class' => Yii::t('app', 'The classification group'),
            'value' => Yii::t('app', 'Value'),
            'exclusive' => Yii::t('app', 'It is an exclusive field?'),
            'type' => Yii::t('app', 'Type')
        ];
    }

    /**
     * Gets query for [[QuestionTrigger]].
     *
     * @return \yii\db\ActiveQuery|QuestionTrigger
     */
    public function getQuestionTriggers()
    {
        return $this->hasMany(QuestionTrigger::className(), ['id' => 'conditional']);
    }

    /**
     * Gets query for [[Answer]].
     *
     * @return \yii\db\ActiveQuery|AnswerQuery
     */
    public function getAnswer()
    {
        return $this->hasOne(Answer::className(), ['data_coding_id' => 'id']);
    }

    /**
     * Gets query for [[DataCodingText]].
     *
     * @return \yii\db\ActiveQuery|DataCodingTextQuery
     */
    public function getDataCodingTexts()
    {
        return $this->hasMany(DataCodingText::className(), ['data_coding_id' => 'id']);
    }

    /**
     * Gets query for [[Questions0]].
     *
     * @return \yii\db\ActiveQuery|QuestionQuery
     */
    public function getQuestions()
    {
        return $this->hasMany(Question::className(), ['class' => 'class']);
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
     * Get the text in the proper language and format the output
     * @param string $string
     * @return string|null
     */
    private function _getTexts() {
      if(array_key_exists('language',Yii::$app->params)) {
        $lang = Yii::$app->params['language'];
      } else {
        //It comes from the webapp
        $lang = Yii::$app->language;
      }
      foreach($this->dataCodingTexts as $t) {
        if($t->language_id == $lang)
         return ['title'=>self::_stringToNull($t->title),
                 'subtitle'=>self::_stringToNull($t->subtitle),
                 'info'=>self::_stringToNull($t->info)];
      }
    }

    /**
     * Get the selected and value for different answers
     * @param array $userAnsers all the answers
     * @param int $question_id The question analized
     * @param int $code_id The id of the anlized option
     * @return mixed[] ['selected'=>true|false, 'value'=>string|null]
     */
     private function _getSingleAnswer($userAnswers,$question_id) {
       $selected = false;
       $value = null;
       if(array_key_exists($question_id,$userAnswers['data_coding_id']) && $userAnswers['data_coding_id'][$question_id] == $this->id) {
         if(in_array($this->type,self::ANSWERS_NO_OPTION)) {
           if(array_key_exists($question_id,$userAnswers['value'])) {
             $selected = true;
             $value = (string) $userAnswers['value'][$question_id];
           }
         }
       }
       if(!in_array($this->type,self::ANSWERS_NO_OPTION)) {
            if(array_key_exists($question_id,$userAnswers['value']) && $userAnswers['value'][$question_id] == $this->value) {
               $selected = true;
            }
            $value = (string) $this->value;
      }

       return ['selected'=>$selected,'value'=>$value];
     }

    /**
     * Wrap the possible answers to be passed with questions
     * @param array $userAnswers all the user answers
     * @param int $question_id the question analized
     * @param string (Y-m-d) $date
     * @return array the option analized
     */
     public function wrap($userAnswers,$question_id,$date) {
       $res = array();
       $texts = $this->_getTexts();
       $res['title'] = $texts['title'];
       $res['subtitle'] = $texts['subtitle'];
       $res['info'] = $texts['info'];
       $res['exclusive'] = (bool) $this->exclusive;
       if(is_null($date)) {
         $res['selected'] = false;
         $res['value'] = (string) $this->value;
       } else {
         $answer = $this->_getSingleAnswer($userAnswers,$question_id);
         $res['value'] = (string) $answer['value'];
         $res['selected'] = $answer['selected'];
       }
       $res['id'] = $this->id;
       $res['type'] = $this->type;
       return $res;
     }

    /**
     * {@inheritdoc}
     * @return DataCodingQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new DataCodingQuery(get_called_class());
    }
}
