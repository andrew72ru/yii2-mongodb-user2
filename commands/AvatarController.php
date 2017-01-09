<?php
/**
 * Project: yii2-mongodb-user
 * File: AvatarController.php
 * User: andrew
 * Date: 09.01.17
 * Time: 9:52
 */

namespace andrew72ru\user\commands;

use andrew72ru\user\models\User;
use andrew72ru\user\models\UserAvatar;
use Yii;
use yii\console\Controller;
use yii\helpers\Console;
use yii\helpers\VarDumper;

/**
 * Операции с аватарами пользователей
 *
 * Class AvatarController
 * @package commands
 *
 * @method stderr($message, $fg = null, $bg = null)
 * @method stdout($message, $fg = null, $bg = null)
 */
class AvatarController extends Controller
{
    public $color = true;

    /**
     * Сброс пользовательского аватара в дефолтный. С указанием id сбрасывается только аватар указанного пользователя
     *
     * @param null|string   $userId     идентификатор пользователя
     * @param null|string   $letter     буква для аватара
     * @return integer
     */
    public function actionRefresh($userId = null, $letter = null)
    {
        $models = [];
        if($userId !== null)
        {
            $model = User::findOne($userId);
            if($model === null)
            {
                $this->stderr(" Unable to find user with id {$userId}, exiting ", Console::BG_RED, Console::FG_BLACK);
                $this->stdout("\n ", Console::BG_BLACK, Console::FG_BLACK);
                return Controller::EXIT_CODE_ERROR;
            }
            $models = [$model];
        }
        if(empty($models))
            $models = User::find()->all();

        foreach($models as $user)
            $this->resetAvatar($user, ($letter === null ? false : $letter));

        return Controller::EXIT_CODE_NORMAL;
    }

    /**
     * Установка картинки для пользователя
     *
     * @param string    $userId     идентификатор пользователя
     * @param string    $file       путь к файлу картинки
     * @return int
     */
    public function actionSetAvatar($userId, $file)
    {
        if(!is_file($file))
        {
            $this->stderr(" File {$file} does not exists, exiting ", Console::BG_RED, Console::FG_BLACK);
            $this->stdout("\n");
            return Controller::EXIT_CODE_ERROR;
        }
        $user = User::findOne($userId);
        if($user === null)
        {
            $this->stderr("User with id {$userId} not found, exiting", Console::BG_RED, Console::FG_BLACK);
            $this->stdout("\n");
            return Controller::EXIT_CODE_ERROR;
        }
        UserAvatar::deleteAll(['user_id' => $user->_id]);

        $model = new UserAvatar();
        $model->user_id = $user->_id;
        $model->thumbnail_size = 'default';
        $manager = new \Intervention\Image\ImageManager(['driver' => 'imagick']);
        $image = $manager->make($file);
        if(!($image instanceof \Intervention\Image\Image))
        {
            $this->stderr("Unable to create Image from {$file}, exiting", Console::BG_RED, Console::FG_BLACK);
            $this->stdout("\n");
            return Controller::EXIT_CODE_ERROR;
        }
        $model->newFileContent = $image->encode('png');
        if(!$model->save())
        {
            $this->stderr("Unable to save User Avatar", Console::BG_RED, Console::FG_BLACK);
            $this->stdout("\n");
            VarDumper::dump($model->errors);
            return Controller::EXIT_CODE_ERROR;
        }
        $this->stdout("Avatar for user {$userId} from file {$file} successfully set\n", Console::FG_GREEN);

        return Controller::EXIT_CODE_NORMAL;
    }

    /**
     * @param User $user
     * @param bool|string $letter
     * @return int
     */
    private function resetAvatar(User $user, $letter = false)
    {
        UserAvatar::deleteAll(['user_id' => $user->_id]);
        $model = new UserAvatar();
        $model->user_id = $user->_id;
        $model->createDefaultAvatar(300, '#8a8a8a', $letter);
        if(!$model->save())
        {
            $this->stderr("Unable to create avatar for user {$user->getFullName()}\n:", Console::FG_RED);
            VarDumper::dump($model->errors);
        } else
            $this->stdout("Default avatar for {$user->getFullName()} created\n", Console::FG_GREEN);

        return Controller::EXIT_CODE_NORMAL;
    }
}
