<?php

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

}

elgg.register_hook_handler('init', 'system', elgg.lazy_load.init);