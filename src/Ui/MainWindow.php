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
    $mainBox = new \GtkVBox();

    $startStopArea = new \GtkHBox();

    $vbox = new \GtkVBox();
    $hbox = new \GtkVBox();

    $label = new \GtkLabel();
    $label->set_markup(
        '<span>Create new activity:</span>'.
        '<span color="darkgray" font="sans">[ACTIVITY_NAME][@PROJECT_NAME][#TAGS]</span>'.
        ''
    );
    $label->set_single_line_mode(false);

    $hbox->pack_start($label, false);
    $vbox->pack_start($hbox, false);

    $textBuffer = new \GtkTextBuffer();
    $textView = new \GtkTextView();
    $textBuffer->set_text('Hello World!');
    $textView->set_buffer($textBuffer);
    $textView->set_editable(true);
    $vbox->pack_start($textView, true);

    $startStopArea->pack_start($vbox, true);
    $mainBox->pack_start($startStopArea, false);

    // ==============================================================
    $notebook = new \GtkNotebook();

    $tab1 = new \GtkVBox();
    //$tab1->pack_start(new \GtkLabel('This is the first tab'));
    new ActivitiesTreeview($tab1);
    $notebook->append_page(
        $tab1,
        new \GtkLabel('Activities')
    );

    // Projects tab
    $tab2 = new \GtkVBox();
    new ProjectsTreeview($tab2);
    $notebook->append_page(
        $tab2,
        new \GtkLabel('Projects')
    );

    //Create the third page, with an icon as label and
    //some nested childs
    $tab3 = new \GtkVBox();
    $tab3->pack_start(new \GtkLabel('This is the third tab'));
    $tab3->pack_start(new \GtkEntry(), false, false);
    $tab3->pack_start(new \GtkButton('Test'), false, false);
    $notebook->append_page(
        $tab3,
        \GtkImage::new_from_stock(
            \Gtk::STOCK_ADD,
            \Gtk::ICON_SIZE_MENU
        )
    );
    $mainBox->pack_start($notebook, true);
    // ==============================================================


    $this->add($mainBox);
  } // end setupUi()
} // End of MainWindow
