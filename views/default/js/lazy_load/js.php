<?php

// custom selectors
$cs_string = elgg_get_plugin_setting('custom_selectors', 'lazy_load');
$cs = array();
if ($cs_string) {
	$cstmp = explode("\n", $cs_string);
	foreach ($cstmp as $s) {
		$cs[] = trim($s);
	}
}

// lazyload in a view so it can be overwritten by others if necessary
echo elgg_view('js/lazy_load/jquery.lazyload');

?>




/*
	Elgg Lazy Loading implementation by Matt Beckett
*/

elgg.provide('elgg.lazy_load');

elgg.lazy_load.init = function() {
  
  $("img.lazy-load").show().lazyload({
	  threshold : 200,
	  effect : "fadeIn",
	  skip_invisible : false,
	  failure_limit: 10
  });

  <?php
  
  // if there are columns being used, the columns further down the DOM don't work properly
  // unless lazy_load is called individually
  // so do that for custom selectors
  if ($cs && is_array($cs)) {
	foreach ($cs as $selector) {
	?>
	
	$("<?php echo $selector; ?> img.lazy-load").show().lazyload({
	  threshold : 200,
	  effect : "fadeIn",
	  skip_invisible : false,
	  failure_limit: 10
	});
	
	<?php
	}
	
	// if a div is set to overflow: auto, or some other scrolling method, we need to
	// trigger the window resize event when that gets scrolled to allow images in that
	// div to lazy_load
	$selectors = implode(', ', $cs);
	
	?>
	
	$("<?php echo $selectors; ?>").scroll(function() {
		$(window).trigger('resize');
	});
	
	<?php
  }
  ?>
  
  $(document).ajaxComplete(function(e) {
    setTimeout(function() {$(window).trigger("resize")}, 100); //trigging the window resize event forces $.lazyload to look for new images to load
  });
  
  // trigger scroll on pageload after small delay (to make sure everything is bound properly)
  setTimeout(function() {$(window).trigger("resize")}, 100);
}

elgg.register_hook_handler('init', 'system', elgg.lazy_load.init);