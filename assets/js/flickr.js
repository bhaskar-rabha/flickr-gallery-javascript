var flickr = {
 		selector:'#flickr',
 		apiKey:'c48a2139266a0bcf07b8e30b593d2145',
 		tags:'temple',
 		apiUrl:'https://api.flickr.com/services/rest/',
 		tagmode:'any',
		perPage:20,
		page:1,
		totalRecord:0,
 		init:function(config)
 		{
 			if(typeof config != 'undefined')
 			{
 				if(typeof config.selector != 'undefined')	{ this.selector = this.selector;	}
 				if(typeof config.apiKey != 'undefined')		{ this.apiKey 	= this.apiKey; 		}
 				if(typeof config.tags != 'undefined')		{ this.tags 	=  config.tags; 	}
 				if(typeof config.tagmode != 'undefined')	{ this.tagmode 	=  config.tagmode; 	}	
 				if(typeof config.perPage != 'undefined')	{ this.perPage 	=  config.perPage; 	}	
 			}
 			
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
					jsoncallback:'?',
					extras:'url_m',
					nojsoncallback:1,
					per_page:self.perPage,
					page:this.page
				}		

			}).done(function(rsp) 
			{			   
				$(self.selector).html('');
				window.rsp = rsp;			    
			 	var html = '';
	 			var ul = $("<ul/>",{
	 				'class':'flickr-ul active',
					id:'tab'
	 			});
				self.totalRecord = rsp.photos.pages;
				
				for (var i=1; i <= rsp.photos.photo.length; i++) 
				{				
					var photo = rsp.photos.photo[i-1];
					var t_url = "http://farm" + photo.farm + ".static.flickr.com/" + 
					photo.server + "/" + photo.id + "_" + photo.secret + "_" + "t.jpg";
					var n_url = "http://farm" + photo.farm + ".static.flickr.com/" + 
					photo.server + "/" + photo.id + "_" + photo.secret +  ".jpg";
					var d_url = "http://farm" + photo.farm + ".static.flickr.com/" + 
					photo.server + "/" + photo.id + "_" + photo.secret +  "_d.jpg";				
					var p_url = "http://www.flickr.com/photos/" + photo.owner + "/" + photo.id;
					var li = $("<li/>",{
						html:$( "<a>", 
						{ 
							href:'javascript:void(0);',	
							html:$( "<img>", 
							{ 
								width:'150px',
								rel:n_url,
								dataindex: i	
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
				}
				ul.appendTo(self.selector);
				
				var ulPagination = $("<ul/>",{
	 				'class':'flickr-pagination'				
	 			});
				
				if(parseInt(self.totalRecord)>1)
				{
					var active = (self.page > 1)?' active':'';
					var liPage = $("<li/>",{
								html: $( "<a>", 
								{									
									html: ' <-Previous Page '	
								}).attr( "href", 'javascript:flickr.previousPage()'),
								'class':"page" + active
							}).appendTo(ulPagination);
						liPage = $("<li/>",{
								html: $( "<a/>", 
								{									
									html: 'Next Page-> '	
								}).attr( "href", 'javascript:flickr.nextPage()'),
								'class':"page active"
							}).appendTo(ulPagination);
						ulPagination.appendTo(self.selector);	
				}
				
				$('<span/>',{
					html:'Showing page '+ self.page +' of '+ self.totalRecord,
					id:'page-text'			
				}).appendTo(self.selector);;
				$('<br/>').appendTo(self.selector);			
				$('<span/>',{
					html:'Go to page ',
					id:"search-label"
				}).appendTo(self.selector);		
				
				var inputTxt = $("<input/>",{					
					type:'text',
					id:'textSearch',
					name:'search',
					value:self.page
				}).attr('size',10).appendTo(self.selector);

				var aBtnGo = $("<a/>",{
					id:'btnGo',
					href:'javascript:flickr.goToPage()',
					html:'Go'
				}).appendTo(self.selector);				

		    	self.bindEvent();
			})

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
			
			var rel = $('.li-img-flickr').eq(idx).find('img').attr('rel');
			var index = $('.li-img-flickr').eq(idx).find('img').attr('dataindex');
			$('#overlay').remove();
			var overlay = $('<div id="overlay"><div id="popupImg"><img src="assets/images/loading.gif" /></div></div>');
			overlay.appendTo(document.body) 
			var html = '<div style="width:100%;text-align:right"><a href="javascript:flickr.closeButton(this);"><img src="assets/images/Close-2-icon.png"/></a></div><div class="arrow" style="float:left;height:100%"><span style="color:#ff;float:left"><a href="javascript:flickr.previousPhoto(\''+ index +'\');">';
			if(index > 1) html +='<img src="assets/images/Actions-arrow-left-icon.png"/>';
			
			html +='</a></span></div><div style="float:left"><img  src="'+ rel + '" /></div><div class="arrow" style="float:left;height:100%"><span style="color:#ff"><a href="javascript:flickr.nextPhoto(\''+ index +'\');">';				
			
			if(index < this.perPage) html +='<img src="assets/images/Actions-arrow-right-icon.png"/>';				
			
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
			this.fetchJSONData();
			$('#page-text').text('Showing page '+ this.page + ' of '+ this.totalRecord);
			$('.flickr-pagination li').removeClass('active');
			if(this.page > 1) $('.flickr-pagination li').eq(0).addClass('active');
			if(this.page < this.totalRecord) $('.flickr-pagination li').eq(1).addClass('active');
			
		},
		goToPage:function()
		{
			var i = parseInt($('#textSearch').val());
			if(i>0 && i<= this.totalRecord){ this.page = i; this.goPage(); }else{  alert('Wrong Page'); }
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

