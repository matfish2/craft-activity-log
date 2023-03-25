## Craft Activity Log
![craft4 test_adminos_activity-log_site=default (1)](https://user-images.githubusercontent.com/1510460/175233839-30d3f4ac-a3a3-4511-9c1d-b388e4225bf5.png)

This plugin provides a detailed activity log for incoming web requests.

### Requirements

This plugin requires Craft CMS 4.x or later.

### Installation

1. Include the package:

```
composer require matfish/craft-activity-log
```

2. Install the plugin:

```
php craft plugin/install activity-logs
```

### Usage

Once the plugin is installed Craft will start recording all requests, excluding Control Panel AJAX requests (except for Login request).
Data points include:
* URL
* Action (if it is an action request)
* User
* Site
* Query
* Payload 
* IP
* User agent
* Method (GET,POST,PUT or DELETE)
* Is CP (Control Panel) Request?
* Is AJAX request?
* Response Code
* Execution Time
* Timestamp (Created at)

The user can control which request types to record under the Settings page.

![craft4 test_adminos_settings_plugins_activity-log_site=default](https://user-images.githubusercontent.com/1510460/175233673-87f2f69d-0c45-4b0c-a3d9-7c231026989e.png)

For a more fine-grained control, on top of request type settings, you can use the `requestFilter` setting:
1. In you project create a `config/activity-logs.php` file
2. Define a `requestFilter` callback that returns a boolean. E.g:
```php
<?php
return [
    'requestFilter' => function () {
        return $this->getPathInfo() !== 'activity-logs';
    }
];
```
The `$this` object in this context will be an instance of the request class (`craft\web\Request`).
Only requests satisfying the condition (returning `true`) will be recorded.

The user can also give labels to all recorded actions for ease of search.
![craft4 test_adminos_settings_activity-logs_actions_site=default](https://user-images.githubusercontent.com/1510460/190848960-05dc091f-fe01-4e96-ade1-aba8600b00a9.png)

Requests can be viewed and filtered under the Activity Log page.
Click the "Columns" button to add or remove columns from the table on the fly:

![craft4 test_adminos_activity-logs_site=default](https://user-images.githubusercontent.com/1510460/175236200-2c2ebc1b-b1c6-4dfa-a07a-8d20a3780cb5.png)

Note that most columns have a dedicated filter attached to them (except for date range at the top of the table).

Click the "+" sign on the left-hand side of each row to expand a child row containing the full request data:

![craft4 test_adminos_activity-log_site=default (4)](https://user-images.githubusercontent.com/1510460/175233957-eeb453c1-8b18-448e-af7a-c476f3ac9cb5.png)
 
### Payload Filtering

The plugin automatically replaces the CSRF Token and any payload key which contains the word "password" with a "[filtered]" mask.
You can add additional keys to be filtered in two ways:

a. General: Add it to the `filterPayloadKeys` on the setting file:
```php   
 'filterPayloadKeys'=>[
        'cvv','long_number'
    ]
```
b. Specific: If you only want to filter a certain key from specific requests you can use the `filterPayloadCallbacks` array instead, e.g:
```php
 'filterPayloadCallbacks'=> [
     function(\craft\web\Request $request) {
        if (str_contains($request->getUrl(),'add-credit-card')) {
                return 'cvv';
         }

        // Don't add any key to the list
        return false;
      }
]
```

### Statistics
![craft4 ddev site_admin_activity-logs_stats (1)](https://user-images.githubusercontent.com/1510460/227717790-51cc3998-f496-4ec3-9346-50e2b443dc14.png)
The statistics page provides some insights gleaned from the raw data.
Similar to Craft's dashboard widgets, you can add and remove widgets, as well as change the order and the column span.
The data can be filtered by:
1. Date Range
2. Site Id
3. User Id
4. Is Ajax?
5. Is Cp? (Control Panel Request)

If you have an idea for additional widget(s) please open a new [feature request](https://github.com/matfish2/craft-activity-log/issues/new?assignees=&labels=&template=feature_request.md&title=).

### Pruning Data

You can prune (delete) data before that last X days using the following console command:
```bash
php craft activity-logs/logs/prune --days=30
```
If omitted the `days` option defaults to 30 days.

### License

You can try Activity Log in a development environment for as long as you like. Once your site goes live, you are
required to purchase a license for the plugin. License is purchasable through
the [Craft Plugin Store](https://plugins.craftcms.com/activity-logs).

For more information, see
Craft's [Commercial Plugin Licensing](https://craftcms.com/docs/4.x/plugins.html#commercial-plugin-licensing).
