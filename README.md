## Craft Activity Log
![activity_logs](https://github.com/matfish2/craft-activity-log/assets/1510460/2c52ed8f-9805-4203-85fe-69b453ced922)

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

![Screenshot 2023-07-05 122540](https://github.com/matfish2/craft-activity-log/assets/1510460/a0eca755-4351-4e52-8c9d-847a8d38f9ca)

### Advanced Request Filtering

For a more fine-grained control, **on top of** request type settings, you can use the `requestFilter` setting:
1. In you project create a `config/activity-logs.php` file
2. Define a `requestFilter` callback that returns a boolean. E.g:
```php
<?php
return [
 'requestFilter' => function () {
        if ($this->isAjax) {
            return $this->isActionRequest && count($this->actionSegments) === 2 && $this->actionSegments[1] === 'save-draft';
        }

        return true;
    }
]
```
The `$this` object in this context will be an instance of the request class (`craft\web\Request`).
Only requests satisfying the condition (returning `true`) will be recorded.

### Action Requests
Actions are automatically labelled using a naming convention. E.g ["fields","save-group"] will become "Fields Save Group".
This is relevant for the "Action" search dropdown on the Logs page and for the Actions widget on the Statistics page.
In addition the user can optionally override this convention by giving explicit labels to recorded actions under the Actions page.

![Screenshot 2023-07-05 123908](https://github.com/matfish2/craft-activity-log/assets/1510460/b977f911-9783-4b77-894a-a0b693d63baa)

### Audit Trail UI
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

### Payload Search
By default, searching in request payload is disabled in order to remove unnecessary clutter from the table controls.
You can enable it in the Settings Page.
![Screenshot 2023-07-05 122958](https://github.com/matfish2/craft-activity-log/assets/1510460/5050e149-0872-464c-bc64-e00ff6586666)

Note that you need to press enter or leave the field for the search to be triggered.

### Statistics
![activity_logs_stats](https://github.com/matfish2/craft-activity-log/assets/1510460/824cf593-157e-4eb4-ba73-4365b75b2be0)
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
