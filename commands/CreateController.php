<?php
/**
 * Project: nvtmn2
 * File: CreateController.php
 * User: andrew
 * Date: 28.12.16
 * Time: 11:09
 */

namespace andrew72ru\user\commands;

use Yii;
use yii\console\Controller;
use yii\helpers\Console;

/**
 * Создание пользователя из консоли
 *
 * Class CreateController
 * @package andrew72ru\user\commands
 * @method stdout($string, $color = null, $color = null)
 */
class CreateController extends Controller
{
    /**
     * Создает учетную запись пользователя. Если пароль не задан, будет сгенерирован случайный пароль из восьми символов
     *
     * @param string       $email
     * @param string       $username
     * @param null|string  $password
     */
    public function actionIndex($email, $username, $password = null)
    {
        /** @var \andrew72ru\user\Module $module */
        $module = Yii::$app->getModule('user');

        /** @var \yii\mongodb\ActiveRecord|\andrew72ru\user\models\User $user */
        $user = Yii::createObject([
            'class' => $module->modelMap['User'],
            'email'    => $email,
            'username' => $username,
            'password' => $password,
        ]);

        if($user->create())
            $this->stdout(Yii::t('user', 'User has been created') . "!\n", Console::FG_GREEN);
        else
        {
            $this->stdout(Yii::t('user', 'Please fix following errors:') . "\n", Console::FG_RED);
            foreach ($user->getErrors() as $errors)
            {
                foreach($errors as $error)
                    $this->stdout("\t- " . $error . "\n", Console::FG_PURPLE);
            }
        }

    }
}
