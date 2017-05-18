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
 * Manager for Subplugins
 *
 * @package tool_cleanupcourses
 * @copyright  2017 Tobias Reischmann WWU
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_cleanupcourses;

defined('MOODLE_INTERNAL') || die();

class step_manager extends subplugin_manager {

    /**
     * Returns a subplugin object.
     * @param int $subpluginid id of the subplugin
     * @return step_subplugin
     */
    public function get_subplugin_by_id($subpluginid) {
        global $DB;
        $record = $DB->get_record('tool_cleanupcourses_step', array('id' => $subpluginid));
        if ($record) {
            $subplugin = step_subplugin::from_record($record);
            return $subplugin;
        } else {
            return null;
        }
    }

    /**
     * Persists a subplugin to the database.
     * @param step_subplugin $subplugin
     */
    private function insert_or_update(step_subplugin &$subplugin) {
        global $DB;
        $transaction = $DB->start_delegated_transaction();
        if ($subplugin->id !== null) {
            $DB->update_record('tool_cleanupcourses_step', $subplugin);
        }
        $record = array(
            'name' => $subplugin->name,
        );
        if (!$DB->record_exists('tool_cleanupcourses_step', $record)) {
            $subplugin->id = $DB->insert_record('tool_cleanupcourses_step', $record);
            $record = $DB->get_record('tool_cleanupcourses_step', array('id' => $subplugin->id));
            $subplugin = step_subplugin::from_record($record);
        }
        $transaction->allow_commit();
    }

    /**
     * Removes a subplugin from the database.
     * @param step_subplugin $subplugin
     */
    private function remove(step_subplugin &$subplugin) {
        global $DB;
        $transaction = $DB->start_delegated_transaction();
        $record = array(
            'name' => $subplugin->name,
        );
        if ($record = $DB->get_record('tool_cleanupcourses_step', $record)) {
            $DB->delete_records('tool_cleanupcourses_step', (array) $record);
            $subplugin = step_subplugin::from_record($record);
        }
        $transaction->allow_commit();
    }

    /**
     * Gets the list of step instances.
     * @return array of step instances.
     */
    public function get_step_instances() {
        global $DB;
        return $DB->get_records('tool_cleanupcourses_step');
    }

}