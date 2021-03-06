<?php

/*
 * (c) Kévin Dunglas <dunglas@gmail.com>
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

/**
 * <code>Facebook</code> singleton.
 * 
 * @package kdDoctrineGuardFacebookConnectPlugin
 * @subpackage config
 * @author Kévin Dunglas <dunglas@gmail.com>
 */
class kdDoctrineGuardFacebookConnect {

    /**
     * @var Facebook The <code>Facebook</code> instance 
     */
    protected static $facebook = null;

    /**
     * Returns a Facebook api instance.
     * 
     * @return Facebook The instance
     */
    public static function getFacebook() {
        if (null === self::$facebook) {
            $config = sfConfig::get('app_facebook_' . $_SERVER['SERVER_NAME']);
            self::$facebook = new Facebook($config);
        }

        return self::$facebook;
    }

    /**
     * Updates or creates a sfGuardUser for the logged in Facebook usser
     * 
     * @param array $me 
     * @return sfGuardUser
     */
    public static function updateOrCreateUser(array $me) {
        // Try by Facebook ID
        $sfGuardUser = Doctrine_Core::getTable('sfGuardUser')->findOneByFacebookId($me['id']);
        if (!$sfGuardUser) {
            // Try by email address
            $sfGuardUser = Doctrine_Core::getTable('sfGuardUser')->findOneByEmailAddress($me['email']);
            if (!$sfGuardUser) {
                $sfGuardUser = new sfGuardUser();
                $sfGuardUser->setUsername('Facebook_' . $me['id']);
            }
        }
        
        $sfGuardUser->setFacebookId($me['id']);
        $sfGuardUser->setFacebookLink($me['link']);
        $sfGuardUser->setFirstName($me['first_name']);
        $sfGuardUser->setLastName($me['last_name']);
        if (array_key_exists('verified', $me)) {
            $sfGuardUser->setFacebookVerified($me['verified']);
        }
        if (array_key_exists('location', $me)) {
            $sfGuardUser->setLocation($me['location']['name']);
            $sfGuardUser->setFacebookLocationId($me['location']['id']);
        }
        if (array_key_exists('hometown', $me)) {
            $sfGuardUser->setHometown($me['hometown']['name']);
            $sfGuardUser->setFacebookHometownId($me['hometown']['id']);
        }
        $sfGuardUser->setGender($me['gender']);
        $sfGuardUser->setLocale($me['locale']);
        $sfGuardUser->setTimezone($me['timezone']);
        if (array_key_exists('email', $me)) {
            $sfGuardUser->setEmailAddress($me['email']);
        }
        $sfGuardUser->save();

        return $sfGuardUser;
    }

}
