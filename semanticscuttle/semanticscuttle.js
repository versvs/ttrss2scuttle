	function shareArticleToSemanticScuttle(id) {
	try {
		var query = "?op=pluginhandler&plugin=semanticscuttle&method=getInfo&id=" + param_escape(id);

		console.log(query);

		var d = new Date();
  		var ts = d.getTime();

		var w = window.open('backend.php?op=backend&method=loading', 'ttrss_tweet',
			"status=0,toolbar=0,location=0,width=800,height=600,scrollbars=1,menubar=0");

		new Ajax.Request("backend.php",	{
			parameters: query,
			onComplete: function(transport) {
				var ti = JSON.parse(transport.responseText);

				var share_url = "http://www.yourdomain.com/root/of/your/instance/bookmarks.php/?action=add" +
					"&address=" + param_escape(ti.link) +
					"&title=" + param_escape(ti.title) +
					"&description=";

				w.location.href = share_url;

			} });


	} catch (e) {
		exception_error("shareArticleIdentica", e);
	}




	}

