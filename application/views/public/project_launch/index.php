<form class="form-inline " method="post" action="<?= base_url(); ?>add_project" id="add_project">

<div class="well">

     <div>
          <div class="pull-left">
               <h3><?= lang('proj_launch_intro_header'); ?></h3>
               <div><?= lang('proj_launch_intro_1'); ?></div>
               <div><?= lang('proj_launch_intro_2'); ?></div>
               <div><?= lang('proj_launch_intro_3'); ?></div>
               <div><?= lang('proj_launch_intro_4'); ?></div>
               <br />
               <div><?= lang('proj_launch_intro_5'); ?></div>
          </div>


          <div class="pull-right">
               <a href="<?= base_url()?>workflow" class="menu_link">Librivox Dashboard</a>
               <a href="https://forum.librivox.org" class="menu_link">Librivox Forum</a>
               <a href="<?= base_url()?>pages/workflow-help" class="menu_link">Help</a>
               <br />
               <?= form_dropdown('lang_select', $languages, $current_lang , 'id="lang_select" style="float:right;margin-top:20px;"'); ?>
          </div>
     </div>

     <div class="clearfix"></div>

     <div style='margin-top: 20px;'>
          

               <?= validation_errors('<div class="alert alert-error">', '</div>'); ?>
               <div id="showErrors"></div>

               <fieldset class="fieldset-margin">  
                    <legend><?= lang('proj_launch_project'); ?></legend> 

                    <div class="control-group">
                         <div class="controls center">
                              <?= form_label(lang('proj_launch_title'),  'title', array('class'=>'span2')); ?>   
                              <?= form_input(array('name'=> 'title', 'id' => 'title', 'class'=>'span8', 'value'=>set_value('title'))); ?>    
                         </div>
                    </div>   

                    <div class="control-group">
                         <div class="controls center">
                              <?= form_label(lang('proj_launch_link_to_text_1').' '.lang('proj_launch_link_to_text_2'),  'link_to_text', array('class'=>'span4')); ?>
                              <?= form_input(array('name'=> 'link_to_text', 'id' => 'link_to_text', 'class'=>'span6', 'value'=>set_value('link_to_text'))); ?>                   
                         </div>
                    </div>

                    <br />

                    <div class="control-group">
                         <div class="controls center">
                              <?= form_label(lang('proj_launch_type_of_project'),  'project_type', array('class'=>'span2')); ?>
                              <?= form_dropdown('project_type', $project_types, set_value('project_type'), 'id="project_type"'); ?> 

                              <div id="completion_date_block" style="display:inline;">
                               <?= form_label(lang('proj_launch_expected_completion'),  'expected_completion', array('style'=>'margin-left:30px;width:180px')); ?>
                               <?= form_dropdown('expected_completion_year', $years, set_value('expected_completion_year'), 'id="expected_completion_year" style="width:80px;padding-right:1px;"'); ?> 

                               <?= form_dropdown('expected_completion_month', $months, set_value('expected_completion_month'), 'id="expected_completion_month" class="span2"'); ?> 

                               <?= form_dropdown('expected_completion_day', $days, set_value('expected_completion_day'), 'id="expected_completion_day" class="span1"'); ?>                     
                               </div>

                         </div>
                    </div>   

                    
                    <div class="control-group">
                         <div class="controls center">
                              <?= form_label(lang('proj_launch_recorded_language'),  'recorded_language', array('class'=>'span2')); ?>
                              <?= $recorded_languages ?>

                              <?= form_label(lang('proj_launch_recorded_language_other'),  'recorded_language_other', array('id'=>'recorded_language_other_label','style'=>'display:none;margin-left:30px;width:140px')); ?>
                              <?= form_input(array('name'=> 'recorded_language_other', 'id' => 'recorded_language_other', 'value'=>set_value('recorded_language_other'), 'style'=>'display:none;')); ?>     
                              
                         </div>
                    </div> 

                    <div class="control-group">
                         <div class="controls center">
                              <?= form_label(lang('proj_launch_is_compilation'),  'is_compilation', array('class'=>'span5')); ?>
                              
                              
                              <label>
                                   <?= form_radio(array('name'=> 'is_compilation', 'id' => 'is_compilation', 'value'=>'1', 'style'=>'margin: 0 0 0 10px !important;', 'checked'=>(set_value('is_compilation'))? TRUE: FALSE)); ?>
                                   <span><?= lang('proj_launch_yes'); ?></span>
                              </label>
                              
                              <label>
                                   <?= form_radio(array('name'=> 'is_compilation', 'id' => 'is_compilation', 'value'=>'0', 'style'=>'margin: 0 0 0 10px !important;', 'checked'=>(set_value('is_compilation'))? FALSE: TRUE)); ?>
                                   <span><?= lang('proj_launch_no'); ?></span>
                              </label>                 
                                               
                         </div>
                    </div>   
               </fieldset> 


               <fieldset class="fieldset-margin">  
                    <legend><?= lang('proj_launch_author'); ?> </legend>     
                    
                    <?php $data['counter'] =1;  ?>
                    <?= $this->load->view('public/project_launch/author_block', $data, TRUE); ?>

                    <div id="author_blocks" style="margin-top: 30px;" ></div> 

                   <div class="span10">
                         <div id="add_author" class="btn pull-right" data-counter="1"><?= lang('proj_launch_author_add'); ?></div>  
                    </div>
                      
               </fieldset>     

               <fieldset class="fieldset-margin">  
                    <legend><?= lang('proj_launch_translator'); ?> </legend>     
                    
                    <?php $data['counter'] =1;  ?>
                    <?= $this->load->view('public/project_launch/translator_block', $data, TRUE); ?>

                    <div id="translator_blocks" style="margin-top: 30px;" ></div> 

                   <div class="span10">
                         <div id="add_translator" class="btn pull-right" data-counter="1"><?= lang('proj_launch_translator_add'); ?></div>  
                    </div>                      
               </fieldset>  


               <fieldset class="fieldset-margin">  
                    <legend><?= lang('proj_launch_additional_info_work'); ?></legend>  

                    <div class="control-group">
                         <div class="controls center">
                              <?= form_label(lang('proj_launch_edition_year'),  'edition_year', array('class'=>'span4')); ?>
                              <?= form_input(array('name'=> 'edition_year', 'id' => 'edition_year', 'class'=>'span2', 'value'=>set_value('edition_year'))); ?>                     
                         </div>
                    </div>

                    <div class="control-group">
                              <?= form_label(lang('proj_launch_brief_summary_1').' '.lang('proj_launch_brief_summary_2'),  'brief_summary', array('class'=>'span10')); ?>
                         <div class="controls center">                
                              <?= form_textarea(array('name'=> 'brief_summary', 'value'=>set_value('brief_summary'), 'id' => 'brief_summary', 'rows'=>5, 'cols'=>'100', 'class'=>'span10 20marginleft')); ?>
                         </div>
                    </div> 
                    
                    <div class="control-group">
                         <div class="controls center">
                              <?= form_label(lang('proj_launch_brief_summary_by'),  'brief_summary_by', array('class'=>'span4')); ?>   
                              <?= form_input(array('name'=> 'brief_summary_by', 'id' => 'brief_summary_by', 'class'=>'span6', 'value'=>set_value('brief_summary_by'))); ?>    
                         </div>
                    </div>

                    <div class="control-group">
                         <div class="controls center">
                              <?= form_label(lang('proj_launch_link_to_book'),  'link_to_book', array('class'=>'span4')); ?>
                              <?= form_input(array('name'=> 'link_to_book', 'id' => 'link_to_book', 'class'=>'span6', 'value'=>set_value('link_to_book'))); ?>                   
                         </div>
                    </div>   
                                   

                    <div class="control-group">
                         <div class="controls center">
                              <?= form_label(lang('proj_launch_pub_year'),  'pub_year', array('class'=>'span4')); ?>
                              <?= form_input(array('name'=> 'pub_year', 'id' => 'pub_year', 'class'=>'span6', 'value'=>set_value('pub_year'))); ?>                     
                         </div>
                    </div>

                    <div class="control-group">
                         <div class="controls center"> 
                              <?= form_label(lang('proj_launch_select_genres_1'). ' '. lang('proj_launch_select_genres_2'),  '', array('class'=>'span11')); ?>
                              <div class="dropdown">
                                   <a class="dropdown-toggle" style="float:left;cursor:pointer;margin-left:20px;" data-toggle="dropdown" >Select from menu</a>
                                   <b class="dropdown-toggle caret" style="float:left;margin-left:6px;cursor:pointer;" data-toggle="dropdown"></b>

                                   <ul class="dropdown-menu" role="menu" style="cursor:pointer; margin-bottom: 5px; *width: 180px;" aria-labelledby="dropdownMenu">
                                   <?php foreach ($genres as $key => $genre): ?>

                                        <?php $class = (empty($genre['children']))? '': 'dropdown-submenu' ; ?>

                                        <li class="<?= $class ?> level-1" ><a class="genre_item" data-id="<?= $genre['id'];?>" data-level="1" data-name="<?= $genre['name'];?>"><?= $genre['name'];?></a>
                                             <?php if (!empty($genre['children'])):?>
                                                  <ul class="dropdown-menu">
                                                       <?php foreach ($genre['children'] as $key => $child): ?>

                                                            <?php $class = (empty($child['children']))? '': 'dropdown-submenu' ; ?>

                                                            <li class="<?= $class ?>  level-2"><a class="genre_item" data-id="<?= $child['id'];?>" data-level="2"  data-name="<?= $child['name'];?>"><?= $child['name'];?></a>


                                                            <?php if (!empty($child['children'])):?>
                                                                 <ul class="dropdown-menu">
                                                                      <?php foreach ($child['children'] as $key => $grandchild): ?>

                                                                           <?php $class = (empty($grandchild['children']))? '': 'dropdown-submenu' ; ?>

                                                                           <li class="<?= $class ?>  level-3"><a class="genre_item" data-id="<?= $grandchild['id'];?>" data-level="3"  data-name="<?= $grandchild['name'];?>"><?= $grandchild['name'];?></a></li>
                                                                      <? endforeach; ?>             
                                                                 </ul>
                                                            <?php endif ?>
                                                            </li>

                                                       <? endforeach; ?>             
                                                  </ul>
                                             <?php endif ?>
                                        </li>
                                   <? endforeach; ?>
                                   </ul>
                              </div>                 
                         </div>
                    </div> 

                    <div class="clearfix"></div> 

                    <div class="control-group">
                          <div class="controls center">  
                              <input type="hidden" id="genres" name="genres" value="">
                              <div id="genres_div" class="tagsinput" style="width: 770px;margin-bottom:20px;"></div>
                          </div>
                    </div> 

                    <div class="clearfix"></div>   
                    
                    <div class="control-group">
                         <div class="controls center">                
                              <?= form_label(lang('proj_launch_list_keywords_1'),  'list_keywords', array('class'=>'span11')); ?>
                              <?= form_label(lang('proj_launch_list_keywords_2'),  'list_keywords', array('class'=>'span11')); ?>
                              <?= form_textarea(array('name'=> 'list_keywords', 'id' => 'list_keywords', 'rows'=>5, 'cols'=>'100', 'class'=>'span10 20marginleft', 'value'=>set_value('keywords_tag'))); ?>

                         </div>
                    </div>

                    <div class="clearfix"></div>

               </fieldset>  


               <fieldset class="fieldset-margin">
                    <legend><?= lang('proj_launch_additional_info_project'); ?></legend>
                    <div class="control-group">
                         <div class="controls center">
                              <?= form_label(lang('proj_launch_proof_level'),  'proof_level', array('class'=>'span4')); ?>
                              <?= form_dropdown('proof_level', $proof_level, set_value('proof_level'), 'id="proof_level"'); ?>                     
                         </div>
                    </div>

                    <div class="control-group">
                         <div class="controls center">
                              <?= form_label(lang('proj_launch_num_sections'),  'num_sections', array('class'=>'span4')); ?>
                              <?= form_input(array('name'=> 'num_sections', 'id' => 'num_sections', 'class'=>'span1', 'value'=>set_value('num_sections'))); ?>                   
                         </div>
                    </div>
                    
                    <div class="control-group">
                         <div class="controls center">
                              <?= form_label(lang('proj_launch_has_preface'),  'has_preface', array('class'=>'span6')); ?>
                              
                              
                              <label>
                                   <?= form_radio(array('name'=> 'has_preface', 'id' => 'has_preface', 'value'=>'1', 'style'=>'margin: 0 0 0 10px !important;', 'checked'=>(set_value('has_preface'))? TRUE: FALSE)); ?>
                                   <span><?= lang('proj_launch_yes'); ?></span>
                              </label>
                              
                              <label>
                                   <?= form_radio(array('name'=> 'has_preface', 'id' => 'has_preface', 'value'=>'0', 'style'=>'margin: 0 0 0 10px !important;', 'checked'=>(set_value('has_preface'))? FALSE: TRUE)); ?>
                                   <span><?= lang('proj_launch_no'); ?></span>
                              </label>                 
                                               
                         </div>
                    </div>


               </fieldset>   

               <fieldset class="fieldset-margin">
                    <legend><?= lang('proj_launch_additional_info_soloist'); ?></legend>

                    <div class="control-group">
                         <div class="controls center">
                              <?= form_label('What is your forum username?',  'forum_name', array('class'=>'span10')); ?>
                              <?= form_input(array('name'=> 'forum_name', 'id' => 'forum_name', 'class'=>'span6', 'style'=>'margin-left:20px', 'value'=>set_value('forum_name'))); ?>                    
                         </div>
                    </div>
                    
                    <div class="control-group">
                         <div class="controls center">
                              <?= form_label(lang('proj_launch_soloist_name'),  'soloist_name', array('class'=>'span10')); ?>
                              <?= form_input(array('name'=> 'soloist_name', 'id' => 'soloist_name', 'class'=>'span6', 'style'=>'margin-left:20px', 'value'=>set_value('soloist_name'))); ?>                    
                         </div>
                    </div>
                    
                    <div class="control-group">
                         <div class="controls center">
                              <?= form_label(lang('proj_launch_soloist_link_1'). ' ' .lang('proj_launch_soloist_link_2'),  'soloist_link', array('class'=>'span10')); ?>
                              <?= form_input(array('name'=> 'soloist_link', 'id' => 'soloist_link', 'class'=>'span6', 'style'=>'margin-left:20px', 'value'=>set_value('soloist_link'))); ?>                    
                         </div>
                    </div>   
               </fieldset> 


               <div class="control-group">
                 <div class="controls center" style="margin-left: 600px;">
                     <button class="btn btn-tiny btn-primary" type="reset"><?= lang('proj_launch_clear_form')?></button>
                     <button id="generate_form_submit" class="btn btn-large btn-primary">
                         <span class="submit_top_line"><?= lang('proj_launch_submit')?></span><br />
                         <span class="submit_bottom_line"><?= lang('proj_launch_submit_2')?></span>
                      </button>
                 </div>
               </div>
           
     </div>
</div>

</form>    