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

defined('MOODLE_INTERNAL') || die();

/**
 * Block Custom Site Links class definition.
 *
 * @package    block_custom_site_links
 * @copyright  2019 Michael de Raadt
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_custom_site_links extends block_base {

    /**
     * Core function used to initialize the block.
     */
    public function init() {
        $this->title = '';
    }

    /**
     * Core function used to identify if the block has a config page.
     */
    public function has_config() {
        return true;
    }

    /**
     * Controls whether multiple instances of the block are allowed on a page
     *
     * @return bool
     */
    public function instance_allow_multiple() {
        return false;
    }

    /**
     * Controls whether the block is configurable
     *
     * @return bool
     */
    public function instance_allow_config() {
        return true;
    }

    /**
     * Defines where the block can be added
     *
     * @return array
     */
    public function applicable_formats() {
        return array(
            'course-view'    => false,
            'site'           => true,
            'mod'            => false,
            'my'             => true,
        );
    }

    /**
     * Used to generate the content for the block.
     * @return object
     */
    public function get_content() {
        global $USER, $OUTPUT;

        // Determing which user role and campus we are rendering to.
        // This block assumes users have custom profile fields for Campus and ConstitCode.
        $userconstits = [];
        if (isset($USER->profile['ConstitCodes'])) {
            $userconstits = explode(',', $USER->profile['ConstitCodes']);
        }
        $usercampuses = [];
        if (isset($USER->profile['Campus'])) {
            $usercampuses = explode(',', $USER->profile['Campus']);
        }

        // If content has already been generated, don't waste time generating it again.
        if ($this->content !== null) {
            return $this->content;
        }
        $this->content = new stdClass;
        $this->content->text = '';
        $this->content->footer = '';

        $iconimages = array();
        $fs = get_file_storage();
        $files = $fs->get_area_files($this->context->id, 'block_custom_site_links', 'icons');
        foreach ($files as $file) {
            $id = $file->get_contenthash();
            $filename = $file->get_filename();
            if ($filename <> '.') {
                $src = moodle_url::make_pluginfile_url($file->get_contextid(), $file->get_component(), $file->get_filearea(),
                    $file->get_itemid(), $file->get_filepath(), $filename );
                $iconimages[] = $src;
            }
        }

        $data = [
            'instanceid' => $this->instance->id,
            'iconlinks' => array(),
            'textlinks' => array(),
            'linktypes' => '',
        ];
        if (isset($this->config->iconlinkurl)) {
            foreach ($this->config->iconlinkurl as $i => $url) {
                if ($url == '') {
                    continue;
                }
                $roleallowed = array_intersect($userconstits, explode(',', $this->config->iconlinkroles[$i]));
                $campusallowed = array_intersect($usercampuses, explode(',', $this->config->iconlinkcampus[$i]));
                if (($roleallowed && $campusallowed) || is_siteadmin()) {

                    $icon = isset($iconimages[$i]) ? $iconimages[$i] : '';
                    $label = isset($this->config->iconlinklabel[$i]) ? $this->config->iconlinklabel[$i] : '';
                    $target = isset($this->config->iconlinktarget[$i]) ? $this->config->iconlinktarget[$i] : '';

                    $data['iconlinks'][] = [
                        'icon' => $icon,
                        'label' => $label,
                        'url' => $url,
                        'target' => $target,
                    ];
                }
            }
        }

        if (isset($this->config->textlinkurl)) {
            foreach ($this->config->textlinkurl as $i => $url) {
                if ($url == '') {
                    continue;
                }
                $roleallowed = array_intersect($userconstits, explode(',', $this->config->textlinkroles[$i]));
                $campusallowed = array_intersect($usercampuses, explode(',', $this->config->textlinkcampus[$i]));
                if (($roleallowed && $campusallowed) || is_siteadmin()) {

                    $icon = isset($iconimages[$i]) ? $iconimages[$i] : '';
                    $label = isset($this->config->textlinklabel[$i]) ? $this->config->textlinklabel[$i] : '';
                    $target = isset($this->config->textlinktarget[$i]) ? $this->config->textlinktarget[$i] : '';

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

        // Render links if any.
        if ($data['linktypes'] != '') {
            $this->content->text = $OUTPUT->render_from_template('block_custom_site_links/content', $data);
        }

        return $this->content;
    }
}
