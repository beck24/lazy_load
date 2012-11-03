<?php

register_elgg_event_handler('init', 'system', 'lazy_load_init');

function lazy_load_init() {

  elgg_extend_view('metatags', 'lazy_load/metatags');
  elgg_extend_view('footer/analytics', 'lazy_load/analytics');
  
  register_plugin_hook('display', 'view', 'lazy_load_defaultpage');
}


function lazy_load_defaultpage($hook, $type, $return, $params) {
  global $CONFIG;
  
  if ($params['view'] != 'pageshells/pageshell') {
	return $return;
  }
  
  preg_match_all('/<img[^>]+>/i',$return, $imgs);
  
  $placeholder = $CONFIG->wwwroot . '_graphics/spacer.gif';
  
  foreach ($imgs[0] as $img) {
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
	
	$attributes = '';
	$count = 0;
	foreach ($vars as $name => $value) {
		if ($count != 0) {
			$attributes .= ' ';
		}
		
		$attributes .= $name . '="' . $value . '"';
		
		$count++;
	}
	
	$replacement_img = "<img $attributes>";
	$replacement_img .= "<noscript>$img</noscript>";
	$return = str_replace($img, $replacement_img, $return);
  }
  
  return $return;
}