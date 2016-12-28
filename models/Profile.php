<?php
/**
 * Project: nvtmn2
 * File: Profile.php
 * User: andrew
 * Date: 28.12.16
 * Time: 9:15
 */

namespace andrew72ru\user\models;

use Yii;
use yii\base\Model;

/**
 * Профиль пользователя
 *
 * Class Profile
 * @package andrew72ru\user\models
 */
class Profile extends Model
{
    /** @var string $first_name */
    public $first_name;
    /** @var string $last_name */
    public $last_name;

    /**
     * @return array
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['first_name', 'last_name'], 'required'],
            [['first_name', 'last_name'], 'string', 'max' => 255],
        ];
    }

    /**
     * @return array
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'first_name' => Yii::t('user', 'First Name'),
            'last_name' => Yii::t('user', 'Last Name'),
        ];
    }
}
