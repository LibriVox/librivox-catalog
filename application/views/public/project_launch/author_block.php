<div class="author_block" id="author_block_<?= $counter ?>" style="padding-top: 20px;">
<button type="button" class="close" style="margin-right:70px;font-size:26px !important;" data-dismiss="alert" title="Remove this author">×</button>

<div class="control-group">
     <div class="controls center">
          <input type="hidden" name="auth_id[<?= $counter ?>]" id="auth_id[<?= $counter ?>]" value="<?= set_value('auth_id', 0)?>"  data-array_index="<?= $counter ?>"/>     	
     	<?= form_label(lang('proj_launch_auth_last_name'),  'auth_last_name', array('class'=>'span2')); ?>
     	<?= form_input(array('name'=> 'auth_last_name[' . $counter . ']', 'value' => '' ,'id' => 'auth_last_name[' . $counter . ']',  'class'=>'autocomplete', 'data-search_field'=>'last_name', 'data-search_area'=>'author', 'data-array_index'=>$counter )); ?>    

          <?= form_label(lang('proj_launch_auth_first_name'),  'auth_first_name', array('style'=>'margin-left:30px;width:140px')); ?>
          <?= form_input(array('name'=> 'auth_first_name[' . $counter . ']', 'value' => '' ,'id' => 'auth_first_name[' . $counter . ']',  'class'=>'', 'data-search_field'=>'first_name' , 'data-search_area'=>'author' , 'data-array_index'=>$counter)); ?>          
          
     </div>
</div>   

<div class="control-group">
     <div class="controls center">
     	<?= form_label(lang('proj_launch_auth_dob'),  'auth_yob', array('class'=>'span2')); ?>
     	<?= form_input(array('name'=> 'auth_yob[' . $counter . ']', 'value' => '' ,'id' => 'auth_yob[' . $counter . ']', 'value'=>set_value('auth_yob'), 'data-array_index'=>$counter)); ?>
     	
     	<?= form_label(lang('proj_launch_auth_dod'),  'auth_yod', array('style'=>'margin-left:30px;width:140px')); ?>               	
     	<?= form_input(array('name'=> 'auth_yod[' . $counter . ']', 'value' => '' ,'id' => 'auth_yod[' . $counter . ']', 'value'=>set_value('auth_yod'), 'data-array_index'=>$counter)); ?>    
     </div>
</div> 

<div class="control-group">
     <div class="controls center" >
          <?= form_label(lang('proj_launch_link_to_auth'),  'link_to_auth', array('class'=>'span3')); ?>
          <?= form_input(array('name'=> 'link_to_auth[' . $counter . ']', 'value' => '' ,'id' => 'link_to_auth[' . $counter . ']', 'class'=>'span7', 'value'=>set_value('link_to_auth'), 'data-array_index'=>$counter)); ?>                   
          <a id="link_to_auth_link[<?= $counter ?>]" data-array_index="<?= $counter ?>" href="#" target="_blank">Link</a>
     </div>
</div>

<hr class="span10">

</div> 