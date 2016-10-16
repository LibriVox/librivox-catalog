<div id="author_blurb_modal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="profile_label" aria-hidden="true">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
		<h3 id="profile_label">Author Blurb - <span id="modal_author_name"></span></h3>
	</div>

	<div class="modal-body">
		<div id="response_message" style="display:none;"></div>
		
           <form id="author_blurb_form" method="post">   

            <input type="hidden" id="action" name="action" value="update">
            <input type="hidden" id="author_id" name="author_id">

             <div class="control-group">
                 <div class="controls ">
                        <label for="blurb" ><span class="span2">Author Blurb</span></label><br />
                        <textarea id="blurb" style="width:90%;height:300px;"></textarea>                        
                        
                 </div>
             </div>

           </form> 		


	</div>

	<div class="modal-footer">
		<button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
		<button id="author_blurb_submit" class="btn btn-primary">Save changes</button>
	</div>
</div>