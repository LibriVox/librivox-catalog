
	<?php $display = ($advanced_search)? 'display:block;' : 'display:none;'; ?>

	<div class="advanced-search-inner" style="<?= $display ?>">
		
		<h2>Advanced Search</h2>

		<h5>Fill in as many fields & options as you like.</h5>	

		<div class="clearfix" style="height:20px;"></div>
		
		<form id="advanced_search_form" method="post" >


			<div class="control-group">
			     <div class="controls center">
			     	<label for="title" ><span class="span2">Title:</span>
					<input type="text" class="span4" id="title" name="title" value="<?= $advanced_search_form['title'] ?>"/>
					</label>
			     </div>
			</div>   

			<div class="control-group">
			     <div class="controls center">
			     	<label for="author" ><span class="span2">Author:</span>
					<input type="text" class="span4" id="author" name="author" value="<?= $advanced_search_form['author'] ?>"/>
					</label>
			     </div>
			</div>   

			<div class="control-group">
			     <div class="controls center">
			     	<label for="reader" ><span class="span2">Reader:</span>
					<input type="text" class="span4" id="reader" name="reader" value="<?= $advanced_search_form['reader'] ?>"/>
					</label>
			     </div>

			     <div class="controls center">
			     	<label for="exact_match" ><span class="span2">Exact match:</span>
					<input type="checkbox" class="span4" id="exact_match" name="exact_match" <?= isset($advanced_search_form['exact_match'])? ' checked' : ''; ?>/>
					</label>
			     </div>

			</div> 


			<div class="control-group">
			     <div class="controls center">
			     	<label for="keywords" ><span class="span2">Keywords:</span>
					<input type="text" class="span4" id="keywords" name="keywords" value="<?= $advanced_search_form['keywords'] ?>"/>
					</label>
			     </div>
			</div> 


			<div class="control-group">
			     <div class="controls center">
			     	<label for="genre_id" ><span class="span2">Category/Genre:</span>
					<?= form_dropdown('genre_id', $genres, $advanced_search_form['genre_id'], 'id="genre_id"');?>	
					</label>
			     </div>
			</div>   

			<div class="control-group">
			     <div class="controls center">
			     	<label for="status" ><span class="span2">Status:</span>
						<?= form_dropdown('status', $statuses, $advanced_search_form['status'], 'id="status"');?>			
					</label>
			     </div>
			</div>   

			<div class="control-group">
			     <div class="controls center">
			     	<label for="project_type" ><span class="span2">Solo/ Group:</span>
						<?= form_dropdown('project_type', $project_type, $advanced_search_form['project_type'], 'id="project_type"');?>				
					</label>
			     </div>
			</div>   
		
			<div class="control-group">
			     <div class="controls center">
			     	<label for="recorded_language" ><span class="span2">Language:</span>
					<?= $recorded_languages ;?>			
				</label>
			     </div>
			</div>   	

			<div class="control-group">
			     <div class="controls center">
			     	<label for="sort_order"><span class="span2">Sort by:</span>
						<?= form_dropdown('sort_order', $sort_order, $advanced_search_form['sort_order'], 'id="sort_order"');?>		
					</label>
			     </div>
			</div>   

	       <div class="control-group">
	         <div class="controls center buttons clearfix">
	             <button id="advanced_search_form_submit" class="btn btn-large btn-primary">
	                 <span class="submit_top_line">Search</span><br />
	              </button>
	         </div>
	       </div>

	       <input type="hidden" name="search_page" id="search_page" value="1">
	       <input type="hidden" name="search_form" id="search_form" value="advanced">


		</form>	
	</div> <!-- end .advanced-search-inner	-->