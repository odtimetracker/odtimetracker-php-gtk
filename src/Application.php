<?php
/**
 * odtimetracker-php-gtk
 *
 * @license Mozilla Public License 2.0 https://www.mozilla.org/MPL/2.0/
 * @author Ondřej Doněk, <ondrejd@gmail.com>
 * @link https://github.com/odTimeTracker/odtimetracker-php-lib
 */

namespace odTimeTracker\Gtk;

use \odTimeTracker\Gtk\Ui\MainWindow;

/**
 * Main application class.
 *
 * @author Ondřej Doněk, <ondrejd@gmail.com>
 */
class Application extends \GtkWindow {
  /**
   * @const string Filename of the configuration file.
   */
  const CONFIGURATION_FILE = 'conf.ini';

  /**
   * @const string Application's version.
   */
  const VERSION = '0.1.0';

  /**
   * @var Application $instance
   */
  protected static $instance;

  /**
   * @var array $configuration
   */
  protected $config;

  /**
   * @var \PDO $pdo
   */
  protected $pdo;

  /**
   * Retrieve instance of the main application class.
   * 
   * @static
   * @return Application
   */
  public static function getInstance() {
    if (!(self::$instance instanceof self)) {
      self::$instance = new self();
    }

    return self::$instance;
  } // end getInstance()

  /**
   * @return \PDO Returns database connection.
   */
  public function getPdo() {
    return $this->pdo;
  } // end getInstance()

  /**
   * Execute application.
   * 
   * @static
   * @return void
   */
  public static function execute() {
    self::getInstance()
      ->initConfiguration()
      ->initDatabaseConnection()
      ->showMainWindow();
  } // end execute()

  /**
   * Reads the configuration file.
   * 
   * @return Application
   */
  public function initConfiguration() {
    // Check configuration directory and create it if neccessary
    $configdir = $this->getUserHomePath() . '/.odTimeTracker';

    if (!file_exists($configdir)) {
      if (!mkdir('path/to/directory', 0777, true)) {
        die('Exiting - can not create configuration directory!' . PHP_EOL);
      }
    }
    else if (!is_dir($configdir)) {
      die('Exiting - configuration directory is not a directory!' . PHP_EOL);
    }

    // Check configuration file and create it if neccessary
    $configfile = $configdir . '/' . self::CONFIGURATION_FILE;

    if (!file_exists($configfile)) {
      // Create `.odTimeTracker/conf.ini` file with default database connection
      $res = file_put_contents(
          $configfile,
          '; odTimeTracker Configuration File' . PHP_EOL .
          PHP_EOL .
          '[odtimetracker-php-gtk]' . PHP_EOL .
          'db.dsn="sqlite:/home/ondrejd/.odTimeTracker/db.sqlite"' . PHP_EOL .
          'db.username=""' . PHP_EOL .
          'db.password=""' . PHP_EOL .
          PHP_EOL
      );

      if ($res === false) {
        die('Exiting - creating of configuration file failed!' . PHP_EOL);
      }
    }
    else if (!is_file($configfile) || !is_readable($configfile)) {
      die('Exiting - configuration file is not readable!' . PHP_EOL);
    }

    // Read configuration
    $configarr = parse_ini_file($configfile, true);

    if (!array_key_exists('odtimetracker-php-gtk', $configarr)) {
      die('Exiting - configuration file is not valid!' . PHP_EOL);
    }

    $this->config = $configarr['odtimetracker-php-gtk'];

    return $this;
  } // end initConfiguration()

  /**
   * Tries to connect the database.
   * 
   * @return Application
   */
  public function initDatabaseConnection() {
    if (!array_key_exists('db.dsn', $this->config)) {
      die('Exiting - database connection configuration is not valid!' . PHP_EOL);
    }

    try {
      $this->pdo = new \PDO(
        $this->config['db.dsn'],
        array_key_exists('db.username', $this->config) ? $this->config['db.username'] : null,
        array_key_exists('db.password', $this->config) ? $this->config['db.password'] : null
      );
    } catch (\PDOException $e) {
      die('Exiting - database connection failed!' . PHP_EOL);
    }

    return $this;
  } // end initDatabaseConnection()

  /**
   * Shows main application window.
   * 
   * @return void
   */
  public function showMainWindow() {
    echo 'Showing main window...' . PHP_EOL;

    new MainWindow();
    \Gtk::main();
  } // end showMainWindow()

  /**
   * @internal
   * @link https://github.com/drush-ops/drush/blob/master/includes/environment.inc
   * @return string Returns the user's home directory or empty string.
   */
  protected function getUserHomePath() {
      $home = getenv('HOME');

      if (!empty($home)) {
        $home = rtrim($home, '/');
      }
      else {
        $home = input_filter(INPUT_SERVER, 'HOMEDRIVE');
        if (!empty($home)) {
          // home on windows
          $home = $_SERVER['HOMEDRIVE'] . $_SERVER['HOMEPATH'];
          $home = rtrim($home, '\\/');
        }
      }

      return empty($home) ? '' : $home;
  } // end getUserHomePath()

  /**
   * @internal
   * @return boolean Returns TRUE if script is executed through the CLI.
   */
  protected function isCli() {
    $argc = filter_input(INPUT_SERVER, 'argc');
    return (php_sapi_name() == 'cli' || (is_numeric($argc) && $argc > 0));
  } // end isCli()

  /**
   * @internal
   * @link https://github.com/drush-ops/drush/blob/master/includes/environment.inc
   * @return string|null Returns the name of user running our application.
   */
  protected function getUsername() {
    $name = getenv('username'); // Windows
    if ($name !== false && !empty($name)) {
      return $name;
    }

    $name = getenv('user');
    if ($name !== false && !empty($name)) {
      return $name;
    }

    if (function_exists('posix_getpwuid')) {
      $user = posix_getpwuid(posix_geteuid());
      if (array_key_exists('name', $user)) {
        return $user['name'];
      }
    }

    return null;
  } // end getUsername()
} // End of Application

