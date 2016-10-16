Genres:


<?php //var_dump($genres)?>


<link href="<?= base_url()?>css/bootstrap.css" rel="stylesheet">

<script src="<?= base_url()?>js/libs/jquery-1.8.2.min.js"></script>
<script src="<?= base_url()?>js/libs/bootstrap.js"></script>

<script type="text/javascript">
	$('.dropdown-toggle').dropdown();

	$('.genre_item').live('click', function(){
		var tag;

		var this_level = $(this).attr('data-level');

		var level1_id = $(this).closest('.level-1').children('a:first').attr('data-id');
		var level1_name = $(this).closest('.level-1').children('a:first').attr('data-name');

		tag = build_tag(level1_name);
		$('#genres_div').html($('#genres_div').html() + tag);

		id = build_id_list(level1_id);
		$('#genres').val(id);

		if (this_level > 1)
		{
			var level2_id = $(this).closest('.level-2').children('a:first').attr('data-id');
			var level2_name = $(this).closest('.level-2').children('a:first').attr('data-name');

			tag = build_tag(level1_name+'/'+level2_name);
			$('#genres_div').html($('#genres_div').html() + tag);

			id = build_id_list(level2_id);
			$('#genres').val(id);		
		}
		
		if (this_level > 2)
		{
			var level3_id = $(this).closest('.level-3').children('a:first').attr('data-id');
			var level3_name = $(this).closest('.level-3').children('a:first').attr('data-name');

			tag = build_tag(level1_name+'/'+level2_name+'/'+level3_name);
			$('#genres_div').html($('#genres_div').html() + tag);	

			id = build_id_list(level3_id);
			$('#genres').val(id);		
		}
	});

	function build_tag(name)
	{
		return '<span class="tag"><span>'+name+'&nbsp;&nbsp;</span><a href="#" title="Removing tag">x</a></span>';
	}

	function build_id_list(id)
	{
		return $('#genres').val() + ',' + id;
		/*
		var genres = $('#genres').val().split(',');
		alert(genres.length);
		genres.push(id);
		//genres = genres.join();
		alert(genres.length);
		

		var genres = $('#genres').val().split(',');
		genres.push(parseInt(id));
		//var genres = [parseInt(id)]; //["Saab","Volvo","BMW"];
	
		//alert( genres.join());
		//var a = [1,5,1,6,4,5,2,5,4,3,1,2,6,6,3,3,2,4];

		// note: jQuery's filter params are opposite of javascript's native implementation :(
		var unique = $.makeArray($(genres).filter(function(i,itm){ 
		    // note: 'index', not 'indexOf'
		    itm = parseInt(itm);
		    return i == $(genres).index(itm);
		}));

		alert(unique);
		return unique;

		//alert(unique);
		/*
		var genre_ids = genres.split(',');		
		genre_ids.push(id);
		genres = genre_ids.join();
		alert(genres);
		return genres;
		*/
	}
</script>

<div class="control-group">
	<div class="dropdown controls center">
		<input type="hidden" id="genres" value="">
		<div id="genres_div" style="width:400px;height:200px;"></div>
	</div>
</div>   

<div class="dropdown">
	<a class="dropdown-toggle" data-toggle="dropdown" data-target="#">Dropdown trigger</a>
	<b class="dropdown-toggle caret" data-toggle="dropdown"></b>

	<ul class="dropdown-menu" role="menu" style=" margin-bottom: 5px; *width: 180px;" aria-labelledby="dropdownMenu">
	<?php foreach ($genres as $key => $genre): ?>

		<?php $class = (empty($genre['children']))? '': 'dropdown-submenu' ; ?>

		<li class="<?= $class ?> level-1" ><a class="genre_item" data-id="<?= $genre['id'];?>" data-level="1" data-name="<?= $genre['name'];?>"><?= $genre['name'];?></a>
			<?php if (!empty($genre['children'])):?>
				<ul class="dropdown-menu">
					<?php foreach ($genre['children'] as $key => $child): ?>

						<?php $class = (empty($child['children']))? '': 'dropdown-submenu' ; ?>

						<li class="<?= $class ?>  level-2"><a class="genre_item" data-id="<?= $child['id'];?>" data-level="2"  data-name="<?= $child['name'];?>"><?= $child['name'];?></a></li>
					<? endforeach; ?>			
				</ul>
			<?php endif ?>
		</li>
	<? endforeach; ?>
	</ul>
</div>
