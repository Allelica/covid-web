<?php
$this->title = $question_name;
$patient_link = '/user/instances/'.$user_id;
$this->params['breadcrumbs'][] =  ['label'=>Yii::t('ui', 'Instances'),'url'=>'/user/instances'];
$this->params['breadcrumbs'][] =  ['label'=>$this->title];
?>
<button class="submit-button">Submit</button>
<div class="row">
  <div class="col-lg-1 col-sm-12">&nbsp;</div>
  <div class="col-10">
    <div class="flex-container">
    <?php
    $index = 0;
    foreach($questions['questions'] as $question) {
      $class = '';
      if(($index % 3) == 0) {
        $class = 'no-left-margin';
      }
      if(($index % 3) == 2) {
        $class = 'no-right-margin';
      }
      $index++;

      ?>
      <div class="card question <?=$class;?>">
        <div class="card-header">
          <h3 class="card-title"><?= $question['title'];?>
            <i id="spinner-<?=$question['id'];?>" class="fas fa-spinner fa-spin"></i>
          </h3>
          <h4 class="card-subtitle"><?= $question['subtitle'];?>&nbsp;</h4>
        </div>
          <?php
          echo $this->render('/partials/options',[
            'question_id' => $question['id'],
            'options' => $question['options'],
            'user_id' => $user_id,
            'role'=>$role,
            'date'=> $date]);?>
      </div>
    <?php }  ?>
    </div>
  </div>
  <div class="col-lg-1 col-sm-12">&nbsp;</div>
</div>
