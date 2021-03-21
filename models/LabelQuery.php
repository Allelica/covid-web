<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[Label]].
 *
 * @see Label
 */
class LabelQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return Label[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return Label|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
