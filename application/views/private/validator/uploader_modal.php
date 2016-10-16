<div id="uploader_modal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="uploader_label" aria-hidden="true">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
		<h3 id="uploader_label">Upload to Archive.org</h3>
	</div>

	<div class="modal-body">
		<div id="response_message_uploader" style="display:none;"></div>
		
           <form id="uploader_form" method="post">   

            <input type="hidden" id="project_id" name="project_id" value="<?= $project->id?>">

             <div class="control-group">
                 <div class="controls ">
                        <label for="upload_title" ><span class="span2">Title</span>
                        <input type="text" class="no-margin span3" name="upload_title" id="upload_title" value="<?= create_full_title($project)?>" >                        
                        </label>
                 </div>
             </div>
             
             <div class="control-group">
                 <div class="controls ">
                        <label for="upload_name" ><span class="span2">Name</span>
                        <input type="text" class="no-margin span3" name="upload_name" id="upload_name" value="<?= strtolower($project->validator_dir). '_librivox' ?>" >                        
                        </label>
                 </div>
             </div>




           </form> 		



	</div>

	<div class="modal-footer">
		<button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
		<button id="iarchive_uploader_submit" class="btn btn-primary">Upload project</button>
	</div>
</div>