<?php

elgg_register_event_handler('init', 'system', 'lazy_load_init');

function lazy_load_init() {

  elgg_extend_view('css/elgg', 'lazy_load/css');
  
  require_once dirname(__FILE__) . '/vendors/Mobile_Detect.php';
  
  $mobile = new Mobile_Detect();
  
  if (!$mobile->is('iOS')) {
	// register our js library
	$js = elgg_get_simplecache_url('js', 'lazy_load/js');
	elgg_register_simplecache_view('js/lazy_load/js');
	elgg_register_js('lazy_load.js', $js);
  
	//use lazy load on all pages
	elgg_load_js('lazy_load.js');
  
	elgg_register_plugin_hook_handler('view', 'page/default', 'lazy_load_defaultpage');
  }
}


function lazy_load_defaultpage($hook, $type, $return, $params) {
  
  preg_match_all('/<img[^>]+>/i',$return, $imgs);
  
  // Thanks to Viorel Tabara for the regex!
  $regex = "/<script(.*)<\/script>/msU";
  preg_match_all($regex, $return, $scripts);
  // stringify the scripts

  $script_string = '';
  if (!empty($scripts[0])) {
	foreach ($scripts[0] as $script) {
	  $script_string .= $script;
	}
  }

  $placeholder = elgg_get_site_url() . '_graphics/spacer.gif';
  
  foreach ($imgs[0] as $img) {
	
	if (strpos($script_string, $img) !== false) {
	  // this image might be being used in inline js
	  // so we'll just leave it alone
	  continue;
	}	
	
	$pattern = '/([a-zA-Z\-]+)\s*=\\s*("[^"]*"|\'[^\']*\'|[^"\'\\s>]*)/';
	preg_match_all($pattern, $img, $attributes, PREG_SET_ORDER);
	
	//format the attributes
	$vars = array();
	foreach ($attributes as $key => $attribute) {
	  // strip beginning/end quotations from value
	  $attribute[2] = substr($attribute[2], 1, (strlen($attribute[2]) - 2));
	  
	  $vars[$attribute[1]] = $attribute[2];
	}
	
	if (!empty($vars['data-original'])) {
	  // this image already has a data-original attribute, so we can't use it
	  continue;
	}
	else {
	  $vars['data-original'] = $vars['src'];
	}
	
	$vars['src'] = $placeholder;
	
	if (!empty($vars['class'])) {
	  // no class was set originally, we need to set it ourselves
	  $vars['class'] = $vars['class'] . ' lazy-load';
	}
	else {
	  $vars['class'] = 'lazy-load';
	}
	
	
	$replacement_img = elgg_view('output/img', $vars);
	$replacement_img .= "<noscript>$img</noscript>";
	$return = str_replace($img, $replacement_img, $return);
  }
  
  return $return;
}