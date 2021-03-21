<?php

/* @var $this yii\web\View */

$this->title = 'Patients list';
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="site-index">


    <div class="body-content">
      <div id="toolbar" class="row">
        <div class="col-lg-1 col-sm-12">&nbsp;</div>
        <div class="col-lg-3 col-sm-8">
          <div id="new-patient">
            <i class="fas fa-user-plus"></i>
            <?= Yii::t('ui', 'Add New Patient');?>
          </div>
          <div id="new-patient-code">
            <input type="text" class="add-user" id="new-patient-field" placeholder="<?= Yii::t('ui', 'Patient Code');?>*" />
            <input type="text" class="add-user" id="new-patient-name-field" placeholder="<?= Yii::t('ui', 'Full name');?>" />
            <button id="new-patient-send"><i class="fas fa-plus"></i></button>
          </div>
      </div>
      <!--div class="col-6">&nbsp;</div>
      <div class="col-3">
        <div id="search">
          <input type="text" id="search-field" placeholder="<?= Yii::t('ui', 'Search');?>"/>
        </div>
      </div-->
    </div>

        <div class="row">
            <div class="col-lg-1 col-sm-12">&nbsp;</div>
            <div class="col-lg-10 col-sm-12">
              <h3><?= Yii::t('ui', 'Patient list');?></h3>
              <table class="table table-striped user-list">
                <thead>
                  <tr>
                    <th scope="col" width="25%"><?= Yii::t('ui', 'Sample code');?></th>
                    <th scope="col"  width="25%"><?= Yii::t('ui', 'Full Name');?></th>
                    <th scope="col" colspan="3" style="text-align:center;"><?= Yii::t('ui', 'Questions');?></th>
                    <th scope="col" style="text-align:center;"><?= Yii::t('ui', 'Answers');?></th>
                  </tr>
                  <tr>
                    <th scope="col" colspan="2">&nbsp;</th>
                    <th scope="col"  class="inside left-bound"><?= Yii::t('ui', 'For Patient');?></th>
                    <th scope="col"  class="inside"><?= Yii::t('ui', 'For Health Care Professional');?></th>
                    <th scope="col"  class="inside right-bound"><?= Yii::t('ui', 'Follow up');?></th>
                    <th scope="col">&nbsp;</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                    foreach($user_list as $user) {
                      $instances = $user->getAllInstances(true);
                      $obsr = '<a href="/operator/questions/'.$user->id.'/1"><i class="far fa-clipboard user-list"></i></a>';
                      $obhp = '<a href="/operator/questions/'.$user->id.'/2"><i class="far fa-clipboard user-list"></i></a>';
                      $obfu = '<a href="/operator/questions/'.$user->id.'/3"><i class="far fa-clipboard user-list"></i></a>';
                      if($instances[3]) {
                        //exists only if it compiled the same day
                        $link = $user->id.'/3/'.date("Y-m-d");
                        $obfu = '<a href="/operator/questions/'.$link.'"><i class="fas fa-clipboard-check user-list green"></i></a>';
                      }
                      if($instances[2])
                        $obhp = '<a href="/operator/questions/'.$user->id.'/2/last"><i class="fas fa-clipboard-check user-list green"></i></a>';
                      if($instances[1])
                        $obsr = '<a href="/operator/questions/'.$user->id.'/1/last"><i class="fas fa-clipboard-check user-list green"></i></a>';
                   ?>
                  <tr>
                    <td><?= $user->joinUserCollector->code;?></td>
                    <td width="25%"><?= $user->full_name;?></td>
                    <td class="left-bound" style="text-align:center;"><?=$obsr;?></td>
                    <td style="text-align:center;"><?=$obhp;?></td>
                    <td class="right-bound"  style="text-align:center;"><?=$obfu;?></td>
                    <td style="text-align:center;">
                      <a class="button-custom user-profile" id="<?= $user->id;?>">
                        <?= Yii::t('ui', 'View');?>
                      </a>
                    </td>
                  </tr>
                <?php }?>
                </tbody>
              </table>
            </div>
            <div class="col-lg-1 col-sm-12">&nbsp;</div>
          </div>
</div>
