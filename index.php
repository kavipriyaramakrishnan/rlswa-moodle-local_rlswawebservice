<?php
/**
 * RLSWA Web Service
 *
 *
 * @package   local_rlswawebservice
 * @copyright Pukunui
 * @author    Priya Ramakrishnan, Pukunui {@link http://pukunui.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once('../../config.php');
require($CFG->dirroot.'/local/rlswawebservice/locallib.php');

// Parameters.
$hash      = required_param('hash', PARAM_RAW);
$timestamp = required_param('timestamp', PARAM_RAW);
$username  = required_param('username', PARAM_RAW);
$courseid  = optional_param('courseid', '', PARAM_INT);

// Timestamp Validation.
$lastrun = get_config('local_rlswawebservice', 'lasttimestamp');
if ($lastrun >= $timestamp) {
    echo get_string('error_invalidtimestamp', 'local_rlswawebservice');
    exit;
}

// Hash Validation.
local_rlswawebservice_hash_validation($hash, $timestamp, $username);

// Save the timestamp in the config.
set_config('lasttimestamp', $timestamp, 'local_rlswawebservice');

// Username Validation.
if ($user = local_rlswawebservice_user_exists($username)) {
    // Log user in.
    complete_user_login($user);
    // Course ID Validation.
    if ($courseid) {
        if ($course = $DB->get_record('course', array('id' => $courseid))) {
            redirect($CFG->wwwroot.'/course/view.php?id='.$courseid);
        } else {
            echo get_string('error_invalidcourseid', 'local_rlswawebservice');
            exit;
        }
    } else {
        redirect($CFG->wwwroot);
    }
}
