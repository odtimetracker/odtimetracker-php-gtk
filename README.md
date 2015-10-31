# odTimeTracker PHP-GTK

[![License](https://img.shields.io/badge/license-MPL-blue.svg)](https://www.mozilla.org/MPL/2.0/)

> __NOTE__: This application is in early development stage so is not usable yet!

odTimeTracker client application written using [PHP-GTK](http://gtk.php.net/).


## Usage

Currently you need to clone this repository and run it from the console or customized _.desktop_ file.

### Installation

> __NOTE__: __odTimeTracker PHP-GTK__ was tested on __PHP 5.6.4__ with installed __PHP-GTK__ extension (builded from the latest sources).

Open your console and write something like this (the snippet below expects [Composer](https://getcomposer.org/) is installed on your system):

```bash
git clone https://github.com/ondrejd/odtimetracker-php-gtk.git
cd odtimetracker-php-gtk
composer install
```

And that is all. Now you should able to run the application:

```bash
./bin/odtimetracker-php-gtk
```

Or you can update `odTimeTracker PHP-GTK.desktop` file and use it as a short-cut.

### Screenshots

Here is how it looks:

![odTimeTracker PHP-GTK 0.2.0](screenshots/screen-02.png?raw=true "odTimeTracker PHP-GTK 0.2.0")


## Changelog/ToDo

### ~1.0
* [ ] starting/stopping activities
* [ ] insert/remove/update projects
* [ ] add pagination to `\odTimeTracker\Gtk\Ui\ActivitiesTreeview`
* [ ] refresh UI:
  * [ ] remove top panel and replace it by toolbar
  * [ ] enable refreshing dataviews by user request (via some button)
  * [ ] double-clicks on dataviews should open edit form
* [ ] __odTimeTracker PHP-GTK__ should supports these data sources:
  * [ ] SQLite/MySQL databases
  * [ ] remote datasource connected via JSON-RPC (`odtimetracker/http-json-rpc`)

### 0.2.0
* [ ] updated activities treeview:
  * [x] highlight currently running activity
  * [x] added tooltip with activity details
* [x] removed start/stop area

### 0.1.0
* Initial version
* Sources placed on [GitHub](https://github.com/odtimetracker/odtimetracker-php-gtk)
