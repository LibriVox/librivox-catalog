
	<?php $display = (isset($advanced_search) and $advanced_search) ? 'display:block;' : 'display:none;'; ?>
	
	<link type="text/css" rel="stylesheet" href="https://librivox.org/css/ui-lightness/jquery-ui-1.8.24.custom.css?v=1670239344" />

	<div class="advanced-search-inner" style="<?= $display ?>">
		
		<h2>Advanced Search</h2>

		<h5>Fill in as many fields &amp; options as you like.</h5>

		<div class="clearfix" style="height:20px;"></div>
		
		<form id="advanced_search_form" method="post" >


			<div class="control-group">
			     <div class="controls center">
			     	<label for="title" ><span class="span2">Title:</span>
					<input type="text" class="span4" id="title" name="title" value="<?= htmlspecialchars(isset($advanced_search_form['title']) ? $advanced_search_form['title'] : '') ?>"/>
					</label>
			     </div>
			</div>   

			<div class="control-group">
			     <div class="controls center">
			     	<label for="author" ><span class="span2">Author:</span>
					<input type="text" class="span4" id="author" name="author" value="<?= htmlspecialchars(isset($advanced_search_form['author']) ? $advanced_search_form['author'] : '') ?>"/>
					</label>
			     </div>
			</div>   

			<div class="control-group">
			     <div class="controls center">
			     	<label for="reader" ><span class="span2">Reader:</span>
					<input type="text" class="span4" id="reader" name="reader" value="<?= htmlspecialchars(isset($advanced_search_form['reader']) ? $advanced_search_form['reader'] : '') ?>"/>
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
				<input type="text" name="keywords" value="" id="keywords" class="autocomplete" data-search_func="autocomplete_keywords" data-search_field="keywords" data-search_area="keywords" data-array_index="0"   />
					</label>
			     </div>
			</div> 


			<div class="control-group">
			     <div class="controls center">
			     	<label for="genre_id" ><span class="span2">Category/Genre:</span>
					<?= form_dropdown('genre_id', isset($genres) ? $genres : '', isset($advanced_search_form['genre_id']) ? $advanced_search_form['genre_id'] : '', 'id="genre_id"');?>	
					</label>
			     </div>
			</div>   

			<div class="control-group">
			     <div class="controls center">
			     	<label for="status" ><span class="span2">Status:</span>
						<?= form_dropdown('status', isset($statuses) ? $statuses : '', isset($advanced_search_form['status']) ? $advanced_search_form['status'] : '', 'id="status"');?>			
					</label>
			     </div>
			</div>   

			<div class="control-group">
			     <div class="controls center">
			     	<label for="project_type" ><span class="span2">Solo/ Group:</span>
						<?= form_dropdown('project_type', isset($project_type) ? $project_type : '', isset($advanced_search_form['project_type']) ? $advanced_search_form['project_type'] : '', 'id="project_type"');?>				
					</label>
			     </div>
			</div>   
		
			<div class="control-group">
			     <div class="controls center">
			     	<label for="recorded_language" ><span class="span2">Language:</span>
					<?= isset($recorded_languages) ? $recorded_languages : '' ;?>			
				</label>
			     </div>
			</div>   	

			<div class="control-group">
			     <div class="controls center">
			     	<label for="sort_order"><span class="span2">Sort by:</span>
						<?= form_dropdown('sort_order', isset($sort_order) ? $sort_order : '', isset($advanced_search_form['sort_order']) ? $advanced_search_form['sort_order'] : '', 'id="sort_order"');?>		
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

		<h4>Still can't find what you're looking for?</h4>
		<h5>Try searching 
			<a href="https://archive.org/details/librivoxaudio">Internet Archive's LibriVox Audiobook Collection</a>
		by title, author, reader, key terms, or terms appearing in book descriptions.</h5>

	       <input type="hidden" name="search_page" id="search_page" value="1">
	       <input type="hidden" name="search_form" id="search_form" value="advanced">


		</form>	
	</div> 
	
	<script type="text/javascript">
	function autocomplete_assign_vars(item) {
		return item.value;
	}

	function autocomplete_assign_elements(search_area, ui, array_index) {
		switch (search_area) {
			case 'keywords':
				document.getElementById("keywords").value = ui.item.label;
				break;
		}
	}
	</script>

	<script type="text/javascript" src="https://librivox.org/js/libs/jquery-1.8.2.js?v=1710057521"></script>
	<script type="text/javascript" src="https://librivox.org/js/libs/jquery.validate.js?v=1710057521"></script>
	<script type="text/javascript" src="https://librivox.org/js/libs/jquery-ui-1.8.24.custom.min.js?v=1710057521"></script>
	<script type="text/javascript" src="https://librivox.org/js/common/autocomplete.js?v=1710057521"></script>
	<script type="text/javascript" src="https://librivox.org/js/common/jquery.tagsinput.min.js?v=1710057521"></script>
	
	<!-- end .advanced-search-inner	-->
