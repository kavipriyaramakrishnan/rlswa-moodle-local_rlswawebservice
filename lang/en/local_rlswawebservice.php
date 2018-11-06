<?php
/**
 * Language string definition
 *
 * @package   local_rlswawebservice
 * @copyright Pukunui
 * @author    Priya Ramakrishnan, Pukunui {@link http://pukunui.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['emailbody'] = 'Hi {$a->name},
A new account has been created for you at \'RLSWA HA SERVICES\'
and you have been issued with a new temporary password.

Your current login information is now:
   username: {$a->username}
   password: {$a->password}
        (you will have to change your password
         when you login for the first time)

To start using \'RLSWA SERVICES\', login at
   http://rlsswa-moodle.australiasoutheast.cloudapp.azure.com

In most mail programs, this should appear as a blue link
which you can just click on.  If that doesn\'t work, then cut and paste the address into the address
line at the top of your web browser window.

Cheers from the \'RLSWA HA SERVICES\' administrator,

Admin User';
$string['emailsub'] = 'RLSWA SERVICES: New user account';
$string['error_duplicateemails'] = 'Email addresss entered is already used by another user';
$string['error_invalidcourseid'] = 'Invalid Course ID';
$string['error_invalidhash'] = 'The hash value passed is Incorrect!';
$string['error_invalidtimestamp'] = 'The timestamp entered is Invalid!';
$string['error_invaliduserdetails'] = 'The User details are Invalid';
$string['pluginname'] = 'RLSWA Web Service';
