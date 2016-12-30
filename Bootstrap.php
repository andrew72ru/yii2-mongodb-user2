<?php
/**
 * Project: nvtmn2
 * File: Bootstrap.php
 * User: andrew
 * Date: 28.12.16
 * Time: 10:24
 */

namespace andrew72ru\user;


use Yii;
use yii\base\Application;
use yii\base\BootstrapInterface;
use yii\helpers\ArrayHelper;
use yii\i18n\PhpMessageSource;

class Bootstrap implements BootstrapInterface
{
    /** @var array Model's map */
    private $_modelMap = [
        'User'             => 'andrew72ru\user\models\User',
        'LoginForm'        => 'andrew72ru\user\models\LoginForm',
        'SettingsForm'     => 'andrew72ru\user\models\SettingsForm',
        'UserSearch'       => 'andrew72ru\user\models\UserSearch',
    ];

    /**
     * Bootstrap method to be called during application bootstrap stage.
     * @param Application $app the application currently running
     */
    public function bootstrap($app)
    {
        /** @var \andrew72ru\user\Module $module */
        if($app->hasModule('user') && (($module = $app->getModule('user')) instanceof \andrew72ru\user\Module))
        {
            $this->_modelMap = ArrayHelper::merge($this->_modelMap, $module->modelMap);
            foreach ($this->_modelMap as $name => $definition)
            {
                $class = "andrew72ru\\user\\models\\" . $name;
                Yii::$container->set($class, $definition);
                $modelName = is_array($definition) ? $definition['class'] : $definition;
                $module->modelMap[$name] = $modelName;
            }

            if(Yii::$app instanceof \yii\console\Application)
                $module->controllerNamespace = 'andrew72ru\user\commands';
            else
            {
                Yii::$container->set('\yii\web\User', [
                    'enableAutoLogin' => true,
                    'loginUrl'        => ['/user/security/login'],
                    'identityClass'   => $module->modelMap['User'],
                ]);

                $configUrlRule = [
                    'prefix' => $module->urlPrefix,
                    'rules'  => $module->urlRules,
                ];

                if($module->urlPrefix !== 'user')
                    $configUrlRule['routePrefix'] = 'user';

                $configUrlRule['class'] = 'yii\web\GroupUrlRule';
                $rule = Yii::createObject($configUrlRule);

                $app->urlManager->addRules([$rule], false);
            }

            if (!isset($app->get('i18n')->translations['user*']))
            {
                $app->get('i18n')->translations['user*'] = [
                    'class'    => PhpMessageSource::className(),
                    'basePath' => __DIR__ . '/messages',
                    'sourceLanguage' => 'en-US'
                ];
            }
        }
    }
}
