<?php
/**
 * Project: yii2-mongodb-user
 * File: LoginForm.php
 * User: andrew
 * Date: 30.12.16
 * Time: 15:05
 */

namespace andrew72ru\user\models;


use andrew72ru\user\helpers\Password;
use yii\base\Model;

class LoginForm extends Model
{
    /** @var string User's email or username */
    public $login;

    /** @var string User's plain password */
    public $password;

    /** @var string Whether to remember the user */
    public $rememberMe = false;

    /** @var \andrew72ru\user\models\User */
    protected $user;

    public function attributeLabels()
    {
        return [
            'login' => \Yii::t('user', 'Login'),
            'password' => \Yii::t('user', 'Password'),
            'rememberMe' => \Yii::t('user', 'Remember Me'),
        ];
    }

    public function rules()
    {
        return [
            [['login', 'password'], 'required'],
            ['login', 'trim'],
            ['password', function($attribute)
            {
                if($this->user === null || Password::validate($this->password, $this->user->password_hash))
                    $this->addError($attribute, \Yii::t('user', 'Invalid login or password'));
            }],
            ['rememberMe', 'boolean'],
        ];
    }

    public function beforeValidate()
    {
        if(parent::beforeValidate())
        {
            /** @var User|\yii\mongodb\ActiveRecord $class */
            $class = $this->getModule()->modelMap['User'];
            $this->user = $class::findOne([
                '$or' => [
                    ['username' => trim($this->login)],
                    ['email' => trim($this->login)],
                ]
            ]);

            return true;
        }
        return false;
    }

    public function login()
    {
        if($this->validate())
        {
            \Yii::$app->getUser()->login($this->user, $this->rememberMe ? $this->getModule()->rememberFor : 0);
            return true;
        }
        return false;
    }

    /**
     * @return \andrew72ru\user\Module
     */
    public function getModule()
    {
        return \Yii::$app->getModule('user');
    }
}
