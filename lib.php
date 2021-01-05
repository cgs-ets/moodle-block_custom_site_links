<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.
/**
 * Responsive icon and text links list with visibilty based on user profile fields.
 *
 * @package   block_custom_site_links
 * @copyright Michael Vangelovski, Canberra Grammar School <michael.vangelovski@cgs.act.edu.au>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

require_once($CFG->dirroot . '/user/profile/lib.php');
require_once($CFG->libdir.'/filelib.php');

/**
 * Serves the plugin attachments.
 *
 * @package block_custom_site_links
 * @category files
 * @param stdClass $course course object
 * @param stdClass $birecordorcm course module object
 * @param stdClass $context context object
 * @param string $filearea file area
 * @param array $args extra arguments
 * @param bool $forcedownload whether or not force download
 * @param array $options additional options affecting the file serving
 * @return bool false if file not found, does not return if found - justsend the file
 */
function block_custom_site_links_pluginfile($course, $birecordorcm, $context, $filearea, $args,
    $forcedownload, array $options = array()) {
    global $DB, $CFG;

    if ($context->contextlevel != CONTEXT_BLOCK) {
        send_file_not_found();
    }

    // If block is in course context, then check if user has capability to access course.
    if ($context->get_course_context(false)) {
        require_course_login($course);
    } else if ($CFG->forcelogin) {
        require_login();
    } else {
        // Get parent context and see if user have proper permission.
        $parentcontext = $context->get_parent_context();
        if ($parentcontext->contextlevel === CONTEXT_COURSECAT) {
            // Check if category is visible and user can view this category.
            $category = $DB->get_record('course_categories', array('id' => $parentcontext->instanceid), '*', MUST_EXIST);
            if (!$category->visible) {
                require_capability('moodle/category:viewhiddencategories', $parentcontext);
            }
        }
        // At this point there is no way to check SYSTEM or USER context, so ignoring it.
    }

    if ($filearea !== 'icons') {
        send_file_not_found();
    }

    $fs = get_file_storage();

    $itemid = array_shift($args);
    $filename = array_pop($args);
    $filepath = $args ? '/' . implode('/', $args) . '/' : '/';

    if (!$file = $fs->get_file($context->id, 'block_custom_site_links', 'icons', $itemid, $filepath, $filename)
        or $file->is_directory()) {
        send_file_not_found();
    }

    if ($parentcontext = context::instance_by_id($birecordorcm->parentcontextid, IGNORE_MISSING)) {
        if ($parentcontext->contextlevel == CONTEXT_USER) {
            // Force download on all personal pages including /my/
            // because we do not have reliable way to find out from where this is used.
            $forcedownload = true;
        }
    } else {
        // Weird, there should be parent context, better force dowload then.
        $forcedownload = true;
    }

    \core\session\manager::write_close();

    // Set the caching time for five days.
    send_stored_file($file, 120 * 60 * 60, 0, $forcedownload, $options);
}

function block_custom_site_links_init($instanceid, $platforms = null) {
    global $USER, $DB;

    // Default platform is web.
    if (empty($platforms)) {
        $platforms = array('web');
    }

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

    profile_load_custom_fields($USER);
    // Determing which user role we are rendering to.
    // This block assumes users have custom profile fields for CampusRoles.
    $userroles = array();
    if (isset($USER->profile['CampusRoles'])) {
        $userroles = explode(',', $USER->profile['CampusRoles']);
    }

    // Determing which user year we are rendering to.
    $useryears = array();
    if (!empty($USER->profile['Year'])) {
        $useryears = explode(',', $USER->profile['Year']);
    }

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

            if ( ! array_intersect($platforms, $config->iconlinkplatforms[$i])) {
                continue;
            }

            $allowed = block_custom_site_links_is_allowed($config->iconlinkcampusroles[$i], $userroles, $config->iconlinkyear[$i], $useryears);
            if ($allowed) {
                $icon = isset($iconimages[$i]) ? $iconimages[$i] : '';
                $icontok = isset($iconimagestokenised[$i]) ? $iconimagestokenised[$i] : '';
                $label = isset($config->iconlinklabel[$i]) ? $config->iconlinklabel[$i] : '';
                $target = ( isset($config->iconlinktarget[$i]) && $config->iconlinktarget[$i] ) ? '_blank' : '';
                $data['iconlinks'][] = [
                    'icon' => $icon,
                    'icontok' => $icontok,
                    'label' => $label,
                    'url' => $url,
                    'target' => $target,
                ];
            }
        }
    }

    if (isset($config->textlinkurl)) {

        foreach ($config->textlinkurl as $i => $url) {
            if ($url == '') {
                continue;
            }

            if ( ! array_intersect($platforms, $config->textlinkplatforms[$i])) {
                continue;
            }

            $allowed = block_custom_site_links_is_allowed($config->textlinkcampusroles[$i], $userroles, $config->textlinkyear[$i], $useryears);

            if ($allowed) {
                $icon = isset($iconimages[$i]) ? $iconimages[$i] : '';
                $label = isset($config->textlinklabel[$i]) ? $config->textlinklabel[$i] : '';
                $target = ( isset($config->textlinktarget[$i]) && $config->textlinktarget[$i] ) ? '_blank' : '';
                $data['textlinks'][] = [
                    'label' => $label,
                    'url' => $url,
                    'target' => $target,
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

    return $data;
}

/**
 * Check if the user is allowed to see link.
 *
 * @param string $linkroles
 * @param array $userroles
 * @param string $linkyears
 * @param array $useryear
 * @return boolean
 */
function block_custom_site_links_is_allowed($linkroles, $userroles, $linkyears = null , $useryear = null) {
    if(is_siteadmin()) {
        return true;
    }

    $linkrolesarr = array_map('trim', explode(',', $linkroles));
    $userrolesstr = implode(',', $userroles);
    $isstudent = false;

    if($linkyears != "*" && !empty($linkyears) && !empty($useryear)) {
      $linkyearsarr = array_map('trim', explode(',', $linkyears));
      $useryearsstr = implode(',', $useryear);
      $isstudent = true;
    }
    $allowed =  isset($linkyearsarr) ? array_merge($linkrolesarr,$linkyearsarr) : $linkrolesarr;

    if( !empty($useryearsstr)){
      $str = $userrolesstr .= ',' . $useryearsstr ;
    }else{
       $str = $userrolesstr;
    }

    // Do regex checks.
    foreach ($allowed as $reg) {
        $regex = "/${reg}/i";
        // Role = Student but Year level != to the student's year.
        if ($isstudent) {
           return  in_array($useryearsstr,$linkyearsarr);
        }else if ( ($reg && $reg == "*") || (preg_match($regex, $str) === 1)){
            return true;
        }
    }

    return false;
}
