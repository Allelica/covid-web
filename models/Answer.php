<?php

namespace app\models;

use Yii;
use app\models\Question;

/**
 * This is the model class for table "answer".
 *
 * @property int $id
 * @property int $question_id
 * @property int $data_coding_id The id of the answer given (is the data_coding id not the class)
 * @property int $user_id
 * @property String (Y-m-d H:i:s) $date
 * @property String $value
 *
 * @property User $user
 * @property Question $question
 * @property DataCoding $dataCoding
 */
class Answer extends \yii\db\ActiveRecord
{

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'answer';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['question_id', 'data_coding_id', 'user_id'], 'required'],
            [['question_id', 'data_coding_id', 'user_id'], 'integer'],
            [['value'],'string'],
            ['date', 'datetime', 'format' => 'php:Y-m-d'],
            //[['date','question_id','user_id'], 'unique', 'targetAttribute' => ['date', 'question_id','user_id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
            [['question_id'], 'exist', 'skipOnError' => true, 'targetClass' => Question::className(), 'targetAttribute' => ['question_id' => 'id']],
            [['data_coding_id'], 'exist', 'skipOnError' => true, 'targetClass' => DataCoding::className(), 'targetAttribute' => ['data_coding_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'question_id' => Yii::t('app', 'Question ID'),
            'data_coding_id' => Yii::t('app', 'The id of the answer given (is the data_coding id not the class)'),
            'user_id' => Yii::t('app', 'User ID'),
            'date' => Yii::t('app', 'Date'),
        ];
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery|UserQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * Gets query for [[Question]].
     *
     * @return \yii\db\ActiveQuery|QuestionQuery
     */
    public function getQuestion()
    {
        return $this->hasOne(Question::className(), ['id' => 'question_id']);
    }

    /**
     * Gets query for [[DataCoding]].
     *
     * @return \yii\db\ActiveQuery|DataCodingQuery
     */
    public function getDataCoding()
    {
        return $this->hasOne(DataCoding::className(), ['id' => 'data_coding_id']);
    }

    /**
     * {@inheritdoc}
     * @return AnswerQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new AnswerQuery(get_called_class());
    }

}
