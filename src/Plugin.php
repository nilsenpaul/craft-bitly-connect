<?php

namespace nilsenpaul\bitlyconnect;

use Craft;
use craft\events\RegisterComponentTypesEvent;
use craft\services\Dashboard;
use craft\services\Elements;
use craft\web\twig\variables\CraftVariable;
use nilsenpaul\bitlyconnect\behaviors\CraftVariableBehavior;
use nilsenpaul\bitlyconnect\elements\Bitlink;
use nilsenpaul\bitlyconnect\helpers\Bitly;
use nilsenpaul\bitlyconnect\models\Settings;
use nilsenpaul\bitlyconnect\twig\Extension;
use nilsenpaul\bitlyconnect\widgets\Bitlinks as BitlinksWidget;
use putyourlightson\logtofile\LogToFile;
use yii\base\Event;

class Plugin extends \craft\base\Plugin
{
    public $hasCpSettings = true;
    public static $instance;

    public function init()
    {
        parent::init();
        self::$instance = $this;

        // Add service(s)
        $this->setComponents([
            'bitlinks' => \nilsenpaul\bitlyconnect\services\Bitlink::class,
        ]);

        // Register Bitlink element
        Event::on(
            Elements::class,
            Elements::EVENT_REGISTER_ELEMENT_TYPES,
            function (RegisterComponentTypesEvent $event) {
                $event->types[] = Bitlink::class;
            }
        );

        // Register Bitlinks variable
        Event::on(CraftVariable::class, CraftVariable::EVENT_INIT, function (Event $e) {
            $variable = $e->sender;

            // Attach a behavior:
            $variable->attachBehaviors([
                CraftVariableBehavior::class,
            ]);
        });

        // Register `bitlink` Twig filter
        $extension = new Extension();
        Craft::$app->view->registerTwigExtension($extension);

        // Register widget
        Event::on(
            Dashboard::class,
            Dashboard::EVENT_REGISTER_WIDGET_TYPES,
            function(RegisterComponentTypesEvent $event) {
                $event->types[] = BitlinksWidget::class;
            }
        );
    }

    protected function createSettingsModel()
    {
        return new Settings();
    }

    protected function settingsHtml()
    {
        $bitly = new Bitly();

        return \Craft::$app->getView()->renderTemplate(
            'bitly-connect/settings',
            [
                'settings' => $this->getSettings(),
                'availableDomains' => $bitly->getDomains(),
                'availableGroups' => $bitly->getGroups(),
            ]
        );
    }

    public static function error(string $message, array $params = [])
    {
        self::log($message, $params, 'error');
    }

    public static function warning(string $message, array $params = [])
    {
        self::log($message, $params, 'warning');
    }

    public static function info(string $message, array $params = [])
    {
        self::log($message, $params, 'info');
    }

    public static function log(string $message, array $params = [], string $type = 'info')
    {
        $message = Craft::t('bitly-connect', $message, $params);

        LogToFile::log($message, 'bitly-connect', $type);
    }
}
