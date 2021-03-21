<?php
$this->title = $question_name;
$patient_link = '/operator/instances/'.$user_id;
$this->params['breadcrumbs'][] =  ['label'=>Yii::t('ui', 'Patient list'),'url'=>'/operator/user-list'];
$this->params['breadcrumbs'][] =  ['label'=>Yii::t('ui', 'Patient').": ".$user_code,'url'=>$patient_link];
$this->params['breadcrumbs'][] =  ['label'=>$this->title];
?>

<div class="row question-container">
  <div class="col-12">
    <dl>
    <?php
    foreach($questions['questions'] as $question) {?>
      <dt class="question-title"><?= $question['title'];?></dt>
      <?php
      foreach($question['options'] as $option) {
        if($option['selected']) {
          if($option['type'] == 'choice')
            $answer = $option['title'];
          else
            $answer = $option['value'];
        }
      }?>
	<?php
	 if(!isset($answer))
		$answer = '';
	?>
      <dd><?=$answer;?></dd>
    <?php } ?>
  </dl>
  </div>
</div>
