<fieldset>
  <legend class="toggle_form_btn" data-toggle_div_id="search_projects_form">Search Existing Projects</legend>
  <?php $display = (!empty($usage) && $usage == 'search')? '' : 'display:none'; ?>
  <div id="search_projects_form" class="toggle_div" style="<?= $display ?>" >
      <form class="well form-inline " method="post" action="<?= base_url(); ?>ajax_search_catalog">  

      <div id="response_message" class="alert" style="display:none;"></div>  

        <div class="control-group">
           <div class="controls center">
            <?= form_label('Project Id',  'projectid', array('class'=>'span3')); ?>
            <?= form_input(array('name'=> 'projectid', 'id' => 'projectid', 'value'=>set_value('projectid') )); ?>
          </div>
        </div>

        <div class="control-group">
           <div class="controls center">
            <?= form_label('Title',  'projectname', array('class'=>'span3')); ?>
            <?= form_input(array('name'=> 'projectname', 'id' => 'projectname', 'value'=>set_value('projectname') )); ?>
          </div>
        </div>

        <div class="control-group">
           <div class="controls center">
            <?= form_label('Status',  'status', array('class'=>'span3')); ?>
            <?= form_dropdown('status', $statuses, '', 'id="status"'); ?> 

          </div>
        </div>    

        <div class="control-group">
          <div class="controls center">      
            <div id="search_catalog_form_btn" data-page="<?= $page ?>" style="margin-left: 500px;" class="btn btn-tiny btn-primary">Search</div>  
          </div>
        </div>

      </form>

      <div id="results" class="well" style="display:none"></div>

  </div>

</fieldset>