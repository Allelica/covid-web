<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "question_text".
 *
 * @property int $id
 * @property int $question_id
 * @property int $language_id
 * @property string $title
 * @property string $subtitle
 * @property string $info
 *
 * @property Question $question
 * @property Lang $language
 */
class QuestionText extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'question_text';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['question_id', 'language_id', 'text'], 'required'],
            [['question_id', 'language_id'], 'integer'],
            [['title','subtitle','info'], 'string'],
            [['question_id'], 'exist', 'skipOnError' => true, 'targetClass' => Question::className(), 'targetAttribute' => ['question_id' => 'id']],
            [['language_id'], 'exist', 'skipOnError' => true, 'targetClass' => Lang::className(), 'targetAttribute' => ['language_id' => 'id']],
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
            'language_id' => Yii::t('app', 'Language ID'),
            'title' => Yii::t('app', 'Title'),
            'subtitle' => Yii::t('app', 'SubTitle'),
            'info' => Yii::t('app', 'Info (Help)'),
        ];
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
     * Gets query for [[Language]].
     *
     * @return \yii\db\ActiveQuery|LangQuery
     */
    public function getLanguage()
    {
        return $this->hasOne(Lang::className(), ['id' => 'language_id']);
    }

    /**
     * {@inheritdoc}
     * @return QuestionTextQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new QuestionTextQuery(get_called_class());
    }
}
