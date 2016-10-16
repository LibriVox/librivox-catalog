<!-- Upload function on action form -->
<form id="fileupload" action="<?php echo base_url() . 'private/validator/upload_file'; ?>" method="POST" enctype="multipart/form-data">

    <input type="hidden" id="project_id" name="project_id" value="<?= $project->id?>">

    <!-- The fileupload-buttonbar contains buttons to add/delete files and start/cancel the upload -->
    <div class="row fileupload-buttonbar">

        <div class="span10">

        <!-- The fileinput-button span is used to style the file input field as button -->

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
            <button type="button" class="btn btn-danger delete">
               <i class="icon-trash icon-white"></i> Delete
            </button>
            <input type="checkbox" class="toggle">
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
        <table class="table table-striped"><tbody class="files" data-toggle="modal-gallery" data-target="#modal-gallery"></tbody></table>
</form>



<?= $this->load->view('uploader/uploader/templates', '', true); ?>