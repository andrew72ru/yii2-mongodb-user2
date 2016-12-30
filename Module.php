<?php
/**
 * Project: nvtmn2
 * File: Module.php
 * User: andrew
 * Date: 28.12.16
 * Time: 10:12
 */

namespace andrew72ru\user;


class Module extends \yii\base\Module
{
    const VERSION = '0.0.1';

    /** @var bool Whether to remove password field from registration form. */
    public $enableGeneratingPassword = false;

    /** @var array An array of administrator's usernames. */
    public $admins = [];

    /** @var string The Administrator permission name. */
    public $adminPermission;

    /** @var array Model map */
    public $modelMap = [];

    /** @var int Cost parameter used by the Blowfish hash algorithm. */
    public $cost = 10;

    /** @var int The time you want the user will be remembered without asking for credentials. */
    public $rememberFor = 1209600; // two weeks

    /**
     * @var string The prefix for user module URL.
     *
     * @See [[GroupUrlRule::prefix]]
     */
    public $urlPrefix = 'user';

    /** @var array The rules to be used in URL management. */
    public $urlRules = [
        '<id:\d+>'                               => 'profile/show',
        '<action:(login|logout)>'                => 'security/<action>',
        '<action:(register|resend)>'             => 'registration/<action>',
        'confirm/<id:\d+>/<code:[A-Za-z0-9_-]+>' => 'registration/confirm',
        'forgot'                                 => 'recovery/request',
        'recover/<id:\d+>/<code:[A-Za-z0-9_-]+>' => 'recovery/reset',
        'settings/<action:\w+>'                  => 'settings/<action>'
    ];
}
