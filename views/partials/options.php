<?php
$select_option = [];
$inputs = [];

foreach($options as $option) {
  switch ($option["type"]) {
    case 'choice':
      $select_option[] = ['id'=>$option['id'],
                          'name'=>$option['title']." ".$option['subtitle'],
                          'value'=>$option['value'],
                          'checked'=>$option['selected']];
    break;
    case 'numeric':
    case 'date':
    case 'text':
      $inputs[] = ['id'=>$option['id'],
                   'title'=>$option['title'],
                   'type'=>$option["type"],
                   'value'=>$option["value"],
                   'selected'=>$option['selected']];
    }
}?>
<?php
foreach($inputs as $input) {
  $value = '';
  $class = '';
  if($input['selected']) {
    $value = $input['value'];
    $class = 'checked';
  }
  ?>
  <div class="list-group-item-custom <?=$class?>" style="margin-top:10px;">
    <label for="<?=$input['id'];?>"><?=$input['title'];?></label>
    <input id="<?=$input['id'];?>" class="text"
      data-option-id="<?=$input['id'];?>"
      data-user-id="<?=$user_id;?>"
      data-question-id="<?=$question_id;?>"
      data-role="<?=$role;?>"
      data-date="<?=$date;?>"
      type="<?=$input['type'];?>" value="<?=$value;?>" />
  </div>
  <?php
}?>

<?php
if(count($option) > 0) {
?>
<ul class="list-group list-group-flush">
  <?php
$u = 0;
foreach($select_option as $option) {
  $checked = '';
  $class = 'far';
  if($option['checked']) {
    $checked = 'checked';
    $class = 'fas';
  }
  ?>
  <li class="list-group-item-custom <?=$checked;?>">
    <span class="label-check">
    <input type="radio" name=<?=$question_id;?>
                         data-option-id="<?=$option['id'];?>"
                         data-question-id="<?=$question_id;?>"
                         data-user-id="<?=$user_id;?>"
                         data-role="<?=$role;?>"
                         data-date="<?=$date;?>"
                         value="<?=$option['value'];?>"
                         <?=$checked;?>><?=$option['name'];?>
    </span>
    <span class="checkmark"><i class="<?= $class;?> fa-circle"></i></span>
  </li>
  <?php
}?>

</ul>
<?php
}
