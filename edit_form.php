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
 * Custom Site Links block configuration form definition
 *
 * @package   block_custom_site_links
 * @copyright Michael Vangelovski, Canberra Grammar School <michael.vangelovski@cgs.act.edu.au>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

require_once($CFG->dirroot.'/lib/formslib.php');

define('DEFAULT_NUMBER_ICON_LINKS', 1);
define('DEFAULT_NUMBER_TEXT_LINKS', 1);

/**
 * Edit form class
 *
 * @package   block_custom_site_links
 * @copyright 2019 Michael Vangelovski, Canberra Grammar School <michael.vangelovski@cgs.act.edu.au>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_custom_site_links_edit_form extends block_edit_form {

    /**
     * Form definition
     *
     * @param \moodleform $mform
     * @return void
     */
    protected function specific_definition($mform) {

        /***********************
        * ICON LINKS
        ************************/
        $mform->addElement('header', 'configheader', get_string('iconlinksheader', 'block_custom_site_links'));
        $mform->addElement('static', 'rolesdesc', '', get_string('roles_desc', 'block_custom_site_links'));


        $type = 'advcheckbox';
        $name = 'config_sorticonlinksalpha';
        $label = get_string('sorticonsalpha', 'block_custom_site_links');
        $desc = get_string('sorticonsalpha_desc', 'block_custom_site_links');
        $options = array();
        $values = array(0, 1);
        $mform->addElement($type, $name, $label, $desc, $options, $values);
        $mform->setDefault('config_sorticonlinksalpha', 1); // On by default.

        $repeatarray = array();
        $type = 'hidden';
        $name = 'config_iconlinkid';
        $value = get_string('arrayno', 'block_custom_site_links');
        $repeatarray[] = &$mform->createElement($type, $name, $value);

        $type = 'filemanager';
        $name = 'config_iconlinkimage';
        $label = get_string('icon', 'block_custom_site_links');
        $options = array('subdirs' => 0, 'maxbytes' => 5000000, 'maxfiles' => 1, 'accepted_types' => array('image'));
        $repeatarray[] = &$mform->createElement($type, $name, $label, null, $options);

        $type = 'text';
        $name = 'config_iconlinklabel';
        $label = get_string('label', 'block_custom_site_links');
        $options = array('size' => '50');
        $repeatarray[] = &$mform->createElement($type, $name, $label, $options);

        $type = 'text';
        $name = 'config_iconlinkurl';
        $label = get_string('url');
        $options = array('size' => '50');
        $repeatarray[] = &$mform->createElement($type, $name, $label, $options);

        $type = 'advcheckbox';
        $name = 'config_iconlinktarget';
        $label = get_string('target', 'block_custom_site_links');
        $desc = get_string('targetdesc', 'block_custom_site_links');
        $options = array();
        $values = array(0, 1);
        $repeatarray[] = &$mform->createElement($type, $name, $label, $desc, $options, $values);

        $type = 'text';
        $name = 'config_iconlinkcampusroles';
        $label = get_string('roles', 'block_custom_site_links');
        $options = array('size' => '50');
        $repeatarray[] = &$mform->createElement($type, $name, $label, $options);
        
        $type = 'text';
        $name = 'config_iconlinkyear';
        $label = get_string('years', 'block_custom_site_links');
        $options = array('size' => '50');
        $repeatarray[] = &$mform->createElement($type, $name, $label, $options);

        $type = 'text';
        $name = 'config_iconlinkorder';
        $label = get_string('order');
        $options = array('size' => '10');
        $repeatarray[] = &$mform->createElement($type, $name, $label, $options);

        $type = 'advcheckbox';
        $name = 'config_iconlinkdelete';
        $label = get_string('delete');
        $desc = get_string('deletedesc', 'block_custom_site_links');
        $options = array();
        $value = array(0, 1);
        $repeatarray[] = &$mform->createElement($type, $name, $label, $desc, $options, $values);

        $type = 'html';
        $value = '<br/><br/>';
        $repeatarray[] = &$mform->createElement($type, $value); // Spacer.

        $repeatcount = DEFAULT_NUMBER_ICON_LINKS;
        if ( isset($this->block->config->iconlinkid) ) {
            $countlinks = count($this->block->config->iconlinkid);
            if ( $countlinks > 0 ) {
                $repeatcount = $countlinks;
            }
        }

        $repeatoptions = array();

        $repeatoptions['config_iconlinkid']['type']     = PARAM_INT;
        $repeatoptions['config_iconlinklabel']['type']  = PARAM_RAW;
        $repeatoptions['config_iconlinkurl']['type']    = PARAM_RAW;
        $repeatoptions['config_iconlinktarget']['type'] = PARAM_INT;
        $repeatoptions['config_iconlinkcampusroles']['type']  = PARAM_TEXT;
        $repeatoptions['config_iconlinkyear']['type']  = PARAM_TEXT;
        $repeatoptions['config_iconlinkorder']['type']  = PARAM_INT;
        $repeatoptions['config_iconlinkdelete']['type'] = PARAM_INT;

        $repeatoptions['config_iconlinkorder']['default']  = get_string('arrayno', 'block_custom_site_links');

        $repeatoptions['config_iconlinkimage']['rule']  = array(get_string('required'), 'required', null, 'server');
        $repeatoptions['config_iconlinklabel']['rule']  = array(get_string('required'), 'required', null, 'server');
        $repeatoptions['config_iconlinkurl']['rule']    = array(get_string('required'), 'required', null, 'server');
        $repeatoptions['config_iconlinkcampusroles']['rule']  = array(get_string('required'), 'required', null, 'server');
        //$repeatoptions['config_iconlinkyear']['rule']  = array(get_string('required'), 'required', null, 'server');
        $repeatoptions['config_iconlinkorder']['rule']  = array(get_string('numeric', 'block_custom_site_links'),
            'numeric', null, 'client');

        $repeatoptions['config_iconlinkimage']['disabledif']   = array('config_iconlinkdelete', 'checked');
        $repeatoptions['config_iconlinklabel']['disabledif']   = array('config_iconlinkdelete', 'checked');
        $repeatoptions['config_iconlinkurl']['disabledif']     = array('config_iconlinkdelete', 'checked');
        $repeatoptions['config_iconlinktarget']['disabledif']  = array('config_iconlinkdelete', 'checked');
        $repeatoptions['config_iconlinkcampusroles']['disabledif']   = array('config_iconlinkdelete', 'checked');
        $repeatoptions['config_iconlinkyear']['disabledif']   = array('config_iconlinkdelete', 'checked');
        $repeatoptions['config_iconlinkorder']['disabledif']   = array('config_iconlinkdelete', 'checked');

        $repeatoptions['config_iconlinkorder']['hideif']   = array('config_sorticonlinksalpha', 'checked');

        $this->repeat_elements($repeatarray, $repeatcount, $repeatoptions, 'iconlink_repeats', 'iconlink_add_fields',
            1, get_string('addnewiconlink', 'block_custom_site_links'), true);

        /***********************
        * TEXT LINKS
        ************************/
        $mform->addElement('header', 'configheader', get_string('textlinksheader', 'block_custom_site_links'));

        $type = 'advcheckbox';
        $name = 'config_sorttextlinksalpha';
        $label = get_string('sorttextalpha', 'block_custom_site_links');
        $desc = get_string('sorttextalpha_desc', 'block_custom_site_links');
        $options = array();
        $values = array(0, 1);
        $mform->addElement($type, $name, $label, $desc, $options, $values);
        $mform->setDefault('config_sorttextlinksalpha', 1); // On by default.

        $repeatarray = array();
        $type = 'hidden';
        $name = 'config_textlinkid';
        $value = get_string('arrayno', 'block_custom_site_links');
        $repeatarray[] = &$mform->createElement($type, $name, $value);

        $type = 'text';
        $name = 'config_textlinklabel';
        $label = get_string('label', 'block_custom_site_links');
        $options = array('size' => '50');
        $repeatarray[] = &$mform->createElement($type, $name, $label, $options);

        $type = 'text';
        $name = 'config_textlinkurl';
        $label = get_string('url');
        $options = array('size' => '50');
        $repeatarray[] = &$mform->createElement($type, $name, $label, $options);

        $type = 'advcheckbox';
        $name = 'config_textlinktarget';
        $label = get_string('target', 'block_custom_site_links');
        $desc = get_string('targetdesc', 'block_custom_site_links');
        $options = array();
        $values = array(0, 1);
        $repeatarray[] = &$mform->createElement($type, $name, $label, $desc, $options, $values);

        $type = 'text';
        $name = 'config_textlinkcampusroles';
        $label = get_string('roles', 'block_custom_site_links');
        $options = array('size' => '50');
        $repeatarray[] = &$mform->createElement($type, $name, $label, $options);
        
        $type = 'text';
        $name = 'config_textlinkyear';
        $label = get_string('years', 'block_custom_site_links');
        $options = array('size' => '50');
        $repeatarray[] = &$mform->createElement($type, $name, $label, $options);

        $type = 'text';
        $name = 'config_textlinkorder';
        $label = get_string('order');
        $options = array('size' => '10');
        $repeatarray[] = &$mform->createElement($type, $name, $label, $options);

        $type = 'advcheckbox';
        $name = 'config_textlinkdelete';
        $label = get_string('delete');
        $desc = get_string('deletedesc', 'block_custom_site_links');
        $options = array();
        $value = array(0, 1);
        $repeatarray[] = &$mform->createElement($type, $name, $label, $desc, $options, $values);

        $type = 'html';
        $value = '<br/><br/>';
        $repeatarray[] = &$mform->createElement($type, $value); // Spacer.

        $repeatcount = DEFAULT_NUMBER_TEXT_LINKS;
        if ( isset($this->block->config->textlinkid) ) {
            $countlinks = count($this->block->config->textlinkid);
            if ( $countlinks > 0 ) {
                $repeatcount = $countlinks;
            }
        }

        $repeatoptions = array();

        $repeatoptions['config_textlinkid']['type']     = PARAM_INT;
        $repeatoptions['config_textlinklabel']['type']  = PARAM_RAW;
        $repeatoptions['config_textlinkurl']['type']    = PARAM_RAW;
        $repeatoptions['config_textlinktarget']['type'] = PARAM_INT;
        $repeatoptions['config_textlinkcampusroles']['type']  = PARAM_TEXT;
        $repeatoptions['config_textlinkyear']['type']  = PARAM_TEXT;
        $repeatoptions['config_textlinkorder']['type']  = PARAM_INT;
        $repeatoptions['config_textlinkdelete']['type'] = PARAM_INT;

        $repeatoptions['config_textlinkorder']['default']  = get_string('arrayno', 'block_custom_site_links');

        $repeatoptions['config_textlinklabel']['rule']  = array(get_string('required'), 'required', null, 'server');
        $repeatoptions['config_textlinkurl']['rule']    = array(get_string('required'), 'required', null, 'server');
        $repeatoptions['config_textlinkcampusroles']['rule']  = array(get_string('required'), 'required', null, 'server');
        //$repeatoptions['config_textlinkyear']['rule']  = array(get_string('required'), 'required', null, 'server');
        $repeatoptions['config_textlinkorder']['rule']  = array(get_string('numeric', 'block_custom_site_links'),
            'numeric', null, 'client');

        $repeatoptions['config_textlinklabel']['disabledif']    = array('config_textlinkdelete', 'checked');
        $repeatoptions['config_textlinkurl']['disabledif']      = array('config_textlinkdelete', 'checked');
        $repeatoptions['config_textlinktarget']['disabledif']   = array('config_textlinkdelete', 'checked');
        $repeatoptions['config_textlinkcampusroles']['disabledif']  = array('config_textlinkdelete', 'checked');
        $repeatoptions['config_textlinkyear']['disabledif']  = array('config_textlinkyear', 'checked');
        $repeatoptions['config_textlinkorder']['disabledif']   = array('config_textlinkdelete', 'checked');

        $repeatoptions['config_textlinkorder']['hideif']   = array('config_sorttextlinksalpha', 'checked');

        $this->repeat_elements($repeatarray, $repeatcount, $repeatoptions, 'textlink_repeats', 'textlink_add_fields',
            1, get_string('addnewtextlink', 'block_custom_site_links'), true);
    }

    /**
     * Return submitted data.
     *
     * @return object submitted data.
     */
    public function get_data() {
        $data = parent::get_data();

        if ($data) {
            // Remove deleted icon links before saving data.
            if ( !empty($data->config_iconlinkdelete) ) {
                foreach ($data->config_iconlinkdelete as $i => $del) {
                    if ($del) {
                        $this->delete_array_element($data->config_iconlinkid, $i);
                        $this->delete_array_element($data->config_iconlinkimage, $i);
                        $this->delete_array_element($data->config_iconlinklabel, $i);
                        $this->delete_array_element($data->config_iconlinkurl, $i);
                        $this->delete_array_element($data->config_iconlinktarget, $i);
                        $this->delete_array_element($data->config_iconlinkcampusroles, $i);
                        $this->delete_array_element($data->config_iconlinkorder, $i);
                    }
                }
                // Dont need delete array anymore.
                $data->config_iconlinkdelete = array();

                // Reindex arrays.
                $data->config_iconlinkid = array_values($data->config_iconlinkid);
                $data->config_iconlinkimage = array_values($data->config_iconlinkimage);
                $data->config_iconlinklabel = array_values($data->config_iconlinklabel);
                $data->config_iconlinkurl = array_values($data->config_iconlinkurl);
                $data->config_iconlinktarget = array_values($data->config_iconlinktarget);
                $data->config_iconlinkcampusroles = array_values($data->config_iconlinkcampusroles);
                $data->config_iconlinkorder = array_values($data->config_iconlinkorder);
            }

            // Remove deleted text links before saving data.
            if ( !empty($data->config_textlinkdelete) ) {
                foreach ($data->config_textlinkdelete as $i => $del) {
                    if ($del) {
                        $this->delete_array_element($data->config_textlinkid, $i);
                        $this->delete_array_element($data->config_textlinklabel, $i);
                        $this->delete_array_element($data->config_textlinkurl, $i);
                        $this->delete_array_element($data->config_textlinktarget, $i);
                        $this->delete_array_element($data->config_textlinkcampusroles, $i);
                        $this->delete_array_element($data->config_textlinkorder, $i);
                    }
                }
                // Dont need delete array anymore.
                $data->config_textlinkdelete = array();

                // Reindex arrays.
                $data->config_textlinkid = array_values($data->config_textlinkid);
                $data->config_textlinklabel = array_values($data->config_textlinklabel);
                $data->config_textlinkurl = array_values($data->config_textlinkurl);
                $data->config_textlinktarget = array_values($data->config_textlinktarget);
                $data->config_textlinkcampusroles = array_values($data->config_textlinkcampusroles);
                $data->config_textlinkorder = array_values($data->config_textlinkorder);
            }

            // Reordering icon links.
            $order = array();
            if ($data->config_sorticonlinksalpha) {
                $order = $data->config_iconlinklabel;
                $this->resequence_array($order);
                $data->config_iconlinkorder = $order;
                $order = array_flip($data->config_iconlinkorder);
            } else {
                $this->resequence_array($data->config_iconlinkorder);
                $order = array_flip($data->config_iconlinkorder);
            }
            if (!empty($order)) {
                $this->reorder_by_array($data->config_iconlinkid, $order);
                $this->reorder_by_array($data->config_iconlinkimage, $order);
                $this->reorder_by_array($data->config_iconlinklabel, $order);
                $this->reorder_by_array($data->config_iconlinkurl, $order);
                $this->reorder_by_array($data->config_iconlinktarget, $order);
                $this->reorder_by_array($data->config_iconlinkcampusroles, $order);
                $this->reorder_by_array($data->config_iconlinkorder, $order);
            }

            // Reordering text links.
            $order = array();
            if ($data->config_sorttextlinksalpha) {
                $order = $data->config_textlinklabel;
                $this->resequence_array($order);
                $data->config_textlinkorder = $order;
                $order = array_flip($data->config_textlinkorder);
            } else {
                $this->resequence_array($data->config_textlinkorder);
                $order = array_flip($data->config_textlinkorder);
            }
            if (!empty($order)) {
                $this->reorder_by_array($data->config_textlinkid, $order);
                $this->reorder_by_array($data->config_textlinklabel, $order);
                $this->reorder_by_array($data->config_textlinkurl, $order);
                $this->reorder_by_array($data->config_textlinktarget, $order);
                $this->reorder_by_array($data->config_textlinkcampusroles, $order);
                $this->reorder_by_array($data->config_textlinkorder, $order);
            }

            // Save images.
            if ( !empty($data->config_iconlinkimage) ) {
                foreach ($data->config_iconlinkimage as $i => $image) {
                    file_save_draft_area_files($image, $this->block->context->id, 'block_custom_site_links',
                        'icons', $i);
                }
            }

        }

        return $data;
    }

    /**
     * Set form data.
     *
     * @param array $defaults
     * @return void
     */
    public function set_data($defaults) {
        if (isset($this->block->config->iconlinkimage)) {
            foreach ($this->block->config->iconlinkimage as $i => $draftitemid) {
                $newdraftitemid = ''; // Empty string force creates a new area and copy existing files into.

                // Fetch the draft file areas. On initial load this is empty and new draft areas are created.
                // On subsequent loads the draft areas are retreived.
                if (isset($_REQUEST['config_iconlinkimage'][$i])) {
                    $newdraftitemid = $_REQUEST['config_iconlinkimage'][$i];
                }

                // Copy all the files from the 'real' area, into the draft areas.
                file_prepare_draft_area($newdraftitemid, $this->block->context->id, 'block_custom_site_links',
                    'icons', $i, array('subdirs' => true));
                $this->block->config->iconlinkimage[$i] = $newdraftitemid;
            }
        }

        // Set form data.
        parent::set_data($defaults);
    }

    /**
     * Remove fields not required if delete link is selected.
     *
     * @return void
     */
    public function definition_after_data() {
        if (!isset($this->_form->_submitValues['config_iconlinkdelete'])) {
            return;
        }
        foreach ($this->_form->_submitValues['config_iconlinkdelete'] as $i => $del) {
            // Remove the rules for the deleted link so that error is not triggered.
            if ($del) {
                unset($this->_form->_rules["config_iconlinklabel[${i}]"]);
                unset($this->_form->_rules["config_iconlinkurl[${i}]"]);
                unset($this->_form->_rules["config_iconlinkcampusroles[${i}]"]);
                unset($this->_form->_rules["config_iconlinkimage[${i}]"]);
            }
        }

        if (!isset($this->_form->_submitValues['config_textlinkdelete'])) {
            return;
        }
        foreach ($this->_form->_submitValues['config_textlinkdelete'] as $i => $del) {
            // Remove the rules for the deleted link so that error is not triggered.
            if ($del) {
                unset($this->_form->_rules["config_textlinklabel[${i}]"]);
                unset($this->_form->_rules["config_textlinkurl[${i}]"]);
                unset($this->_form->_rules["config_textlinkcampusroles[${i}]"]);
            }
        }
    }

    /**
     * Helper to delete array element
     *
     * @param array $array
     * @param mixed $index
     * @return void
     */
    private function delete_array_element(&$array, $index) {
        // Unset element and shuffle everything down.
        if (isset($array[$index])) {
            unset($array[$index]);
        }
        if (empty($array)) {
            $array = array();
        }
    }

    /**
     * Helper to reorder array.
     *
     * @param array $array
     * @param array $order
     * @return void
     */
    private function reorder_by_array(&$array, $order) {
        uksort($array, function($key1, $key2) use ($order) {
            return (array_search($key1, $order) > array_search($key2, $order));
        });
        $array = array_values($array);
    }


    /**
     * Helper to ensure order has unique sequence.
     *
     * @param array $array
     * @return void
     */
    private function resequence_array(&$array) {
        asort($array);
        $i = 1;
        foreach ($array as $key => $val) {
            $array[$key] = $i;
            $i++;
        }
        ksort($array);
        return $array;
    }

}