

		function parseTpl(str, data) {
		var result;
		var patt = new RegExp("%([a-zA-z0-9]+)%");
		while ((result = patt.exec(str)) != null) {
			var v = data[result[1]] || '';
			str = str.replace(new RegExp(result[0], "g"), v);
		}
		return str;
	}
		String.prototype.parseTpl = function(data) {
		return parseTpl(this, data);
	};
	
	jQuery.ajaxSetup( {
		type : "GET",
		timeout : 4000,
		beforeSend : function() {
		},
		error : function() {
			jQuery("#xiamiPopupContainer").fadeOut();
		}
	}); 

	var queryCache = [];
	function searchOut(query) {
		 if (query == '' || query == null)
			return;
		queryParts = query.split(':');
		var template = '';
		// 1.song (default) 2.album 3.artist 4.all together
		switch (queryParts[0]) {
		case 'song':
			template = '<p><a target="_blank" href="http://www.xiami.com/song/%song_id%"><img class="xiamiAblumCover" src="http://img.xiami.com/%album_logo%"></a>&nbsp;' + '<a target="_blank" href="http://www.xiami.com/song/%song_id%">%song_name%</a>--' + '<a target="_blank" href="http://www.xiami.com/artist/%artist_id%">%artist_name%</a></p>' + '<p class="xiamihome" style="text-align:right;"><a target="_blank" href="http://www.xiami.com">more</a></p>';
			t = 1;
			break;
		case 'artist':
			template = '<p><a target="_blank" href="http://www.xiami.com/artist/%artist_id%"><img src="http://img.xiami.com/%logo%"></a>&nbsp;' + '<a target="_blank" href="http://www.xiami.com/artist/%artist_id%">%name%</a></p>' + '<p class="xiamihome" style="text-align:right;"><a target="_blank" href="http://www.xiami.com">more</a></p>';
			t = 3;
			break;
		case 'album':
			template = '<p><a target="_blank" href="http://www.xiami.com/album/%album_id%"><img class="xiamiAblumCover" src="http://img.xiami.com/%album_logo%"></a>&nbsp;' + '<a target="_blank" href="http://www.xiami.com/album/%album_id%">%title%</a>--' + '<a target="_blank" href="http://www.xiami.com/artist/%artist_id%">%artist_name%</a></p>' + '<p class="xiamihome" style="text-align:right;"><a target="_blank" href="http://www.xiami.com">more</a></p>';
			t = 2;
			break;
		default:
			query[0] = 'song';
			template = '<p><a target="_blank" href="http://www.xiami.com/song/%song_id%"><img class="xiamiAblumCover" src="http://img.xiami.com/%album_logo%"></a>&nbsp;' + '<a target="_blank" href="http://www.xiami.com/song/%song_id%">%song_name%</a>--' + '<a target="_blank" href="http://www.xiami.com/artist/%artist_id%">%artist_name%</a></p>' + '<p  class="xiamihome" style="text-align:right;"><a target="_blank" href="http://www.xiami.com">more</a></p>';
			t = 1;
			break;
		}

		var querydata = {
			k : queryParts[1],
			t : t,
			n : 1
		};

		jQuery.getJSON(
						"http://www.xiami.com/search/jsonp?callback=?",
						querydata,
						function(data) {
							var html = '';
							if (!data || data.length < 1 || data == 'null') {
								jQuery("#xiamiPopupContainer").fadeOut();
								return;
							}
							jQuery(data)
									.each(
											function(i, item) {
												item.album_logo = (item.album_logo ? item.album_logo
														: './res/img/default/cd100.gif');
												item.logo = (item.logo ? item.logo
														: './res/img/default/cd100.gif');
												html += template.parseTpl(item);
											});
							jQuery('#xiamiPopupContent').html(html);
							queryCache[query] = html;
						}); 
	}

	jQuery( function() {
		 var hideDelay = 500;
		var hideTimer = null;
		var searchTimer = null;

		var container = jQuery('<div id="xiamiPopupContainer">'
				+ '<table width="" border="0" cellspacing="0" cellpadding="0" align="center" class="xiamiPopupPopup">'
				+ '<tr>' + '   <td class="corner topLeft"></td>'
				+ '   <td class="top"></td>'
				+ '   <td class="corner topRight"></td>' + '</tr>' + '<tr>'
				+ '   <td class="left">&nbsp;</td>'
				+ '   <td><div id="xiamiPopupContent"></div></td>'
				+ '   <td class="right">&nbsp;</td>' + '</tr>' + '<tr>'
				+ '   <td class="corner bottomLeft">&nbsp;</td>'
				+ '   <td class="bottom">&nbsp;</td>'
				+ '   <td class="corner bottomRight"></td>' + '</tr>'
				+ '</table>' + '</div>');

		jQuery('body').append(container);

		jQuery('.xiami_link')
				.mouseover(
						function() {
							var querydata = jQuery(this).attr('rel');

							if (hideTimer)
								clearTimeout(hideTimer);

							var pos = jQuery(this).offset();
							var width = jQuery(this).width();
							container.css( {
								left : (pos.left + width) + 'px',
								top : pos.top - 5 + 'px'
							});

							jQuery('#xiamiPopupContent')
									.html(
											'<img id="containerloading" src="/wp-content/plugins/xiami_music/images/loading.gif">');
							container.fadeIn();
							if (queryCache[querydata]) {
								jQuery('#xiamiPopupContent').html(
										queryCache[querydata]);
							} else {
								searchTimer = setTimeout( function() {
									searchOut(querydata);
								}, 1000);
							}
						});

		jQuery('.xiami_link').mouseout( function() {
			if (hideTimer)
				clearTimeout(hideTimer);
			hideTimer = setTimeout( function() {
				container.fadeOut();
			}, hideDelay);
		});

		jQuery('#xiamiPopupContainer').mouseover( function() {
			if (hideTimer)
				clearTimeout(hideTimer);
		});

		jQuery('#xiamiPopupContainer').mouseout( function() {
			if (hideTimer)
				clearTimeout(hideTimer);
			hideTimer = setTimeout( function() {
				container.fadeOut();
			}, hideDelay);
		}); 
	});

