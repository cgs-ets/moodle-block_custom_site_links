<?php
require_once('../../config.php');
$instanceid = required_param('id', PARAM_RAW);
$blockcontext = CONTEXT_BLOCK::instance($instanceid);
$blockrecord = $DB->get_record('block_instances', array('id' => $instanceid), '*');
$config = unserialize(base64_decode($blockrecord->configdata));





$data = [
    'instanceid' => $instanceid,
    'iconlinks' => array(),
    'textlinks' => array(),
    'linktypes' => '',
    'linknumber' => '',
];

$iconimages = array();
    $iconimagestokenised = array();
    $fs = get_file_storage();
    $files = $fs->get_area_files($blockcontext->id, 'block_custom_site_links', 'icons');
    foreach ($files as $file) {
        $id = $file->get_contenthash();
        $filename = $file->get_filename();
        if ($filename <> '.') {
            $src = moodle_url::make_pluginfile_url($file->get_contextid(), $file->get_component(), $file->get_filearea(),
                $file->get_itemid(), $file->get_filepath(), $filename );
            $iconimages[] = $src->out();
            $src = moodle_url::make_pluginfile_url($file->get_contextid(), $file->get_component(), $file->get_filearea(),
                $file->get_itemid(), $file->get_filepath(), $filename, false, true);
            $iconimagestokenised[] = $src->out();
        }
    }

    if (isset($config->iconlinkurl)) {
        foreach ($config->iconlinkurl as $i => $url) {
            if ($url == '') {
                continue;
            }


            if (true) {
                $icon = isset($iconimages[$i]) ? $iconimages[$i] : '';
                $icontok = isset($iconimagestokenised[$i]) ? $iconimagestokenised[$i] : '';
                $label = isset($config->iconlinklabel[$i]) ? $config->iconlinklabel[$i] : '';
                $target = ( isset($config->iconlinktarget[$i]) && $config->iconlinktarget[$i] ) ? '_blank' : '';
                $platforms = isset($config->iconlinkplatforms[$i]) ? json_encode($config->iconlinkplatforms[$i]) : json_encode(['web']);
                $roles = isset($config->iconlinkcampusroles[$i]) ? $config->iconlinkcampusroles[$i] : '';
                $years = isset($config->iconlinkyears[$i]) ? $config->iconlinkyears[$i] : '';
                $data['iconlinks'][] = [
                    'icon' => $icon,
                    'icontok' => $icontok,
                    'label' => $label,
                    'url' => $url,
                    'target' => $target,
                    'platforms' => $platforms,
                    'roles' => $roles,
                    'years' => $years,
                ];
            }
        }
    }

    if (isset($config->textlinkurl)) {

        foreach ($config->textlinkurl as $i => $url) {
            if ($url == '') {
                continue;
            }


            if (true) {
                $label = isset($config->textlinklabel[$i]) ? $config->textlinklabel[$i] : '';
                $target = ( isset($config->textlinktarget[$i]) && $config->textlinktarget[$i] ) ? '_blank' : '';
                $platforms = isset($config->textlinkplatforms[$i]) ? json_encode($config->textlinkplatforms[$i]) : json_encode(['web']);
                $roles = isset($config->textlinkcampusroles[$i]) ? $config->textlinkcampusroles[$i] : '';
                $years = isset($config->textlinkyears[$i]) ? $config->textlinkyears[$i] : '';
                $data['textlinks'][] = [
                    'label' => $label,
                    'url' => $url,
                    'target' => $target,
                    'platforms' => $platforms,
                    'roles' => $roles,
                    'years' => $years,
                ];
            }
        }
    }

    // Determine the type of links this block has to add as a css class later.
    if (!empty($data['textlinks'])) {
        if (count($data['textlinks']) < 10) {
            $data['linknumber'] = 'fewer-than-ten';
        }
        if (!empty($data['iconlinks'])) {
            $data['linktypes'] = 'types-both';
        } else {
            $data['linktypes'] = 'types-one types-text';
        }
    } else {
        if (!empty($data['iconlinks'])) {
            $data['linktypes'] = 'types-one types-icons';
        }
    }


$links_array = $data;



echo "<pre>";
//var_export($data);
//exit;





// Get current timestamp
$now = time();

// Function to prepare and insert a record
function insert_quicklink($DB, $link_data, $is_iconlink = false, $timestamp) {
    // Prepare the record object
    $record = new stdClass();
    
    // Required fields from the array
    $record->label = $link_data['label'] ?? '';
    $record->url = $link_data['url'] ?? '';
    $record->target = $link_data['target'] ?? '';
    
    // Fields present in updated array structure
    $record->roles = $link_data['roles'] ?? '';
    $record->years = $link_data['years'] ?? '';
    $record->platforms = $link_data['platforms'] ?? '["web"]'; // Default to web if not specified
    
    // Additional required fields
    $record->tags = ''; // Still not present in array, using empty string as default
    $record->timecreated = $timestamp;
    $record->timemodified = $timestamp;
    
    // Handle icon field (only for iconlinks)
    $record->icon = $is_iconlink ? ($link_data['icon'] ?? '') : '';
    
    // Basic validation
    if (empty($record->label) || empty($record->url)) {
        echo("Skipping invalid entry - missing label or url");
        return false;
    }

    try {
        // Check for existing record to avoid duplicates
        //if ($DB->record_exists('block_quicklinks', ['label' => $record->label, 'url' => $record->url])) {
        //    echo("Skipping duplicate entry: {$record->label}");
        //    return false;
        //}

        // Insert the record and return the ID
        return $DB->insert_record('block_quicklinks', $record);
    } catch (dml_exception $e) {
        echo('Error inserting quicklink: ' . $e->getMessage());
        return false;
    }
}

// Wrap in a transaction for data integrity
$transaction = $DB->start_delegated_transaction();

try {
    // Process iconlinks
    if (!empty($links_array['iconlinks'])) {
        foreach ($links_array['iconlinks'] as $iconlink) {
            $result = insert_quicklink($DB, $iconlink, true, $now);
            if ($result) {
                echo("Successfully inserted iconlink: {$iconlink['label']} with ID: $result");
            } else {
                echo("Failed to insert or skipped iconlink: {$iconlink['label']}");
            }
        }
    }

    // Process textlinks
    if (!empty($links_array['textlinks'])) {
        foreach ($links_array['textlinks'] as $textlink) {
            $result = insert_quicklink($DB, $textlink, false, $now);
            if ($result) {
                echo("Successfully inserted textlink: {$textlink['label']} with ID: $result");
            } else {
                echo("Failed to insert or skipped textlink: {$textlink['label']}");
            }
        }
    }

    // Commit the transaction if all inserts are successful
    $transaction->allow_commit();
    echo("All links processed successfully");

} catch (Exception $e) {
    // Roll back the transaction on any error
    $transaction->rollback($e);
    echo("Transaction rolled back due to error: " . $e->getMessage());
}