/**
 * ----------------------------------------------------------------------
 * Copyright (C) 2008 by www.xiami.com. http://www.xiami.com
 * ----------------------------------------------------------------------
 * Filename: xiami_music.js Original Author(s): sospartan<sospartan@gmail.com>
 * Purpose: xiami music TinyMCE plugin
 * ----------------------------------------------------------------------
 */

( function() {
	tinymce.create('tinymce.plugins.XiamiPlugin', {
		init : function(ed, url) {
			var t = this;

			t.url = url;
			t.editor = ed;
			// /////////////////////////////////////
		// song button
		ed.addCommand('xiami_song', function() {
			var text = prompt('输入歌曲名称', '');
			if (text != null && text != '') {
				t.editor.execCommand('mceInsertContent', false,
						'[song]' + text + '[/song]');
			}
		});

		ed.addButton('xiami_song', {
			title : '插入歌曲',
			cmd : 'xiami_song',
			image : url + '/images/song.gif'
		});
		// ///////////////////////////////////////
		// album button
		ed.addCommand('xiami_album', function() {
			var text = prompt('输入专辑名称', '');
			if (text != null && text != '') {
				t.editor.execCommand('mceInsertContent', false,
						'[album]' + text + '[/album]');
			}
		});
		ed.addButton('xiami_album', {
			title : '插入专辑',
			cmd : 'xiami_album',
			image : url + '/images/album.gif'
		});
		// ////////////////////////////////////////////
		// artist button
		ed.addCommand('xiami_artist', function() {
			var text = prompt('输入艺人名', '');
			if (text != null && text != '') {
				t.editor.execCommand('mceInsertContent', false,
						'[artist]' + text + '[/artist]');
			}
		});
		ed.addButton('xiami_artist', {
			title : '插入艺人',
			cmd : 'xiami_artist',
			image : url + '/images/artist.gif'
		});
		// ////////////////////////////////////////////
		// music button
		ed.addCommand('xiami_music', function() {
			var ar = prompt('输入艺人名', '');
			ar = ar==null?'':ar;
			var al = prompt('输入专辑名', '');
			al = al==null?'':al;
			var so = prompt('输入歌曲名', '');
			so = so==null?'':so;
			if (so!='') {
				t.editor.execCommand('mceInsertContent', false,
						'[music]' + so+'(' +ar+'/'+al+')'+ '[/music]');
			}
		});
		ed.addButton('xiami_music', {
			title : '插入详细信息',
			cmd : 'xiami_music',
			image : url + '/images/music.gif'
		});

	},

	getInfo : function() {
		return {
			longname : 'Xiami Music plugin',
			author : 'sospartan',
			authorurl : 'http://www.xiami.com',
			infourl : 'http://www.xiami.com',
			version : "1.0"
		};
	}
	});

	// Register plugin
	tinymce.PluginManager.add('xiami_tags', tinymce.plugins.XiamiPlugin);
})();
