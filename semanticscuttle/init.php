<?php
class SemanticScuttle extends Plugin {
	private $link;
	private $host;

	function init($host) {
		$this->link = $host->get_link();
		$this->host = $host;

		$host->add_hook($host::HOOK_ARTICLE_BUTTON, $this);
	        $host->add_hook($host::HOOK_PREFS_TAB, $this);
	}

	function about() {
		return array(2.0,
			"Save article in SemanticScuttle",
			"versvs");
	}

	function save() {
		$semanticscuttle_url = db_escape_string($this->link, $_POST["semanticscuttle_url"]);
    		$this->host->set($this, "semanticscuttle", $semanticscuttle_url);
		echo "Value set to $semanticscuttle_url";
	}

	function get_js() {
		return file_get_contents(dirname(__FILE__) . "/semanticscuttle.js");
	}

	  function hook_prefs_tab($args) {
	    if ($args != "prefPrefs") return;

	    print "<div dojoType=\"dijit.layout.AccordionPane\" title=\"".__("SemanticScuttle")."\">";

	    print "<br/>";

	    $value = $this->host->get($this, "semanticscuttle");

	    print "<div id=\"semantic-header\" style=\"margin-bottom: 2em;\">";
	    print "<strong>Options for Semantic Scuttle Plugin</strong><br />";
	    print "(by Jose Alcántara - <a target=\"_blank\" title=\"Open new tab to visit developer website\" style=\"text-decoration:underline;\" href=\"http://www.versvs.net\">http://www.versvs.net</a>)";

	    print "</div>";
	    print "<form dojoType=\"dijit.form.Form\">";

	    print "<script type=\"dojo/method\" event=\"onSubmit\" args=\"evt\">
		   evt.preventDefault();
		   if (this.validate()) {
		       console.log(dojo.objectToQuery(this.getValues()));
		       new Ajax.Request('backend.php', {
			                    parameters: dojo.objectToQuery(this.getValues()),
			                    onComplete: function(transport) {
			                         notify_info(transport.responseText);
			                    }
			                });
		   }
		   </script>";

	    print "<input dojoType=\"dijit.form.TextBox\" style=\"display : none\" name=\"op\" value=\"pluginhandler\">";
	    print "<input dojoType=\"dijit.form.TextBox\" style=\"display : none\" name=\"method\" value=\"save\">";
	    print "<input dojoType=\"dijit.form.TextBox\" style=\"display : none\" name=\"plugin\" value=\"semanticscuttle\">";
	    print "<table width=\"100%\" class=\"prefPrefsList\">";
		print "<tbody><tr><td style=\"width: 10%; min-width: 100px;\">".__("SemanticScuttle url")."</td>";
		print "<td class=\"prefValue\"><input style=\"width: 25em;\" dojoType=\"dijit.form.ValidationTextBox\" required=\"1\" name=\"semanticscuttle_url\" regExp='^(http|https)://.*' value=\"$value\"></td></tr></tbody>";
	    print "</table>";
	    print "<p><button dojoType=\"dijit.form.Button\" type=\"submit\">".__("Save")."</button>";

	    print "</form>";

	    print "</div>"; #pane

	  }

	function hook_article_button($line) {
		$article_id = $line["id"];

		$rv = "<img src=\"plugins/semanticscuttle/semanticscuttle.png\"
			class='tagsPic' style=\"cursor : pointer\"
			onclick=\"shareArticleToSemanticScuttle($article_id)\"
			title='".__('Share on SemanticScuttle')."'>";

		return $rv;
	}

	function getSemanticScuttle() {
		$id = db_escape_string($this->link, $_REQUEST['id']);

		$result = db_query($this->link, "SELECT title, link
				FROM ttrss_entries, ttrss_user_entries
				WHERE id = '$id' AND ref_id = id AND owner_uid = " .$_SESSION['uid']);

		if (db_num_rows($result) != 0) {
			$title = truncate_string(strip_tags(db_fetch_result($result, 0, 'title')),
				100, '...');
			$article_link = db_fetch_result($result, 0, 'link');
		}
		
		$semanticscuttle_url = $this->host->get($this, "semanticscuttle");

		print json_encode(array("title" => $title, "link" => $article_link,
			    "id" => $id, "semanticscuttleurl" => $semanticscuttle_url));
	}

}
?>
