<?php
class xpwiki_plugin_tb extends xpwiki_plugin {
	function plugin_tb_init () {


	// $Id: tb.inc.php,v 1.2 2008/05/14 07:16:41 nao-pon Exp $
	/*
	 * PukiWiki/TrackBack: TrackBack Ping receiver and viewer
	 * (C) 2003-2005 PukiWiki Developers Team
	 * (C) 2003 Katsumi Saito <katsumi@jo1upk.ymt.prug.or.jp>
	 * License: GPL
	 *
	 * plugin_tb_action()    action
	 * plugin_tb_save($url, $tb_id)          Save or update TrackBack Ping data
	 * plugin_tb_output_response($rc, $msg)  Show a response code of the ping via HTTP/XML (then exit)
	 * plugin_tb_output_rsslist($tb_id)      Show pings for the page via RSS
	 * plugin_tb_output_htmllist($tb_id)     Show pings for the page via XHTML
		 */
	
		switch($this->cont['LANG']){
		case 'ja': $this->cont['PLUGIN_TB_LANGUAGE'] =  'ja-jp'; break;
		default  : $this->cont['PLUGIN_TB_LANGUAGE'] =  'en-us'; break;
		}
	
	// ----
	
		$this->cont['PLUGIN_TB_ERROR'] =    1;
		$this->cont['PLUGIN_TB_NOERROR'] =  0;

	}
	
	function plugin_tb_action()
	{
	//	global $trackback, $vars;
	
		if ($this->root->trackback && isset($this->root->vars['url'])) {
			// Receive and save a TrackBack Ping (both GET and POST)
			$url   = $this->root->vars['url'];
			$tb_id = isset($this->root->vars['tb_id']) ? $this->root->vars['tb_id'] : '';
			list($error, $message) = $this->plugin_tb_save($url, $tb_id);
	
			// Output the response
			$this->plugin_tb_output_response($error, $message);
			exit;
	
		} else {
			if ($this->root->trackback && isset($this->root->vars['__mode']) && isset($this->root->vars['tb_id'])) {
				// Show TrackBacks received (and exit)
				switch ($this->root->vars['__mode']) {
				case 'rss' : $this->plugin_tb_output_rsslist($this->root->vars['tb_id']);  break;
				case 'view': $this->plugin_tb_output_htmllist($this->root->vars['tb_id']); break;
				}
				exit;
	
			} else {
				// Show List of pages that TrackBacks reached
				$pages = $this->func->get_existpages($this->cont['TRACKBACK_DIR'], '.txt');
				if (! empty($pages)) {
					return array('msg'=>'Trackback list',
					'body'=>$this->func->page_list($pages, 'read', FALSE));
				} else {
					return array('msg'=>'', 'body'=>'');
				}
			}
		}
	}
	
	// Save or update TrackBack Ping data
	function plugin_tb_save($url, $tb_id)
	{
	//	global $vars, $trackback;
	//	static $fields = array( /* UTIME, */ 'url', 'title', 'excerpt', 'blog_name');
		static $fields = array();
		if (!isset($fields[$this->xpwiki->pid])) {$fields[$this->xpwiki->pid] = array(/*$this->cont['UTIME'],*/'url','title','excerpt','blog_name');}
	
		$die = '';
		if (! $this->root->trackback) $die .= 'TrackBack feature disabled. ';
		if ($url   == '') $die .= 'URL parameter is not set. ';
		if ($tb_id == '') $die .= 'TrackBack Ping ID is not set. ';
		if ($die != '') return array($this->cont['PLUGIN_TB_ERROR'], $die);
	
		if (! file_exists($this->cont['TRACKBACK_DIR'])) return array($this->cont['PLUGIN_TB_ERROR'], 'No such directory: TRACKBACK_DIR');
		if (! is_writable($this->cont['TRACKBACK_DIR'])) return array($this->cont['PLUGIN_TB_ERROR'], 'Permission denied: TRACKBACK_DIR');
	
		$page = $this->func->tb_id2page($tb_id);
		if ($page === FALSE) return array($this->cont['PLUGIN_TB_ERROR'], 'TrackBack ID is invalid.');
	
		// URL validation (maybe worse of processing time limit)
		$result = $this->func->http_request($url, 'HEAD');
		if ($result['rc'] !== 200) return array($this->cont['PLUGIN_TB_ERROR'], 'URL is fictitious.');
	
		// Update TrackBack Ping data
		$filename = $this->func->tb_get_filename($page);
		$data     = $this->func->tb_get($filename);
	
		$items = array($this->cont['UTIME']);
		foreach ($fields[$this->xpwiki->pid] as $key) {
			$value = isset($this->root->vars[$key]) ? $this->root->vars[$key] : '';
			if (preg_match('/[,"' . "\n\r" . ']/', $value))
				$value = '"' . str_replace('"', '""', $value) . '"';
			$items[$key] = $value;
		}
		$data[rawurldecode($items['url'])] = $items;
	
		$fp = fopen($filename, 'w');
		set_file_buffer($fp, 0);
		flock($fp, LOCK_EX);
		rewind($fp);
		foreach ($data as $line) {
			$line = preg_replace('/[\r\n]/s', '', $line); // One line, one ping
			fwrite($fp, join(',', $line) . "\n");
		}
		fclose($fp);
	
		return array($this->cont['PLUGIN_TB_NOERROR'], '');
	}
	
