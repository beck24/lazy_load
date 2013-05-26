<?php

echo elgg_echo('lazy_load:label:custom_selectors');

echo elgg_view('input/plaintext', array(
	'name' => 'params[custom_selectors]',
	'value' => $vars['entity']->custom_selectors
));

echo elgg_view('output/longtext', array(
	'value' => elgg_echo('lazy_load:help:custom_selectors'),
	'class' => 'elgg-subtext'
));

echo '<br><br>';