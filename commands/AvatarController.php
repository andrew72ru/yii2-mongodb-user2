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
     * @param null|string $userId
     * @return integer
     */
    public function actionRefresh($userId = null)
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
            $this->resetAvatar($user);

        return Controller::EXIT_CODE_NORMAL;
    }

    /**
     * @param User $user
     * @return int
     */
    private function resetAvatar(User $user)
    {
        UserAvatar::deleteAll(['user_id' => $user->_id]);
        $model = new UserAvatar();
        $model->user_id = $user->_id;
        $model->createDefaultAvatar();
        if(!$model->save())
        {
            $this->stderr("Unable to create avatar for user {$user->getFullName()}\n:", Console::FG_RED);
            VarDumper::dump($model->errors);
        } else
            $this->stdout("Default avatar for {$user->getFullName()} created\n", Console::FG_GREEN);

        return Controller::EXIT_CODE_NORMAL;
    }
}
