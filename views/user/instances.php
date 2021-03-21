<?php
use app\models\Question;
$this->title = Yii::t("ui","Instances");
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="row">
  <div class="col-lg-1 col-sm-12">&nbsp;</div>
  <div class="col-lg-10 col-sm-12">
<h1><?= $user_code;?></h1>
<ul class="new_questions">
  <?php
  foreach($instances_to_add as $k=>$v) {
    if(!$v || in_array($k,Question::FLOW_FOLLOWUP)) { ?>
      <li class="add-instance">
          <i class="fas fa-plus"></i>
          <a href="/user/questions/<?=$user_id;?>/<?=$k;?>">
            <?= Yii::t('ui', 'Add new ').Yii::t('ui', 'flow'.$k);?>
          </a>
      </li>
<?php } ?>
  <?php
} ?>
</ul>
<?php if(count($instances)==0) { ?>
  <div class="no-data"><?= Yii::t('ui', 'No data presents');?></div>
<?php } else {?>
 <table class="table table-striped">
   <thead>
     <tr>
       <th scope="col">#</th>
       <th scope="col"><?= Yii::t('ui', 'Date');?></th>
       <th scope="col"><?= Yii::t('ui', 'Type');?></th>
       <th scope="col"><?= Yii::t('ui', 'Edit');?></th>
     </tr>
   </thead>
   <tbody>
     <?php
      foreach($instances as $instance) {
      ?>
     <tr>
       <th scope="row"></th>
       <td><?= $instance->date;?></td>
       <td><?= Yii::t('ui', 'flow'.$instance->flow);?></td>
       <td>
         <button class="btn btn-secondary user-edit"
          data-date="<?=$instance->date;?>"
          data-id="<?= $user_id;?>"
          data-type="user"
          data-flow="<?= $instance->flow;?>">
           <?= Yii::t('ui', 'Edit');?>
         </button>
      </td>
     </tr>
   <?php }?>
   </tbody>
 </table>
<?php } ?>
</div>
<div class="col-lg-1 col-sm-12">&nbsp;</div>
</div>
