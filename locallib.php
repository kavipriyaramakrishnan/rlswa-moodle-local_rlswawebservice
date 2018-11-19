<?php
/**
 * RLSWA Web Service - Local library function
 *
 * @package   local_rlswawebservice
 * @copyright Pukunui
 * @author    Priya Ramakrishnan, Pukunui {@link http://pukunui.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Pre-Shared Key for generating the security hash
 */
define('RLSWAWS_PSK', 'i2L&EnEUYUO6U5q');

/**
 * Generate the security hash
 *
 * @param string $timestamp
 * @param string $username
 * @return string  sha1 hash
 */
function local_rlswawebservice_get_hash($timestamp, $username) {
    return md5($timestamp.$username.RLSWAWS_PSK);
}

/**
 * Authenticate a request
 *
 * @param string $hash authentication token passed as part of the request
 * @param string $timestamp when the request is made
 * @param string $username for whom the request is made
 * @return text
 */
function local_rlswawebservice_hash_validation($hash, $timestamp, $username) {
    if (strcmp($hash, local_rlswawebservice_get_hash($timestamp, $username))) {
        echo get_string('error_invalidhash', 'local_rlswawebservice');
        exit;
    }
}

/**
 * Username Validation
 *
 * @param $username for whom the request is made
 * @return array/boolean
 */
function local_rlswawebservice_user_exists($username) {
    global $DB, $CFG;
   
    $userrec = $DB->get_record('user', array('username' => $username));
    if ($userrec) {
        return $userrec;
    } else {
       echo get_string('error_invaliduserdetails', 'local_rlswawebservice');
       exit;
    }
}
