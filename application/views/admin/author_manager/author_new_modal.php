<div id="author_new_modal" class="modal hide fade" style="width:700px;" tabindex="-1" role="dialog" aria-labelledby="profile_label" aria-hidden="true">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
		<h3 id="profile_label">Author Psuedonyms - <span id="new_author_name"></span></h3>
	</div>

	<div class="modal-body">


		<form id="add_new_author_form" action="#">

		<div id="response_message" style="display:none;"></div>
		

		<div>
			<h4>Add new author</h4>


			<div class="control-group">
			     <div class="controls center">
			     	<label for="first_name" ><span class="span2">First Name:</span>
					<input type="text" class="span4" id="first_name" name="first_name" />
					</label>
			     </div>
			</div>   


			<div class="control-group">
			     <div class="controls center">
			     	<label for="last_name" ><span class="span2">Last Name:</span>
					<input type="text" class="span4" id="last_name" name="last_name" />
					</label>
			     </div>
			</div>  


			<div class="control-group">
			     <div class="controls center">
			     	<label for="dob" ><span class="span2">Year of birth:</span>
					<input type="text" class="span4" id="dob" name="dob" />
					</label>
			     </div>
			</div>  


			<div class="control-group">
			     <div class="controls center">
			     	<label for="dod" ><span class="span2">Year of death:</span>
					<input type="text" class="span4" id="dod" name="dod" />
					</label>
			     </div>
			</div>  


			<div class="control-group">
			     <div class="controls center">
			     	<label for="author_url" ><span class="span2">Author url:</span>
					<input type="text" class="span4" id="author_url" name="author_url" />
					</label>
			     </div>
			</div>  

			<div class="control-group">
			     <div class="controls center">
			     	<label for="blurb" ><span class="span2">Blurb:</span>
					<textarea id="blurb" name="blurb" class="span4" style="margin-left: 0 !important; height: 200px;"></textarea>
					</label>					
			     </div>
			</div>  

		</form>

		</div>


	</div>

	<div class="modal-footer">
		<button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
		<button id="author_new_submit" class="btn btn-primary">Save changes</button>
	</div>
</div>