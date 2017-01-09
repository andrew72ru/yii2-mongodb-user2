<?php
/**
 * Project: yii2-mongodb-user
 * File: SettingsForm.php
 * User: andrew
 * Date: 09.01.17
 * Time: 13:32
 */

namespace andrew72ru\user\models;


use andrew72ru\user\helpers\Password;
use andrew72ru\user\models\User;
use andrew72ru\user\traits\ModuleTrait;
use Yii;
use yii\base\Model;

/**
 * Модель формы редактирования аккаунта
 *
 * Class SettingsForm
 * @package models
 *
 * @property \andrew72ru\user\Module $module
 * @property User $user
 */
class SettingsForm extends Model
{
    use ModuleTrait;

    /** @var string $email */
    public $email;
    /** @var string $username */
    public $username;
    /** @var string $new_password */
    public $new_password;
    /** @var string $current_password */
    public $current_password;
    /** @var User $user */
    private $_user;

    /**
     * @return User|null|\yii\web\IdentityInterface
     */
    public function getUser()
    {
        if($this->_user === null)
            $this->_user = \Yii::$app->user->identity;
        return $this->_user;
    }

    /**
     * @inheritdoc
     * @return array
     */
    public function rules()
    {
        return [
            ['username', 'required'],
            ['username', 'filter', 'filter' => 'trim'],
            ['username', 'string', 'min' => 3, 'max' => 255],
            ['username', 'match', 'pattern' => User::$usernameRegexp],
            ['email', 'required'],
            ['email', 'filter', 'filter' => 'trim'],
            ['email', 'email'],
            [['email', 'username'], 'unique',
                'when' => function ($model, $attribute) { return $this->user->$attribute != $model->$attribute; },
                'targetClass' => $this->module->modelMap['User']],
            ['new_password', 'string', 'max' => 72, 'min' => 6],
            ['current_password', 'required'],
            ['current_password', function($attribute)
            {
                if(!Password::validate($this->$attribute, $this->user->password_hash))
                    $this->addError($attribute, Yii::t('user', 'Current password is not valid'));
            }]
        ];
    }

    /**
     * @inheritdoc
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'username' => Yii::t('user', 'Username'),
            'email' => Yii::t('user', 'Email'),
            'new_password' => Yii::t('user', 'New Password'),
            'current_password' => Yii::t('user', 'Current Password'),
        ];
    }

    /**
     * @return bool
     */
    public function save()
    {
        if($this->validate())
        {
            $this->user->username = $this->username;
            $this->user->password = $this->new_password;
            $this->user->email = $this->email;

            return $this->user->save();
        }

        return false;
    }
}
