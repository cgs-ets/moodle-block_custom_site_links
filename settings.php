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
     *
     * @package   block_custom_site_links
     * @copyright Veronica Bermegui
     * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
     */
    defined('MOODLE_INTERNAL') || die();

    if ($ADMIN->fulltree) {

        $settings->add(new admin_setting_heading(
                'block_custom_site_links_settings',
                '',
                get_string('pluginname_settings', 'block_custom_site_links')
        ));

        $settings->add(new admin_setting_configtextarea('block_custom_site_links/rolesset',
            get_string('availableroles','block_custom_site_links'),
            get_string('availablerolesdesc','block_custom_site_links'), get_string('validroles', 'block_custom_site_links')),
            PARAM_RAW);

        $settings->add(new admin_setting_configtext('block_custom_site_links/years',
            get_string('years', 'block_custom_site_links'),
            get_string('years_desc', 'block_custom_site_links'), get_string('validyears','block_custom_site_links')));

        $settings->add(new admin_setting_configtext('block_custom_site_links/patterns',
            get_string('patterns', 'block_custom_site_links'),
            get_string('patterns_desc', 'block_custom_site_links'), get_string('validpatterns','block_custom_site_links')));
    }
