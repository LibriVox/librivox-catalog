<div class="translator_block" id="translator_block_<?= $counter ?>" style="padding-top: 20px;">
<button type="button" class="close" style="margin-right:70px;font-size:26px !important;" data-dismiss="alert" title="Remove this translator">×</button>

<div class="control-group">
     <div class="controls center">
          <input type="hidden" name="trans_id[<?= $counter ?>]" id="trans_id[<?= $counter ?>]" value="<?= set_value('trans_id', 0)?>"  data-array_index="<?= $counter ?>"/>     	
     	<?= form_label(lang('proj_launch_trans_last_name'),  'trans_last_name', array('class'=>'span2')); ?>
     	<?= form_input(array('name'=> 'trans_last_name[' . $counter . ']', 'value' => '' ,'id' => 'trans_last_name[' . $counter . ']',  'class'=>'autocomplete', 'data-search_field'=>'last_name', 'data-search_area'=>'translator', 'data-array_index'=>$counter )); ?>    

          <?= form_label(lang('proj_launch_auth_first_name'),  'trans_first_name', array('style'=>'margin-left:30px;width:140px')); ?>
          <?= form_input(array('name'=> 'trans_first_name[' . $counter . ']', 'value' => '' ,'id' => 'trans_first_name[' . $counter . ']',  'class'=>'', 'data-search_field'=>'first_name' , 'data-search_area'=>'translator' , 'data-array_index'=>$counter)); ?>                    
     </div>
</div>   

<div class="control-group">
     <div class="controls center">
     	<?= form_label(lang('proj_launch_auth_dob'),  'trans_yob', array('class'=>'span2')); ?>
     	<?= form_input(array('name'=> 'trans_yob[' . $counter . ']', 'value' => '' ,'id' => 'trans_yob[' . $counter . ']', 'value'=>set_value('trans_yob'), 'data-array_index'=>$counter)); ?>
     	
     	<?= form_label(lang('proj_launch_auth_dod'),  'trans_yod', array('style'=>'margin-left:30px;width:140px')); ?>               	
     	<?= form_input(array('name'=> 'trans_yod[' . $counter . ']', 'value' => '' ,'id' => 'trans_yod[' . $counter . ']', 'value'=>set_value('trans_yod'), 'data-array_index'=>$counter)); ?>    
     </div>
</div> 

<div class="control-group">
     <div class="controls center" >
          <?= form_label(lang('proj_launch_link_to_trans'),  'link_to_trans', array('class'=>'span3')); ?>
          <?= form_input(array('name'=> 'link_to_trans[' . $counter . ']', 'value' => '' ,'id' => 'link_to_trans[' . $counter . ']', 'class'=>'span7', 'value'=>set_value('link_to_trans'), 'data-array_index'=>$counter)); ?>                   
          <a id="link_to_trans_link[<?= $counter ?>]" data-array_index="<?= $counter ?>" href="#" target="_blank">Link</a>

     </div>
</div>

<hr class="span10">

</div> 