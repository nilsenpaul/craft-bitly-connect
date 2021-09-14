# Bitly Connect plugin for Craft CMS 3.x

This plugin lets you connect your Craft CMS to Bitly, to create shortened URLs directly from your Twig templates. All you need is a Bitly access token, and off you go!

## License

This plugin requires a commercial license which can be purchased through the [Craft Plugin Store](https://plugins.craftcms.com/bitly-connect).  
The license fee is $29 plus $9 per subsequent year for updates (optional).

## Requirements

This plugin requires Craft CMS 3.1.0 or later.

**Installation**

1. Install with Composer via `composer require nilsenpaul/bitly-connect` from your project directory
2. Install plugin in the Craft Control Panel under Settings > Plugins
3. [Get a Bitly access token](https://app.bitly.com/settings/api/) and enter this into the plugin's settings.

You can also install Bitly Connect via the **Plugin Store** in the Craft Control Panel.

## Usage

After you've installed the plugin, you can create a new shortened URL (or "Bitlink"), by using the `bitlink` filter:

    {{ 'https://nilsenpaul.nl' |bitlink }}
    
Optionally, you can define a custom domain (you'd have to create one on Bitly's side first) and/or group:

    {{ 'https://nilsenpaul.nl' |bitlink('np.link', 'o_u4j1oih') }}

## Bitlinks widget

The plugin comes with a widget, showing you the created bitlinks and the click count for each of them. Click counts are cached between 900 and 1800 seconds, to prevent the plugin from running in to rate limits.
