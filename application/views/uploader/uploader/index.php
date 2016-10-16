<div class="well">


     <div>



          <div class="pull-right">
               <a href="<?= base_url()?>workflow" class="menu_link">Librivox Dashboard</a>
               <a href="https://forum.librivox.org" class="menu_link">Librivox Forum</a>
               <a href="<?= base_url()?>pages/workflow-help" class="menu_link">Help</a>
          </div>
     </div>

     <div class="clearfix"></div>



  <div id="upload-img">

       <div class="control-group">
            <div class="controls center">
            <h2 style="display:inline;margin-right: 300px;">Upload files</h2>
              <?= form_dropdown('lang_select', $languages, $current_lang , 'id="lang_select" style="float:right;margin-top:20px;"'); ?>    
            </div>
       </div>  

      <p><?= lang('proj_launch_uploader_intro')?></p>

      <div><?= lang('proj_launch_uploader_instructions')?>:
          <ul>
              <li><?= lang('proj_launch_uploader_instruct_1')?></li>
              <li><?= lang('proj_launch_uploader_instruct_2')?></li>
              <li><?= lang('proj_launch_uploader_instruct_3')?></li>
              <li><?= lang('proj_launch_uploader_instruct_4')?></li>

          </ul>

      </div>

      <br />

      <div><?= lang('proj_launch_uploader_notes')?>:
          <ul>
              <li><?= lang('proj_launch_uploader_notes_1')?></li>
              <li><?= lang('proj_launch_uploader_notes_2')?></li>
              <li><?= lang('proj_launch_uploader_notes_3')?></li>
              <li><?= lang('proj_launch_uploader_notes_4')?></li>
              <li><?= lang('proj_launch_uploader_notes_5')?></li>
          </ul>
      </div>

      <br />    

      <div><?= lang('proj_launch_uploader_help')?></div>

      <br />  <br />   

      <!-- Upload function on action form -->
      <form id="fileupload" action="<?php echo base_url() . 'upload_file'; ?>" method="POST" enctype="multipart/form-data">

      <!-- The fileupload-buttonbar contains buttons to add/delete files and start/cancel the upload -->
      <div class="row fileupload-buttonbar">

          <div class="span10">

          <!-- The fileinput-button span is used to style the file input field as button -->
              <select name="mc" id="mc">
                  <?php foreach($mcs as $code=>$mc): ?>
                      <option value="<?= $code ?>"><?= $code.' - '. $mc ?></option>
                  <?php endforeach ?>
              </select>

              <span class="btn btn-success fileinput-button">
                  <span><i class="icon-plus icon-white"></i> Add files...</span>
                  <!-- Replace name of this input by userfile-->
                  <input type="file" name="userfile" multiple>
              </span>
              <button type="submit" class="btn btn-primary start">
                  <i class="icon-upload icon-white"></i> Start upload
              </button>
              <button type="reset" class="btn btn-warning cancel">
                  <i class="icon-ban-circle icon-white"></i> Cancel upload
              </button>
          </div>
         
          <div class="span5">

          <!-- The global progress bar -->
          <div class="progress progress-success progress-striped active fade">
              <div class="bar" style="width:0%;"></div>
              <!-- The extended global progress information -->
              <div class="progress-extended">&nbsp;</div>
          </div>
       </div>
    </div>

      <!-- The loading indicator is shown during image processing -->
      <div class="fileupload-loading"></div>
      <br>

          <!-- The table listing the files available for upload/download -->
          <table class="table table-striped" id="upload_table">
              
              <tbody class="files" data-toggle="modal-gallery" data-target="#modal-gallery"></tbody>
          </table>
     </form>
  </div>

</div>


<?= $this->load->view('uploader/uploader/templates', '', true); ?>


