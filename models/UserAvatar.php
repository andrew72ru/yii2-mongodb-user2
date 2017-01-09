<?php
/**
 * Project: nvtmn2
 * File: UserAvartar.php
 * User: andrew
 * Date: 27.12.16
 * Time: 15:55
 */

namespace andrew72ru\user\models;

use MongoDB\BSON\ObjectID;
use MongoDB\BSON\UTCDateTime;
use yii\helpers\ArrayHelper;
use yii\mongodb\validators\MongoIdValidator;

/**
 * Аватары пользователей
 *
 * Class UserAvartar
 * @package andrew72ru\user\models
 *
 * @property ObjectID                   $_id
 * @property string                     $filename
 * @property UTCDateTime                $uploadDate
 * @property integer                    $length
 * @property integer                    $chunkSize
 * @property string                     $md5
 * @property \yii\mongodb\file\Download $file
 * @property ObjectID                   $user_id
 * @property string                     $thumbnail_size
 */
class UserAvatar extends \yii\mongodb\file\ActiveRecord
{
    /** @var \Intervention\Image\ImageManager */
    private $manager;

    public function init()
    {
        parent::init();
        $this->manager = self::getManager();
    }
    /**
     * @return array
     */
    public function attributes()
    {
        return ArrayHelper::merge(parent::attributes(), [
            'user_id',
            'thumbnail_size',
        ]);
    }

    /**
     * @return \Intervention\Image\ImageManager
     */
    private static function getManager()
    {
        return new \Intervention\Image\ImageManager([
            'driver' => 'imagick'
        ]);
    }

    /**
     * @return array
     */
    public function rules()
    {
        return [
            ['user_id', MongoIdValidator::className(), 'forceFormat' => 'object'],
            ['thumbnail_size', 'string'],
        ];
    }

    /**
     * @param int    $size
     * @param string $background
     * @return $this
     */
    public function createDefaultAvatar($size = 300, $background = '#8a8a8a')
    {
        $user = User::findOne($this->user_id);
        if($user === null)
            return $this;

        $image = $this->manager->canvas($size, $size, $background);
        $this->filename = 'avatar.png';
        $this->thumbnail_size = 'default';

        $fontFile = \Yii::getAlias('@andrew72ru/user/helpers/Lato-Thin.ttf');
        if(is_file($fontFile))
        {
            $letter = mb_strtoupper(mb_substr($user->getFullName(), 0, 1));
            $image->text($letter, ($size / 2), ($size / 2), function(\Intervention\Image\AbstractFont $font)
            {
                $font->file(\Yii::getAlias('@andrew72ru/user/helpers/Lato-Thin.ttf'));
                $font->size(250);
                $font->align('center');
                $font->valign('middle');
                $font->color('#fff');
            });
        }

        $this->newFileContent = $image->encode('png');
        return $this;
    }

    /**
     * @param int $size
     * @param array $attributes
     * @param mixed $content
     * @return \andrew72ru\user\models\UserAvatar
     */
    public static function resizeAvatar($size = 300, $attributes = [], $content)
    {
        $image = self::getManager()->make($content);
        $image->fit($size);

        $model = new UserAvatar();
        $model->user_id = array_key_exists('user_id', $attributes) ? $attributes['user_id'] : \Yii::$app->user->identity->_id;
        $model->thumbnail_size = (string) $size;

        $model->newFileContent = $image->encode('png');
        return $model;
    }
}
