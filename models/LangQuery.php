<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[Lang]].
 *
 * @see Lang
 */
class LangQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return Lang[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return Lang|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
