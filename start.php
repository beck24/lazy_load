<?php

elgg_register_event_handler('init', 'system', 'lazy_load_init');

function lazy_load_init() {
  
  // register our html parser library
  elgg_register_library('lazy_load', elgg_get_plugins_path() . 'lazy_load/lib/simple_html_dom.php');
  elgg_register_library('ganon', elgg_get_plugins_path() . 'lazy_load/lib/ganon.php');
  
  // register our js library
  $js = elgg_get_simplecache_url('js', 'lazy_load/js');
  elgg_register_simplecache_view('js/lazy_load/js');
  elgg_register_js('lazy_load.js', $js);
  
  //use lazy load on all pages
  elgg_load_js('lazy_load.js');
  
  elgg_register_plugin_hook_handler('view', 'page/default', 'lazy_load_defaultpage');
}


function lazy_load_defaultpage($hook, $type, $return, $params) {
  
  preg_match_all('/<img[^>]+>/i',$return, $imgs);
  
  $grey = elgg_get_site_url() . 'mod/lazy_load/graphics/grey.gif';
  
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
	
	$vars['src'] = $grey;
	
	if (!empty($vars['class'])) {
	  // no class was set originally, we need to set it ourselves
	  $vars['class'] = $vars['class'] . ' lazy-load';
	}
	else {
	  $vars['class'] = 'lazy-load';
	}
	
	
	$replacement_img = elgg_view('output/img', $vars);
	$return = str_replace($img, $replacement_img, $return);
  }
  
  return $return;
  /*
  elgg_load_library('ganon');
  
  $html = str_get_dom($return);
  
  foreach ($html('img') as $img) {
	echo $img->src . '<br><br>';
  }
  
  exit; */
  /*
  elgg_load_library('lazy_load');
  
  $placeholder = elgg_get_site_url() . 'mod/lazy_load/graphics/grey.gif';
  
  // parse the html
  $html = str_get_html($return);
  
  // get the number of images
  $num = count($html->find('img'));
  
  // add our class to all images
  // and move the src to data-original
  for ($i=0; $i<$num; $i++) {
	$class = $html->find('img', $i)->class;
	
	// set our new class
	$newclass = 'lazy-load';
	if ($class) {
	  $newclass = $class . ' lazy-load';
	}
	
	$html->find('img', $i)->class = $newclass;
	
	
	// set our sources
	$src = $html->find('img', $i)->src;
	$html->find('img', $i)->src = $placeholder;
	$html->find('img', $i)->setAttribute('data-original', $src);
  }

  return $html;
   * *
   */
}