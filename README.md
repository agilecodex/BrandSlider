# AgileCodex Brand Slider Extension for Magento 2

## Installation

Never install an extension directly on a live store. Test it first on a development domain.
This release supports the selected Magento edition with PHP 8.5.x.

### Composer installation

```bash
composer require acx/module-brandslider:^3.1.7
php bin/magento module:enable Acx_Backend Acx_BrandSlider
php bin/magento setup:upgrade
php bin/magento cache:flush
```

## Upload BrandSlider extension
Upload all files and folders to the root folder of your Magento installation

## Install Extension
Enable Acx_Backend module
Enable Acx_BrandSlider module

How to enable Magento 2 module follow this link https://www.agilecodex.com/enable-and-disable-magento-2-module/
That’s it! You are good to go with AgileCodex Brand Slider Extension!

## Manage Brand

1. Go to **Agile Codex → Brand Management → Brand List** in the Admin Panel.
2. Click **Add New Brand**.
3. Enter the brand name, logo, alt text, sort order, store scope, and status.
4. Click **Save Brand**.

## Add Widget

1. Go to **Content → Widgets** in the Admin Panel.
2. Click **Add Widget**.
3. Select **Brand Slider Widget**, choose the active theme, and click **Continue**.
4. Under **Storefront Properties**, assign the widget to the required page and container.
5. Configure the slider options and click **Save**.

The slider is intentionally added through a Magento widget rather than a hardcoded
homepage layout, so it can be moved, disabled, or removed from the Admin Panel.

## Demo logos

Demo/sample logos are not part of the production module. If the optional demo package
is installed, its data patch creates sample brands and copies fixture logos into
`pub/media/acx/brand/logo`.
