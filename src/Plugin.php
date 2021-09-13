<?php

namespace nilsenpaul\bitlyconnect;

use Craft;
use craft\events\RegisterComponentTypesEvent;
use craft\services\Elements;
use craft\web\twig\variables\CraftVariable;
use nilsenpaul\bitlyconnect\behaviors\CraftVariableBehavior;
use nilsenpaul\bitlyconnect\elements\Bitlink;
use nilsenpaul\bitlyconnect\models\Settings;
use nilsenpaul\bitlyconnect\twig\Extension;
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
    }

    protected function createSettingsModel()
    {
        return new Settings();
    }

    protected function settingsHtml()
    {
        return \Craft::$app->getView()->renderTemplate(
            'bitly-connect/settings',
            ['settings' => $this->getSettings()]
        );
    }
}
