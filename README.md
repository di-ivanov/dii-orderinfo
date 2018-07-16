# Dii_Orderinfo
Magento 1.x extension for sending order info to remote API
*Disclaimer* This is a test extension and I would recommend that you **don't** use it in production environment without proper modifications. Always backup your code and DB.
author: *Dimitar Ivanov*
License: *OSL 3.0*
 
 
##### Table of Contents
[Task](#task)  
[Tested on](#tested-on)
[Install](#install)
[Uninstall](#uninstall)
[Notes](#notes)
[Used Tools](#used-tools)

## Task

> Create a module/extension "Dii_Orderinfo", which will send important order data (if module is enabled) in json format using PUT HTTP method, immediately when order status is changed, to API URL specified in admin configuration, when synced update additional order object attributes.  
>Add additional attributes/columns to Order object, you dont have to show them on order grid, but if you manage to do it its a plus:  
>- api_sync (boolean)  
>- api_sync_time (timestamp)  
>Configuration tab (Dii), section (Orderinfo), group (Configuration) should also be created in admin, with these options:  
>- Enabled (yes/no field)  
>- Mode (dropdown with options: production, staging, development)  
>- API production url (text field) dependable on mode choice, hide if mode is not chosen  
>- API staging url (text field) dependable on mode choice, hide if mode is not chosen  
>- API development url (text field) dependable on mode choice, hide if mode is not chosen  
>
>JSON structure:
```javascript
{  
    "order_id": 123,  
    "order_data": {  
        "order_status" : "pending",  
        "payment_method" : "paypal",  
        "number_of_items" : 5,  
        "sub_total": 100,  
        "discount" : 10,  
        "grand_total" : 90  
    },  
    "customer_data" : {  
        "customer_id" : 55,  
        "firstname": "John",  
        "lastname" : "Lamberts",  
        "address" : "Street 123",  
        "city" : "Sofia",  
        "country" : "Bulgaria",  
        "email" : "test@test.com"  
    }  
} 
```
>when sending json, assume api server always return success.


## Tested on
* Magento 1.9.8.3
* PHP 5.6
* MySQL 5.6.23

## Install
Preferred way is through [modman](https://github.com/colinmollenhour/modman)  
```bash
modman init
modman clone https://github.com/didesignbg/dii-orderinfo.git
```
Of course you are good to go just copying files  by hand and preserving folder structure.
```
\---app
    +---etc
    |   \---modules
    |           Dii_Orderinfo.xml
    |
    +---code
    |   \---community
    |       \---Dii
    |           \---Orderinfo
    |               +---Model
    |               |   |   Observer.php
    |               |   |   Api.php
    |               |   |
    |               |   +---System
    |               |   |   \---Config
    |               |   |       \---Source
    |               |   |               Mode.php
    |               |   |
    |               |   \---Resource
    |               |           Setup.php
    |               |
    |               +---etc
    |               |       config.xml
    |               |       system.xml
    |               |       adminhtml.xml
    |               |
    |               +---sql
    |               |   \---dii_orderinfo_setup
    |               |           install-1.0.0.php
    |               |
    |               \---Helper
    |                       Data.php
    |
    \---design
        \---adminhtml
            \---default
                \---default
                    \---layout
                            dii_orderinfo.xml
```

## Uninstall
If using modman `modman undeploy  dii-orderinfo`.  
Otherwise just delete file structure from install.
As for DB, this should be enough
```sql
-- UNINSTALL FROM DB
DELETE FROM `core_resource` WHERE  `code`='dii_orderinfo_setup';
ALTER TABLE `sales_flat_order` DROP `api_sync`, DROP `api_sync_time`;
ALTER TABLE `sales_flat_order_grid` DROP `api_sync`, DROP `api_sync_time`;
-- If you want to remove settings
DELETE FROM `core_config_data` WHERE  `path` LIKE 'orderinfo_options/configuration/%';
```
## Notes
* If installing on a large DB have in mind that `sales_flat_order` is one of the biggest tables in Magento better run direct SQL queries first `ALTER TABLE ... ADD COLUMN ...` so you can better manage the release process
* If installing on Magento 1.4 and older `sales_flat_order` setup should be changed to eav attribute. This will work on both older and newer versions;
* Sending API request will not interfere with order save, but will slow it down. Some actions might trigger more than one status change, and API will be called on every save. For a production site with heavy usage I would definitely prefer cron based solution that detaches API call from order.
* As a test and general showdown this module doesn't implement a logging mechanism just leaves placeholders for it, I strongly encourage implementing it regarding your usage scenario.

## Used tools
What tools were used in the process:
* Docker to prepare the environment
* Modman to deploy module
* Xdebug 
* https://webhook.site to test requests
* Git (obviously)

