jQuery(function() {
	//caching
	//next and prev buttons
	var $rps_next	= jQuery('#rps_next');
	var $rps_prev	= jQuery('#rps_prev');
	//wrapper of the left items
	var $rps_list 	= jQuery('#rps_list');
	var $pages		= $rps_list.find('.rps_page');
	//how many pages
	var cnt_pages	= $pages.length;
	//the default page is the first one
	var page		= 1;
	//list of news (left items)
	var $items 		= $rps_list.find('.rps_item');
	//the current item being viewed (right side)
	var $rps_preview = jQuery('#rps_preview');
	//index of the item being viewed. 
	//the default is the first one
	var current		= 1;
	var rps_ht10 = rps_ht +10;
	
	/*
	for each item we store its index relative to all the document.
	we bind a click event that slides up or down the current item
	and slides up or down the clicked one. 
	Moving up or down will depend if the clicked item is after or
	before the current one
	*/
	$items.each(function(i){
		var $item = jQuery(this);
		$item.data('idx',i+1);
		
		$item.bind('click',function(){
			var $this 		= jQuery(this);
			$rps_list.find('.selected').removeClass('selected');
			$this.addClass('selected');
			var idx			= jQuery(this).data('idx');
			var $current 	= $rps_preview.find('.rps_content:nth-child('+current+')');
			var $next		= $rps_preview.find('.rps_content:nth-child('+idx+')');
			
			if(idx > current){
				$current.stop().animate({'top':'-'+rps_ht+'px'},600,'easeOutBack',function(){
					jQuery(this).css({'top':''+rps_ht10+'px'});
				});
				$next.css({'top':''+rps_ht10+'px'}).stop().animate({'top':'3px'},600,'easeOutBack');
			}
			else if(idx < current){
				$current.stop().animate({'top':''+rps_ht10+'px'},600,'easeOutBack',function(){
					jQuery(this).css({'top':''+rps_ht10+'px'});
				});
				$next.css({'top':'-'+rps_ht+'px'}).stop().animate({'top':'3px'},600,'easeOutBack');
			}
			current = idx;
		});
	});
	
	/*
	shows next page if exists:
	the next page fades in
	also checks if the button should get disabled
	*/
	$rps_next.bind('click',function(e){
		var $this = jQuery(this);
		$rps_prev.removeClass('disabled');
		++page;
		if(page == cnt_pages)
			$this.addClass('disabled');
		if(page > cnt_pages){ 
			page = cnt_pages;
			return;
		}	
		$pages.hide();
		$rps_list.find('.rps_page:nth-child('+page+')').fadeIn();
		e.preventDefault();
	});
	/*
	shows previous page if exists:
	the previous page fades in
	also checks if the button should get disabled
	*/
	$rps_prev.bind('click',function(e){
		var $this = jQuery(this);
		$rps_next.removeClass('disabled');
		--page;
		if(page == 1)
			$this.addClass('disabled');
		if(page < 1){ 
			page = 1;
			return;
		}
		$pages.hide();
		$rps_list.find('.rps_page:nth-child('+page+')').fadeIn();
		e.preventDefault();
	});
	
});