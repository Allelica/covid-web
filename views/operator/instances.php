<?php
use app\models\Question;

$this->title = Yii::t('ui', 'Patient').": ".$user_code;
$this->params['breadcrumbs'][] =  ['label'=>Yii::t('ui', 'Patient list'),'url'=>'/operator/user-list'];
$this->params['breadcrumbs'][] =  ['label'=>$this->title];


?>
<div class="row">
  <div class="col-lg-1 col-sm-12">&nbsp;</div>
  <div class="col-lg-10">
<h1><?= $user_code;?></h1>


<?php

if(count($instances)==0) { ?>

  <div class="no-data"><?= Yii::t('ui', 'No answers available');?></div>

<?php } else {?>
 <table class="table table-striped">
   <thead>
     <tr>
       <th scope="col"><?= Yii::t('ui', 'Date');?></th>
       <th scope="col"><?= Yii::t('ui', 'Type');?></th>
       <th scope="col"><?= Yii::t('ui', 'Edit');?></th>
       <th scope="col"><?= Yii::t('ui', 'View');?></th>
     </tr>
   </thead>
   <tbody>
     <?php
      foreach($instances as $instance) {
      ?>
     <tr>
       <td><?= $instance->date;?></td>
       <td><?= Yii::t('ui', 'flow'.$instance->flow);?></td>
       <td>
         <a class="user-edit"
          data-date="<?=$instance->date;?>"
          data-id="<?= $user_id;?>"
          data-type="operator"
          data-flow="<?= $instance->flow;?>">
          <i class="fas fa-edit"></i>
        </a>
      </td>
      <td>
        <a class="user-view"
         data-date="<?=$instance->date;?>"
         data-id="<?= $user_id;?>"
         data-type="operator"
         data-flow="<?= $instance->flow;?>">
         <i class="far fa-eye"></i>
       </a>
      </td>
     </tr>
   <?php }?>
   </tbody>
 </table>
<?php } ?>
</div>
<div class="col-lg-1 col-sm-12">&nbsp;</div>
</div>
