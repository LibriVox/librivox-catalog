<div id="author_pseudonyms_modal" class="modal hide fade" style="width:700px;" tabindex="-1" role="dialog" aria-labelledby="profile_label" aria-hidden="true">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
		<h3 id="profile_label">Author Psuedonyms - <span id="pseudonyms_author_name"></span></h3>
	</div>

	<div class="modal-body">
		<div id="response_message" style="display:none;"></div>
		
		<div id="pseudonyms_list"></div>

		<div>
			<h4>Add new pseudonym</h4>
			<table>
				<tr>
					<td><input id="pseudo_first_name_0" placeholder="First name..." /></td>
					<td><input id="pseudo_last_name_0" placeholder="Last name..."  /></td>
					<td><input type="button" class="submit_pseudonym btn" data-pseudonym_id="0" data-author_id="0" value="Save"></td>
				</tr>
			</table>	
		</div>


	</div>

	<div class="modal-footer">
		<button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
	</div>
</div>