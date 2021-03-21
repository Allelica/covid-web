<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "question_trigger".
 *
 * @property int $parent Parent Question
 * @property int $conditional data_coding that if checked trigger a new question
 * @property int $triggered Question triggered
 *
 * @property Question $parent0
 * @property Question $triggered0
 * @property DataCoding $conditional0
 */
class QuestionTrigger extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'question_trigger';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['parent', 'conditional', 'triggered'], 'required'],
            [['parent', 'conditional', 'triggered'], 'integer'],
            [['parent', 'conditional', 'triggered'], 'unique', 'targetAttribute' => ['parent', 'conditional', 'triggered']],
            [['parent'], 'exist', 'skipOnError' => true, 'targetClass' => Question::className(), 'targetAttribute' => ['parent' => 'id']],
            [['triggered'], 'exist', 'skipOnError' => true, 'targetClass' => Question::className(), 'targetAttribute' => ['triggered' => 'id']],
            [['conditional'], 'exist', 'skipOnError' => true, 'targetClass' => DataCoding::className(), 'targetAttribute' => ['conditional' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'parent' => Yii::t('app', 'Parent Question'),
            'conditional' => Yii::t('app', 'data_coding that if checked trigger a new question'),
            'triggered' => Yii::t('app', 'Question triggered'),
        ];
    }

    /**
     * Gets query for [[Parent0]].
     *
     * @return \yii\db\ActiveQuery|QuestionQuery
     */
    public function getParent0()
    {
        return $this->hasOne(Question::className(), ['id' => 'parent']);
    }

    /**
     * Gets query for [[Triggered0]].
     *
     * @return \yii\db\ActiveQuery|QuestionQuery
     */
    public function getTriggered0()
    {
        return $this->hasOne(Question::className(), ['id' => 'triggered']);
    }

    /**
     * Gets query for [[Conditional0]].
     *
     * @return \yii\db\ActiveQuery|DataCodingQuery
     */
    public function getConditional0()
    {
        return $this->hasOne(DataCoding::className(), ['id' => 'conditional']);
    }

    /**
     * {@inheritdoc}
     * @return QuestionTriggerQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new QuestionTriggerQuery(get_called_class());
    }
}
