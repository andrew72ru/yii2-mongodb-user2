<?php
/**
 * Project: nvtmn2
 * File: User.php
 * User: andrew
 * Date: 27.12.16
 * Time: 15:33
 */

namespace andrew72ru\user\models;

use andrew72ru\user\helpers\Password;
use andrew72ru\user\Module;
use andrew72ru\user\traits\ModuleTrait;
use MongoDB\BSON\ObjectID;
use MongoDB\BSON\UTCDateTime;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\mongodb\validators\MongoIdValidator;
use yii\web\BadRequestHttpException;
use yii\web\IdentityInterface;
use Yii;

/**
 * Основной класс для пользователя
 *
 * Class User
 * @package andrew72ru\user\models
 *
 * @property ObjectID       _id
 * @property string         username
 * @property array          profile_data
 * @property UTCDateTime    created_at
 * @property UTCDateTime    updated_at
 * @property UTCDateTime    confirmed_at
 * @property UTCDateTime    blocked_at
 * @property string         email
 * @property string         auth_key
 * @property string         password_hash
 *
 * @property string         fullName
 * @property string|null    avatar
 * @property UserAvatar     avatarModel
 * @property Module         module
 * @property bool           isBlocked
 * @property bool           isAdmin
 */
class User extends \yii\mongodb\ActiveRecord implements IdentityInterface
{
    use ModuleTrait;

    /** @var string Plain password. Used for model validation. */
    public $password;

    /** @var string Default username regexp */
    public static $usernameRegexp = '/^[-a-zA-Z0-9_\.@]+$/';

    /**
     * @return array
     */
    public function attributes()
    {
        return [
            '_id',
            'username',
            'created_at',
            'updated_at',
            'confirmed_at',
            'blocked_at',
            'email',
            'auth_key',
            'password_hash',
            'profile_data',
        ];
    }

    /**
     * @inheritdoc
     * @return array
     */
    public function attributeLabels()
    {
        return [
            '_id' => Yii::t('user', 'ID'),
            'username' => Yii::t('user', 'Username'),
            'created_at' => Yii::t('user', 'Created At'),
            'updated_at' => Yii::t('user', 'Updated At'),
            'confirmed_at' => Yii::t('user', 'Confirmed At'),
            'blocked_at' => Yii::t('user', 'Blocked At'),
            'email' => Yii::t('user', 'Email'),
            'auth_key' => Yii::t('user', 'Authentication Key'),
            'password_hash' => Yii::t('user', 'Password Hash'),
            'profile_data' => Yii::t('user', 'Profile Data'),
        ];
    }

