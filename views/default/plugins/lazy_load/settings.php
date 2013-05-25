<?php

echo elgg_echo('lazy_load:label:custom_selectors');

echo elgg_view('input/plaintext', array(
	'name' => 'params[custom_selectors]',
	'value' => $vars['entity']->custom_selectors
));

echo '<br><br>';