	// Show a response code of the ping via HTTP/XML (then exit)
	function plugin_tb_output_response($rc, $msg = '')
	{
		if ($rc == $this->cont['PLUGIN_TB_NOERROR']) {
			$rc = 0; // for $this->cont['PLUGIN_TB_NOERROR']
		} else {
			$rc = 1; // for $this->cont['PLUGIN_TB_ERROR']
		}
	
		$this->func->pkwk_common_headers();
		header('Content-Type: text/xml');
		echo '<?xml version="1.0" encoding="iso-8859-1"?>';
		echo '<response>';
		echo ' <error>' . $rc . '</error>';
		if ($rc) echo '<message>' . $msg . '</message>';
		echo '</response>';
		exit;
	}
	
	// Show pings for the page via RSS
	function plugin_tb_output_rsslist($tb_id)
	{
	//	global $script, $vars, $entity_pattern;
	
		$page = $this->func->tb_id2page($tb_id);
		if ($page === FALSE) return FALSE;
	
		$items = '';
		foreach ($this->func->tb_get($this->func->tb_get_filename($page)) as $arr) {
			// _utime_, title, excerpt, _blog_name_
			array_shift($arr); // Cut utime
			list ($url, $title, $excerpt) = array_map(
				create_function('$a', 'return htmlspecialchars($a);'), $arr);
			$items .= <<<EOD

   <item>
    <title>$title</title>
    <link>$url</link>
    <description>$excerpt</description>
   </item>
EOD;
		}
	
		$title = htmlspecialchars($page);
		$link  = $this->root->script . '?' . rawurlencode($page);
		$this->root->vars['page'] = $page;
		$excerpt = $this->func->strip_htmltag($this->func->convert_html($this->func->get_source($page)));
		$excerpt = preg_replace("/&{$this->root->entity_pattern};/", '', $excerpt);
		$excerpt = mb_strimwidth(preg_replace("/[\r\n]/", ' ', $excerpt), 0, 255, '...');
		$lang    = $this->cont['PLUGIN_TB_LANGUAGE'];
	
		$rc = <<<EOD
<?xml version="1.0" encoding="utf-8" ?>
<response>
 <error>0</error>
 <rss version="0.91">
  <channel>
   <title>$title</title>
   <link>$link</link>
   <description>$excerpt</description>
   <language>$lang</language>$items
  </channel>
 </rss>
</response>
EOD;
	
		$this->func->pkwk_common_headers();
		header('Content-Type: text/xml');
		echo mb_convert_encoding($rc, 'UTF-8', $this->cont['SOURCE_ENCODING']);
		exit;
	}
	
	// Show pings for the page via XHTML
	function plugin_tb_output_htmllist($tb_id)
	{
		$this->func->pkwk_common_headers();
		echo 'This function had been removed now. It will be created soon.<br />' . "\n";
		echo 'Sorry for your inconvenience.';
		exit;
	
		// ----
		// Skeleton Logic
	
	//	global $script;
	//	global $_tb_date;
	
		$page = $this->func->tb_id2page($tb_id);
		if ($page === FALSE) return FALSE;
	
		$data = $this->func->tb_get($this->func->tb_get_filename($page));
	
		// Sort: The first is the latest
		usort($data, create_function('$a,$b', 'return $b[0] - $a[0];'));
	
		$tb_body = '';
		foreach ($data as $x) {
			if (count($x) != 5) continue; // Ignore incorrect record
	
			list ($time, $url, $title, $excerpt, $blog_name) = $x;
			if ($title == '') $title = 'no title';
	
			$time = date($this->root->_tb_date, $time + $this->cont['LOCALZONE']); // May 2, 2003 11:25 AM
			$tb_body .= <<<EOD
EOD;
		}
	
		// Output start
		$this->func->pkwk_common_headers();
	
		// BugTrack/466 Care for MSIE trouble
		// Logically correct, but MSIE will treat the data like 'file downloading'
		//header('Content-type: application/xhtml+xml; charset=UTF-8');
		header('Content-type: text/html; charset=UTF-8'); // Works well
	
		$meta_content_type = $this->func->pkwk_output_dtd($this->cont['PKWK_DTD_XHTML_1_0_TRANSITIONAL'], 'UTF-8');
		$msg = <<<EOD
<head>
 $meta_content_type
</head>
<body>
 {$this->root->script}?tb_id=$tb_id<br /><br />
 $tb_body
</body>
</html>
EOD;
		echo mb_convert_encoding($msg, 'UTF-8', $this->cont['SOURCE_ENCODING']);
		exit;
	}
}
?>