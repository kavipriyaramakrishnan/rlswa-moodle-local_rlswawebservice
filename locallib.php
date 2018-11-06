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
function local_rlswawebservice_user_exists($username, $firstname, $lastname, $email) {
    global $DB, $CFG;
   
    $userrec = $DB->get_record('user', array('username' => $username));
    if ($userrec) {
        return $userrec;
    } else {
        // Validate user fields.
        if ((!empty($email)) && (!empty($lastname)) && (!empty($firstname))) {
            if ($useremail = $DB->get_record('user', array('email' => $email))) {
                // Duplicate Email.
                echo get_string('error_duplicateemails', 'local_rlswawebservice');
                exit;
            } else {
                // Create user.
                $password = generate_password();
                $temppassword = $password;
                $newuser = new stdClass();
                $newuser->username     = $username;
                $newuser->firstname    = $firstname;
                $newuser->lastname     = $lastname;
                $newuser->email        = $email;
                $newuser->password     = hash_internal_user_password($password);
                $newuser->mnethostid   = $CFG->mnet_localhost_id;
                $newuser->maildisplay  = $CFG->defaultpreference_maildisplay;
                $newuser->mailformat   = $CFG->defaultpreference_mailformat;
                $newuser->maildigest   = $CFG->defaultpreference_maildigest;
                $newuser->lang         = $CFG->lang;
                $newuser->timecreated  = time();
                $newuser->timemodified = $newuser->timecreated;
                $newuser->confirmed    = 1;
                // Insert the user into the database.
                $userid  = $DB->insert_record('user', $newuser);
                $userrec = $DB->get_record('user', array('id' => $userid));

                // Email the user.
                $from = new stdClass();
                $from->firstname = 'RLSWA';
                $from->lastname  = 'Admin';
                $from->email     = 'noreply@elearn.rlswa.com.au';

                $to = new stdClass();
                $to->id        = $userrec->id;
                $to->firstname = $userrec->firstname;
                $to->lastname  = $userrec->lastname;
                $to->email     = $userrec->email;

                $emailsub  = get_string('emailsub', 'local_rlswawebservice');

                $a = new stdClass();
                $a->name     = $userrec->firstname.' '.$userrec->lastname;
                $a->username = $userrec->username;
                $a->password = $temppassword;

                $emailbody = get_string('emailbody', 'local_rlswawebservice', $a);
                 
                $emaildelivery = email_to_user($to, $from, $emailsub, $emailbody);
                return $userrec;
            }
        } else {
            echo get_string('error_invaliduserdetails', 'local_rlswawebservice');
            exit;
        }
    }
}

/**
 * Course validation and user enrolment 
 *
 * @param $courseid id of the course to enrol the user
 * @param $user for whom the request is made
 * @return array/boolean
 */
function local_rlswawebservice_course($courseid, $user) {
    global $DB, $CFG;

    if ($courseid) {
        $course = $DB->get_record('course', array('id' => $courseid));
        if ($course) {
            // Is user enrolled in the course.
            if (is_enrolled(context_course::instance($course->id), $user->id)) {
                redirect($CFG->wwwroot."/course/view.php?id=".$course->id);
            } else {
                // Get the student role id.
                $studentrole = $DB->get_record('role', array('shortname'=>'student'));
                // Get the enrolment instances.
                $instance = new stdClass();
                $instance->id = $DB->get_field('enrol', 'id', array('enrol' => 'manual', 'courseid' => $courseid));
                $instance->courseid = $courseid;
                $instance->enrol = 'manual';
                $timestart = time();
                // Enrol user.
                $plugin = enrol_get_plugin('manual');
                $plugin->enrol_user($instance, $user->id, $studentrole->id, $timestart);
                redirect($CFG->wwwroot."/course/view.php?id=".$course->id);
            }
        } else {
            // Course does not exists.
            echo get_string('error_invalidcourseid', 'local_rlswawebservice');
            exit;
        }
    } else {
        redirect($CFG->wwwroot);
    }
}
