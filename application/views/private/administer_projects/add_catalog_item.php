<?= $menu_header; ?>

<?//= validation_errors('<div class="alert alert-error">', '</div>'); ?>

<div>

<fieldset>
  <legend class="toggle_form_btn" data-toggle_div_id="add_project_form">Add from Project Launch</legend>
  <?php $display = (!empty($usage) && $usage == 'new')? '' : 'display:none'; ?>
  <div id="add_project_form" class="toggle_div" style="<?= $display ?>" >
  <form class="well form-inline " method="post" action="<?= base_url(); ?>add_catalog_item">    

    <div class="control-group">
         <div class="controls center">
         	<?= form_label('Lookup Project Launch Code',  'project_code', array('class'=>'span3')); ?>
         	<?= form_input(array('name'=> 'project_code', 'id' => 'project_code','value'=>set_value('project_code') )); ?>

         	<?= form_hidden('submit_type', 'code_lookup'); ?>
         	
         	<div id="add_project_form_btn" class="btn btn-tiny btn-primary">Search</div>  
         </div>
    </div>

  </form>
  </div>
</fieldset>


<?= $project_search_form; ?>



<fieldset>
  <legend class="toggle_form_btn" data-toggle_div_id="project_form">Project Form</legend>
  <div id="project_form"  class="toggle_div" style="display:none" >
    <form class="well form-inline " id="add_catalog_item_form" method="post" action="<?= base_url(); ?>add_catalog_item">  

    	<h4 style="margin-bottom: 20px;">All fields are optional (except Title), and not every field will pertain to every stage of the project.</h4>  

      <div id="message" class="alert alert-info" style="display:none;">Project updated</div>
      <div id="showErrors" class="alert alert-error" style="display:none;"></div>

      <fieldset>
        <legend class="toggle_form_btn" data-toggle_div_id="project_details">Project Details</legend>
        <div id="project_details" style="margin-top:10px;">
        <div class="control-group">
            <div class="controls center">
              <?= form_label('Project Id',  'project_id', array('class'=>'span2')); ?>
              <?= form_input(array('name'=> 'project_id', 'id' => 'project_id', 'value'=>$project_id, 'readonly'=>"readonly" )); ?>

              <?php $display = ($project_id)? '' : 'display:none' ; ?>
              <a id="section_compiler_url" style="margin-left:10px; <?= $display ?>" href="section_compiler/<?= $project_id?>">Go to Section Compiler</a>
              <a id="validator_url" style="margin-left:10px; <?= $display ?>" href="validator/<?= $project_id?>">Go to Validator</a>
              <a id="btn_project_readers_modal" style="margin-left:10px;" href="#">Project Readers</a>
            </div>
        </div>

      	<div class="control-group">
           	<div class="controls center">
           		<?= form_label('Title',  'projectname', array('class'=>'span2')); ?>
              <?= form_input(array('name'=> 'title_prefix', 'id' => 'title_prefix', 'value'=>$title_prefix, 'class'=>"span2", 'placeholder'=>'Prefix...' )); ?>
           		<?= form_input(array('name'=> 'projectname', 'id' => 'projectname', 'value'=>$projectname, 'class'=>"span6" )); ?>
    		    </div>
      	</div>

      	<div class="control-group">
           	<div class="controls center">
           		<?= form_label('Description',  'projectdescription', array('class'=>'span2')); ?>
           		<?= form_textarea(array('name'=> 'projectdescription', 'id' => 'projectdescription', 'value'=>$projectdescription, 'rows'=>5, 'cols'=>'100', 'class'=>'span10 20marginleft' )); ?>
    		    </div>
      	</div>

        <div class="control-group">
            <div class="controls center">
              <?= form_label('Copyright Year',  'copyrightyear', array('class'=>'span2')); ?>
              <?= form_input(array('name'=> 'copyrightyear', 'id' => 'copyrightyear', 'value'=>$copyrightyear )); ?>

              <?= form_label('Copyright Check',  'copyrightcheck', array('style'=>'margin-left:30px;width:140px')); ?>
              <?= form_checkbox(array('name'=> 'copyrightcheck', 'id' => 'copyrightcheck', 'value'=>1, 'checked'=>$copyrightcheck)); ?>

            </div>
        </div>    

        <div class="control-group">
             <div class="controls center">
                  <?= form_label(lang('proj_launch_is_compilation'),  'is_compilation', array('class'=>'span5')); ?>    

                  <?= form_checkbox(array('name'=> 'is_compilation', 'id' => 'is_compilation', 'value'=>1, 'checked'=>(set_value('is_compilation'))? TRUE: FALSE)); ?>              
                                 
             </div>
        </div>



      	<div class="control-group">
           	<div class="controls center">
           		<?= form_label('Number Sections',  'nsections', array('class'=>'span2')); ?>
           		<?= form_input(array('name'=> 'nsections', 'id' => 'nsections', 'value'=>$nsections )); ?>

           		<?= form_label('First Section',  'firstsection', array('style'=>'margin-left:30px;width:140px')); ?>
           		<?= form_input(array('name'=> 'firstsection', 'id' => 'firstsection', 'value'=>$firstsection )); ?>
    		    </div>
      	</div>

        <div class="control-group">
            <div class="controls center">
              <?= form_label('Begin Date',  'begindate', array('class'=>'span2')); ?>
              <?= form_input(array('name'=> 'begindate', 'id' => 'begindate', 'value'=>$begindate )); ?>


              <?= form_label('Target Date',  'targetdate', array('style'=>'margin-left:30px;width:140px')); ?>
              <?= form_input(array('name'=> 'targetdate', 'id' => 'targetdate', 'value'=>$targetdate )); ?>
            </div>
        </div> 

        <div class="control-group">
            <div class="controls center">
              <?= form_label('Status',  'status', array('class'=>'span2')); ?>
              <?= form_dropdown('status', $statuses, $status, 'id="status"'); ?> 

              <?= form_label('Catalog Date',  'catalogdate', array('style'=>'margin-left:30px;width:140px')); ?>
              <?= form_input(array('name'=> 'catalogdate', 'id' => 'catalogdate', 'value'=>$catalogdate)); ?>

            </div>
        </div> 

        <div class="control-group">
            <div class="controls center">
                <?= form_label(lang('proj_launch_recorded_language'),  'recorded_language', array('class'=>'span2')); ?>
                <?= $recorded_languages ?>    

                <?= form_label('Project type',  'project_type', array('style'=>'margin-left:30px;width:140px')); ?>
                <?= form_dropdown('project_type', $project_types, '', 'id="project_type"'); ?>                            
            </div>
        </div>

        <div class="control-group">
            <div class="controls center">
                <?= form_label('Total time',  'totaltime', array('class'=>'span2')); ?>
                <?= form_input(array('name'=> 'totaltime', 'id' => 'totaltime', 'value'=>$totaltime, 'placeholder'=>'00:00:00')); ?>  

                <?= form_label('Zip size (include "MB")',  'zip_size', array('style'=>'margin-left:30px;width:140px')); ?>
                <?= form_input(array('name'=> 'zip_size', 'id' => 'zip_size', 'value'=>$zip_size, 'placeholder'=>'0 MB')); ?>                                           
            </div>
        </div>

          <div class="control-group">
               <div class="controls center"> 
                <?= form_label('Genres',  'genre', array('class'=>'span2')); ?> 
                    <div class="dropdown">
                         <a class="dropdown-toggle" style="cursor:pointer;" data-toggle="dropdown" >Select from menu</a>
                         <b class="dropdown-toggle caret" style="cursor:pointer;" data-toggle="dropdown"></b>

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
               <input type="hidden" id="keywords_from_db" value="" ?>        
                <?= form_label('Keywords',  'list_keywords', array('class'=>'span11')); ?>
                <?= form_textarea(array('name'=> 'list_keywords', 'id' => 'list_keywords', 'rows'=>5, 'cols'=>'100', 'class'=>'span10 20marginleft')); ?>
               </div>
          </div>

          <div class="clearfix"></div>

          <div class="control-group">
              <div class="controls center">
                <?= form_label('Notes',  'notes', array('class'=>'span2')); ?>
                <?= form_textarea(array('name'=> 'notes', 'id' => 'notes', 'value'=>$notes, 'rows'=>5, 'cols'=>'100', 'class'=>'span10 20marginleft'  )); ?>
              </div>
          </div> 


        </div>        
      </fieldset>

      <fieldset>
        <legend class="toggle_form_btn" data-toggle_div_id="project_volunteers">Volunteers</legend>
        <div id="project_volunteers" style="margin-top:10px;">

       	<div class="control-group">
           	<div class="controls center">
           		<?= form_label('BC',  'person_bc_id', array('class'=>'span2')); ?>  
              <?= form_dropdown('person_bc_id', $bcs, $person_bc_id, 'id="person_bc_id"'); ?>

           		<?= form_label('Alt BC',  'person_altbc_id', array('style'=>'margin-left:30px;width:140px')); ?>
              <?= form_dropdown('person_altbc_id', $altbcs, $person_altbc_id, 'id="person_altbc_id"'); ?>
    		    </div>
      	</div>

        <div class="control-group">
            <div class="controls center">
              <?= form_label('MC',  'person_mc_id', array('class'=>'span2')); ?>
              <?= form_dropdown('person_mc_id', $mcs, $person_mc_id, 'id="person_mc_id"'); ?>

              <?= form_label('PL',  'person_pl_id', array('style'=>'margin-left:30px;width:140px')); ?>
              <?= form_dropdown('person_pl_id', $pls, $person_pl_id, 'id="person_pl_id"'); ?>

            </div>
        </div> 
        </div>       
      </fieldset>

      <fieldset>
        <legend class="toggle_form_btn" data-toggle_div_id="project_urls">URLs</legend>
        <div id="project_urls" style="margin-top:10px;">
      	<div class="control-group">
           	<div class="controls center">
           		<?= form_label('Librivox Url',  'librivoxurl', array('class'=>'span2')); ?>
           		<?= form_input(array('name'=> 'librivoxurl', 'id' => 'librivoxurl', 'value'=>$librivoxurl, 'class'=>"span8" )); ?>
              <a id="librivoxurl_link" href="<?= $librivoxurl ?>" target="_blank">Link</a>
    		    </div>
      	</div>  

      	<div class="control-group">
           	<div class="controls center">
           		<?= form_label('Forum Url',  'forumurl', array('class'=>'span2')); ?>
           		<?= form_input(array('name'=> 'forumurl', 'id' => 'forumurl', 'value'=>$forumurl, 'class'=>"span8" )); ?>
              <a id="forumurl_link" href="<?= $forumurl ?>" target="_blank">Link</a>
    		    </div>
      	</div>   	  	

      	<div class="control-group">
           	<div class="controls center">
           		<?= form_label('Archive Org Url',  'archiveorgurl', array('class'=>'span2')); ?>
           		<?= form_input(array('name'=> 'archiveorgurl', 'id' => 'archiveorgurl', 'value'=>$archiveorgurl, 'class'=>"span8" )); ?>
              <a id="archiveorgurl_link" href="<?= $archiveorgurl ?>" target="_blank">Link</a>
    		    </div>
      	</div>  

      	<div class="control-group">
           	<div class="controls center">
           		<?= form_label('Etext URL',  'gutenburgurl', array('class'=>'span2')); ?>
           		<?= form_input(array('name'=> 'gutenburgurl', 'id' => 'gutenburgurl', 'value'=>$gutenburgurl, 'class'=>"span8" )); ?>
              <a id="gutenburgurl_link" href="<?= $gutenburgurl ?>" target="_blank">Link</a>
    		    </div>
      	</div>  

      	<div class="control-group">
           	<div class="controls center">
           		<?= form_label('Wiki Book Url',  'wikibookurl', array('class'=>'span2')); ?>
           		<?= form_input(array('name'=> 'wikibookurl', 'id' => 'wikibookurl', 'value'=>$wikibookurl, 'class'=>"span8" )); ?>
              <a id="wikibookurl_link" href="<?= $wikibookurl ?>" target="_blank">Link</a>
    		    </div>
      	</div>    	


      	<div class="control-group">
           	<div class="controls center">
           		<?= form_label('Zip Url',  'zip_url', array('class'=>'span2')); ?>
           		<?= form_input(array('name'=> 'zip_url', 'id' => 'zip_url', 'value'=>$zip_url, 'class'=>"span8" )); ?>
              <a id="zip_url_link" href="<?= $zip_url ?>" target="_blank">Link</a>
    		    </div>
      	</div>  


        <div class="control-group">
            <div class="controls center span10">
              <input type="button" id="btn_project_urls_modal" class="btn pull-right" value="Additional Links" />
            </div>
        </div>  

      </div>
      </fieldset>

      <fieldset>
        <legend class="toggle_form_btn" data-toggle_div_id="project_file_info">File Info</legend>
        <div class="control-group">
            <div class="controls center">
              <?= form_label('Cover art pdf',  'coverart_pdf', array('class'=>'span2')); ?>
              <?= form_input(array('name'=> 'coverart_pdf', 'id' => 'coverart_pdf', 'value'=>$coverart_pdf, 'class'=>"span8" )); ?>              
            </div>
        </div>  

        <div class="control-group">
            <div class="controls center">
              <?= form_label('Cover art jpg',  'coverart_jpg', array('class'=>'span2')); ?>
              <?= form_input(array('name'=> 'coverart_jpg', 'id' => 'coverart_jpg', 'value'=>$coverart_jpg, 'class'=>"span8" )); ?>              
            </div>
        </div> 

        <div class="control-group">
            <div class="controls center">
              <?= form_label('Cover art thumbnail',  'coverart_thumbnail', array('class'=>'span2')); ?>
              <?= form_input(array('name'=> 'coverart_thumbnail', 'id' => 'coverart_thumbnail', 'value'=>$coverart_thumbnail, 'class'=>"span8" )); ?>              
            </div>
        </div>         

      </fieldset>

      <fieldset>
        <legend class="toggle_form_btn" data-toggle_div_id="project_authors">Authors</legend>
        <div id="project_authors" style="margin-top:10px;">

        <div id="author_blocks" style="margin-top: 30px;" ></div> 

        <div class="span10">
            <div id="add_author" class="btn pull-right" data-counter="1">Add another author</div>  
        </div>

      </div>
      </fieldset>

      <fieldset>
        <legend class="toggle_form_btn" data-toggle_div_id="project_translators">Translators</legend>
        <div id="project_translators" style="margin-top:10px;">
        <div id="translator_blocks" style="margin-top: 30px;" ></div> 

        <div class="span10">
            <div id="add_translator" class="btn pull-right" data-counter="1">Add another translator</div>  
        </div>
        </div>
      </fieldset>



      <br />

    	<div class="control-group">
    	    <div class="controls center" style="margin-left: 700px;">
    	        <div id="add_catalog_item_form_submit" class="btn btn-large btn-primary">Submit</div>
    	    </div>
    	</div>

    </form>
  </div>
</fieldset>



</div>

<div style="height:100px;"></div>


<?= $project_url_modal ?>

<?= $project_readers_modal ?>