    /**
     * @return array
     */
    public function behaviors()
    {
        return [
            'ts' => [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'updated_at',
                'value' => new UTCDateTime((strtotime('now') * 1000))
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
    }

    /**
     * @inheritdoc
     */
    public function afterFind()
    {
        parent::afterFind();
        if(empty($this->profile_data))
            $this->updateAttributes(['profile_data' => (new Profile())->attributes]);
    }

    /**
     * @param bool $insert
     * @return bool
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert))
        {
            if($insert)
                $this->setAttribute('auth_key', Yii::$app->security->generateRandomString());

            if(!empty($this->password))
                $this->setAttribute('password_hash', Password::hash($this->password));

            return true;
        }

        return false;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['_id', MongoIdValidator::className(), 'forceFormat' => 'object'],
            ['username', 'required'],
            ['username', 'match', 'pattern' => static::$usernameRegexp],
            ['email', 'required'],
            ['email', 'email'],
            [['email', 'username'], 'validateUnique'],
//            ['profile_data', 'validateProfile', 'skipOnEmpty' => true],
        ];
    }

    /**
     * @param $attribute
     */
    public function validateUnique($attribute)
    {
        if(self::find()->where([
            '_id' => ['$ne' => $this->_id],
            $attribute => $this->$attribute,
        ])->exists())
            $this->addError($attribute, Yii::t('user', 'User with {attribute_name} "{attribute_value}" already exist', [
                'attribute_name' => $this->getAttributeLabel($attribute),
                'attribute_value' => $this->$attribute
            ]));
    }

    /**
     * Валидация полей профиля
     *
     * @param $attribute
     */
    public function validateProfile($attribute)
    {
        $profile = new Profile();
        $profileData = [];
        foreach($this->$attribute as $profile_field_name => $value)
        {
            if(empty($value))
                continue;

            if ($profile->hasProperty($profile_field_name))
            {
                $profile->$profile_field_name = $value;
                if(!$profile->validate($profile_field_name))
                    $this->addError($attribute, $profile->getFirstError($profile_field_name));
                else
                    $profileData[$profile_field_name] = $value;
            }
        }
        $this->profile_data = $profileData;
    }

    /**
     * @return bool
     * @throws \Exception
     */
    public function create()
    {
        if(!$this->getIsNewRecord())
            throw new \Exception('Calling "' . __CLASS__ . '::' . __METHOD__ . '" on existing user');

        $this->confirmed_at = new UTCDateTime(strtotime('now') * 1000);
        $this->password = empty($this->password) ? Password::generate(10) : $this->password;

        return $this->save();
    }

    /**
     * @return UserAvatar|null
     * @throws BadRequestHttpException
     */
    public function getAvatarModel()
    {
        $model = UserAvatar::findOne([
            'user_id' => $this->_id,
            'thumbnail_size' => 'default',
        ]);
        if($model === null)
        {
            $model = new UserAvatar(['user_id' => $this->_id]);
            $model->createDefaultAvatar();
            if(!$model->save())
                throw new BadRequestHttpException('Unable to create avatar');
        }

        return $model;
    }

    /**
     * @param int $size
     * @return null|string
     */
    public function getAvatar($size = 300)
    {
        /** @var UserAvatar $model */
        $model = UserAvatar::findOne([
            'user_id' => $this->_id,
            'thumbnail_size' => (string) $size,
        ]);

        if($model === null)
            $model = UserAvatar::findOne([
                'user_id' => $this->_id,
                'thumbnail_size' => 'default'
            ]);

        if($model === null)
        {
            $model = new UserAvatar(['user_id' => $this->_id]);
            $model->createDefaultAvatar();
            if(!$model->save())
            {
                Yii::error($model->getErrors(), 'Unable to save default avatar');
                return null;
            }
        }
        $fileContent = $model->fileContent;

        if((string) $model->thumbnail_size !== (string) $size)
        {
            $resized = UserAvatar::resizeAvatar($size, ['user_id' => $this->_id], $fileContent);
            if(!$resized->save())
            {
                Yii::error($resized->getErrors(), 'Unable to save resized avatar');
                return null;
            }
            return 'data:image/png;base64,' . base64_encode($resized->fileContent);
        }

        return 'data:image/png;base64,' . base64_encode($fileContent);
    }

    /**
     * @return \andrew72ru\user\models\Profile
     */
    public function getProfile()
    {
        return new Profile($this->profile_data);
    }

    /**
     * @return string
     */
    public function getFullName()
    {
        $str = $this->username;
        if($this->getProfile()->first_name)
            $str = $this->getProfile()->first_name;
        if($this->getProfile()->last_name)
            $str .= ' ' . $this->getProfile()->last_name;

        return $str;
    }

    /**
     * @return bool
     */
    public function getIsBlocked()
    {
        return $this->blocked_at != null;
    }

    /**
     * @return bool
     */
    public function block()
    {
        return (bool) $this->updateAttributes([
            'blocked_at' => new UTCDateTime(strtotime('now') * 1000)
        ]);
    }

    /**
     * @return bool
     */
    public function unblock()
    {
        return (bool) $this->updateAttributes([
            'blocked_at' => null
        ]);
    }

    /**
     * @return bool
     */
    public function getIsAdmin()
    {
        return (Yii::$app->getAuthManager() && $this->module->adminPermission ? Yii::$app->user->can($this->module->adminPermission) : false) || in_array($this->username, $this->module->admins);
    }

    /**
     * @return bool
     */
    public function confirm()
    {
        return (bool) $this->updateAttributes([
            'confirmed_at' => new UTCDateTime(strtotime('now') * 1000)
        ]);
    }

    /**
     * @param null|string $password
     * @return bool
     */
    public function resetPassword($password = null)
    {
        return (bool) $this->updateAttributes([
            'password_hash' => ($password === null ? Password::hash(Password::generate(8)) : Password::hash($password))
        ]);
    }

    /**
     * @return bool
     * @todo mailer action
     */
    public function resendPassword()
    {
        return $this->resetPassword();
    }


    /**
     * Finds an identity by the given ID.
     * @param string|integer $id the ID to be looked for
     * @return IdentityInterface the identity object that matches the given ID.
     *                           Null should be returned if such an identity cannot be found
     *                           or the identity is not in an active state (disabled, deleted, etc.)
     */
    public static function findIdentity($id)
    {
        return static::findOne($id);
    }

    /**
     * Finds an identity by the given token.
     * @param mixed $token the token to be looked for
     * @param mixed $type  the type of the token. The value of this parameter depends on the implementation.
     *                     For example, [[\yii\filters\auth\HttpBearerAuth]] will set this parameter to be `yii\filters\auth\HttpBearerAuth`.
     * @return IdentityInterface the identity object that matches the given token.
     *                     Null should be returned if such an identity cannot be found
     *                     or the identity is not in an active state (disabled, deleted, etc.)
     * @throws NotSupportedException
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('Method "' . __CLASS__ . '::' . __METHOD__ . '" is not implemented.');
    }

    /**
     * Returns an ID that can uniquely identify a user identity.
     * @return string|integer an ID that uniquely identifies a user identity.
     */
    public function getId()
    {
        return (string) $this->getAttribute('_id');
    }

    /**
     * Returns a key that can be used to check the validity of a given identity ID.
     *
     * The key should be unique for each individual user, and should be persistent
     * so that it can be used to check the validity of the user identity.
     *
     * The space of such keys should be big enough to defeat potential identity attacks.
     *
     * This is required if [[User::enableAutoLogin]] is enabled.
     * @return string a key that is used to check the validity of a given identity ID.
     * @see validateAuthKey()
     */
    public function getAuthKey()
    {
        return $this->getAttribute('auth_key');
    }

    /**
     * Validates the given auth key.
     *
     * This is required if [[User::enableAutoLogin]] is enabled.
     * @param string $authKey the given auth key
     * @return boolean whether the given auth key is valid.
     * @see getAuthKey()
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAttribute('auth_key') === $authKey;
    }
}
