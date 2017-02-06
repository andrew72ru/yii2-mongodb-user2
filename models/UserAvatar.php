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
use yii\web\UploadedFile;

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
    /** @var \yii\web\UploadedFile|null $uploadedFile */
    public $uploadedFile;

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
            ['uploadedFile', 'image', 'mimeTypes' => 'image/*', 'checkExtensionByMimeType' => true, 'extensions' => ['png', 'jpg', 'jpeg']]
        ];
    }

    /**
     * @param int    $size
     * @param string $background
     * @param bool|string $letter
     * @return $this
     */
    public function createDefaultAvatar($size = 300, $background = '#8a8a8a', $letter = false)
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
            if(!$letter)
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

    /**
     * @param int $size
     * @return bool
     */
    public function upload($size = 300)
    {
        $this->uploadedFile = UploadedFile::getInstance($this, 'uploadedFile');
        if($this->uploadedFile instanceof UploadedFile)
        {
            if(!$this->user_id)
                $this->user_id = \Yii::$app->user->identity->_id;

            return $this->uploadToDb($this->uploadedFile->tempName, $size, $this->user_id);
        }

        return false;
    }

    /**
     * @param string $path
     * @param int $size
     * @return bool
     */
    public static function setFromFile($path, $size = 300)
    {
        return (new self())->uploadToDb($path, $size, null, true);
    }

    /**
     * @param string $path path to source file
     * @param int $size with and height of result image
     * @param null|ObjectID $user_id
     * @param bool $remove are the source file will be removed after save
     * @return bool
     */
    private function uploadToDb($path, $size, $user_id = null, $remove = false)
    {
        if($user_id === null)
            $user_id = \Yii::$app->user->identity->_id;

        if(!($user_id instanceof ObjectID))
            throw new \BadMethodCallException('user id must be an Object id');

        $image = $this->manager->make($path);
        if(!($image instanceof \Intervention\Image\Image))
        {
            \Yii::error("Unable to create Image from {$path}", 'User Avatar');
            return false;
        }
        $image->fit($size, $size);
        $this->filename = 'avatar.png';
        $this->thumbnail_size = 'default';
        $this->user_id = $user_id;

        $this->newFileContent = $image->encode('png');
        self::deleteAll(['user_id' => $user_id]);

        if($remove)
        {
            try {
                unlink($path);
            } catch (\Exception $exception) {}
        }

        return $this->save();
    }
}
