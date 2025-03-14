<?php

$id = optional_param('id', '', PARAM_RAW);
$blockcontext = CONTEXT_BLOCK::instance($id);
$blockrecord = $DB->get_record('block_instances', array('id' => $instanceid), '*');
$config = unserialize(base64_decode($blockrecord->configdata));

echo "<pre>";
var_export($config);
exit;