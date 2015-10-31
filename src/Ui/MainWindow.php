<?php
/**
 * odtimetracker-php-gtk
 *
 * @license Mozilla Public License 2.0 https://www.mozilla.org/MPL/2.0/
 * @author Ondřej Doněk, <ondrejd@gmail.com>
 * @link https://github.com/odTimeTracker/odtimetracker-php-lib
 */

namespace odTimeTracker\Gtk\Ui;

use \odTimeTracker\Gtk\Application;

/**
 * Main window.
 *
 * @author Ondřej Doněk, <ondrejd@gmail.com>
 */
class MainWindow extends \GtkWindow {

  /**
   * Constructor.
   *
   * @param boolean $show (Optional.)
   * @return void
   */
  function __construct($show = true) {
    parent::__construct();

    $this->setupWindow();
    $this->setupUi();

    if ($show === true) {
      $this->show_all();
    }
  } // end __construct()

  /**
   * @return void
   */
  protected function setupWindow() {
    $this->set_title('odTimeTracker '.Application::VERSION);
    $this->set_size_request(780, 640);
    //$this->set_resizable(false);
    // Show on all workspaces
    //$this->stick();
    $this->set_border_width(5);

    $this->connect_simple('destroy', array('gtk', 'main_quit'));
  } // end setupWindow()

  /**
   * @return void
   */
  protected function setupUi() {
    $main = new \GtkVBox();

    // Main tabbox
    $notebook = new \GtkNotebook();

    // 1. tab - activities
    $tab1 = new \GtkVBox();
    new ActivitiesTreeview($tab1);
    $notebook->append_page($tab1, new \GtkLabel('Activities'));

    // 2. tab - projects
    $tab2 = new \GtkVBox();
    new ProjectsTreeview($tab2);
    $notebook->append_page($tab2, new \GtkLabel('Projects'));

    $main->pack_start($notebook, true);
    $this->add($main);
  } // end setupUi()
} // End of MainWindow
