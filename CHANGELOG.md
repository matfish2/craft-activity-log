# Release Notes for Activity Log

## 1.5.1 - 2023-07-05
### Added
- Add Payload Search (optional field, enable in Settings page)

## 1.5.0 - 2023-06-18
### Fixed
- Do not load natively labeled actions on Actions page

### Improved
- Auto label unlabeled actions on Actions filter and Actions widget

## 1.4.2 - 2023-06-18
### Fixed
- Unicode for column url throws error when title is in Chinese [#9](https://github.com/matfish2/craft-activity-log/issues/9)

## 1.4.1 - 2023-05-18
### Fixed 
- Defer recording until after craft is done initializing [#3](https://github.com/matfish2/craft-activity-log/pull/8). Courtesy of [@sjeng](https://github.com/sjeng) :pray:  

## 1.4.0 - 2023-05-13
### Changed
- Move Actions page from settings to nav bar 
- Do not record any activity logs requests

## 1.3.4 - 2023-05-07
### Fixed
- Action widget: Get anonymous actions

## 1.3.3 - 2023-05-07
### Fixed
- Get CP trigger for Add Widget Redirect URL

## 1.3.2 - 2023-04-27
### Added
- Actions widget (Note: Only labeled actions are displayed)

### Fixed
- "Others" category on Requests per User and Actions widgets

## 1.3.1 - 2023-03-28
### Added
- Requests per user widget

## 1.3.0 - 2023-03-25
### Added
- Add statistics page

## 1.2.01 - 2023-03-17
### Fixed
- Readability: Replace html encoded slash in url with actual slash 

## 1.2.0 - 2023-03-17
### Added 
- Add advanced payload filtering

## 1.1.5 - 2023-03-17
### Fixed
- IP Query: Search for string contains rather than exact match

## 1.1.4 - 2023-03-16
### Fixed
- Do not record requests to plugin

## 1.1.3 - 2022-11-07
### Fixed
- Fix error when saving setting [#3](https://github.com/matfish2/craft-activity-log/issues/3)

## 1.1.2 - 2022-10-10
### Added
- Added `requestFilter` setting [#2](https://github.com/matfish2/craft-activity-log/issues/2)

## 1.1.1 - 2022-10-10
### Fixed
- Fix createdAt null [#1](https://github.com/matfish2/craft-activity-log/issues/1)

## 1.1.0 - 2022-09-17
### Changed 
- Add All Actions to actions search box
- Add Actions Page to settings 

## 1.0.0 - 2022-07-03
###  Changed
- Move actions list to database in preparation for Actions CRUD UI

## 1.0.0-beta.2 - 2022-06-22
### Fixed
- Fix issue with uninstall

## 1.0.0-beta.1 - 2022-06-22
- Initial Release