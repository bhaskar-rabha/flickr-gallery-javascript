<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  <head>
    <title>Flickr JSON</title>
    <link rel="stylesheet" type="text/css" href="assets/css/style.css"/>
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
   <script>
	var flickr = {
 		selector:'#flickr',
 		apiKey:'c48a2139266a0bcf07b8e30b593d2145',
 		tags:'temple',
 		apiUrl:'https://api.flickr.com/services/rest/',
 		tagmode:'any',
		perPage:0,
		page:1,
 		init:function()
 		{
 			this.apiKey = 'c48a2139266a0bcf07b8e30b593d2145';
 			this.fetchJSONData();
 			this.bindEvent();		
 		},
 		fetchJSONData:function()
 		{			
			var self = this;
			$.ajax({ 
				type:'get',
				url:self.apiUrl,
				data: {
					tags: this.tags,
					tagmode: this.tagmode,					
					format: 'json',
					safe_search:1,
					method:'flickr.photos.search',
					api_key:this.apiKey,
					jsoncallback:'jsonFlickrApi'
				}		

			});
 		},
		closeButton:function()
		{
			$('#overlay').remove();
		},
		previousPhoto:function(idx)
		{			
			var i = parseInt(idx) - 1;
			this.showPhoto(i-1);
		},
		showPhoto:function(idx)
		{
			var rel = $('#tab-'+ this.page).find('.li-img-flickr').eq(idx).find('img').attr('rel');
			var index = $('#tab-'+ this.page).find('.li-img-flickr').eq(idx).find('img').attr('dataindex');
			$('#overlay').remove();
			var overlay = $('<div id="overlay"><div id="popupImg"></div></div>');
 				overlay.appendTo(document.body) 
				var html = '<div style="width:100%;text-align:right"><a href="javascript:flickr.closeButton(this);"><img src="assets/images/Close-2-icon.png"/></a></div><div class="arrow" style="float:left;height:100%"><span style="color:#ff;float:left"><a href="javascript:flickr.previousPhoto(\''+ index +'\');">';
				if(idx >= 1) html +='<img src="assets/images/Actions-arrow-left-icon.png"/>';
				
				html +='</a></span></div><div style="float:left"><img  src="'+ rel + '" /></div><div class="arrow" style="float:left;height:100%"><span style="color:#ff"><a href="javascript:flickr.nextPhoto(\''+ index +'\');">';				
				
				if(idx < parseInt($('#tab-'+ this.page).find('.li-img-flickr').length-1)) html +='<img src="assets/images/Actions-arrow-right-icon.png"/>';				
				
				html +='</a></span></div>';
				
 				$('#popupImg').html(html);
		},
		nextPhoto:function(idx)
		{
			var i = parseInt(idx) + 1;
			this.showPhoto(i-1);
		
		},
		goPage:function()
		{
			$('.flickr-pagination li').removeClass('active');
			if(this.page>1) $('.flickr-pagination li').eq(0).addClass('active');
			if(this.page < Math.ceil(rsp.photos.photo.length/20)) $('.flickr-pagination li').eq(1).addClass('active');
			$('#flickr ul').removeClass('active');
			$('#tab-'+this.page).addClass('active');
		},
		goToPage:function()
		{
			var i = parseInt($('#textSearch').val());
			if(i>0 && i<= Math.ceil(rsp.photos.photo.length/20)){ this.page = i; this.goPage(); }else{  alert('Wrong Page'); }
		},
		previousPage:function()
		{
			this.page = this.page - 1;
			this.goPage()
		},
		nextPage:function()
		{
			this.page = this.page + 1;
			this.goPage()
		},
 		bindEvent:function()
 		{ 			
 			var self = this;
			$('.li-img-flickr a').on('click',function()
 			{
 				var idx = $(this).parent().index();
				self.showPhoto(idx);
			});
			$('.pic-download').on('click',function(e)
 			{
 				e.preventDefault();
				event = e || window.event
	     
				if (event.stopPropagation) {
					event.stopPropagation()
				} else {
					event.cancelBubble = true
				}
				var d_link = $(this).attr('href');
				window.location.assign(d_link);
				self.closeButton();
			});			
 		}
 	};
	function jsonFlickrApi(rsp) {
		    window.rsp = rsp;
		    var s = "";
		 	var html = '';
 			var ul = $("<ul/>",{
 				'class':'flickr-ul active',
				id:'tab-1'
 			});
		    var liCount = 1;
			flickr.perPage = rsp.photos.photo.length;
		    for (var i=0; i < flickr.perPage; i++) 
			{		      	
				liCount++;
				photo = rsp.photos.photo[i];
		      	t_url = "http://farm" + photo.farm + ".static.flickr.com/" + 
		        photo.server + "/" + photo.id + "_" + photo.secret + "_" + "t.jpg";
				n_url = "http://farm" + photo.farm + ".static.flickr.com/" + 
		        photo.server + "/" + photo.id + "_" + photo.secret +  ".jpg";
				d_url = "http://farm" + photo.farm + ".static.flickr.com/" + 
		        photo.server + "/" + photo.id + "_" + photo.secret +  "_d.jpg";				
		      	p_url = "http://www.flickr.com/photos/" + photo.owner + "/" + photo.id;
		    	var li = $("<li/>",{
					html:$( "<a>", 
						{ 
							href:'javasript:void(0);',	
							html:$( "<img>", 
							{ 
								width:'150px',
								rel:n_url,
								dataindex: liCount-1	
							}).attr( "src", t_url)

						}),						
					'class':"li-img-flickr"
				})
				var div = $( "<div>", 
				{ 
					html:$("<a>",{
					html:'Download original',
					'class':'pic-download'
					}
					).attr('href',d_url)					
				}).appendTo(li);
				li.appendTo(ul);			
				
				if(liCount > 20)
				{
					liCount = 1;
					ul.appendTo('#flickr');
					ul = $("<ul/>",{
						'class':'flickr-ul',
						id:'tab-'+ (Math.ceil(i/20)+1)
					});
				}
				
		    }
			if((flickr.perPage%20) != 0) ul.appendTo('#flickr');
			var ulPagination = $("<ul/>",{
 				'class':'flickr-pagination'				
 			});
			
			if(parseInt(rsp.photos.photo.length/20)>1)
			{
				var liPage = $("<li/>",{
							html: $( "<a>", 
							{									
								html: ' <-Previous Page '	
							}).attr( "href", 'javascript:flickr.previousPage()'),
							'class':"page"
						}).appendTo(ulPagination);
					liPage = $("<li/>",{
							html: $( "<a>", 
							{									
								html: 'Next Page-> '	
							}).attr( "href", 'javascript:flickr.nextPage()'),
							'class':"page active"
						}).appendTo(ulPagination);
					ulPagination.appendTo('#flickr');	
			}
			
			$('<span>',{
				html:'Showing page 1 of '+ parseInt(rsp.photos.photo.length/20),
				id:'page-text'			
			}).appendTo('#flickr');;
			$('<br/>').appendTo('#flickr');			
			$('<span>',{
				html:'Go to page ',
				id:"search-label"
			}).appendTo('#flickr');		
			
			$('#flickr').append('<input type="text" id="textSearch" name="search" value=""/> <a id="btnGo" href="javascript:flickr.goToPage()">Go</a>');
			
			
		    flickr.bindEvent();
		}
	  	$(document).ready(function(){
	  		flickr.init();
	  	});

  </script>
  </head>
<body>
	<div id="wrapper">
		<div id="flickr">				
		</div>
	</div>

</body>

</html>