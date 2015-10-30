<?php
/**
 * odtimetracker-php-gtk
 *
 * @license Mozilla Public License 2.0 https://www.mozilla.org/MPL/2.0/
 * @author Ondřej Doněk, <ondrejd@gmail.com>
 * @link https://github.com/odTimeTracker/odtimetracker-php-lib
 */

namespace odTimeTracker\Gtk\Ui;

/**
 * Main window.
 *
 * @author Ondřej Doněk, <ondrejd@gmail.com>
 */
class ConfigurationDialog extends \GtkDialog
{
    /**
     * Constructor.
     *
     * @param boolean $show (Optional.)
     * @return void
     */
    function __construct($show = true)
    {
        parent::__construct();

        $this->setupDialog();
        $this->setupUi();

        if ($show === true) {
            $this->show_all();
        }
    } // end __construct()

    /**
     * @return void
     */
    protected function setupDialog()
    {
        $this->set_title('odTimeTracker - Configuration');
        $this->set_size_request(480, 320);
        //$this->set_resizable(false);
        // Show on all workspaces
        //$this->stick();
        $this->set_border_width(5);

        $this->connect_simple('destroy', array('gtk', 'main_quit'));
    } // end setupDialog()

    /**
     * @return void
     */
    protected function setupUi()
    {
        
    } // end setupUi()
} // End of ConfigurationDialog


$title = new GtkLabel("Set Default Button - Part 2\n".
"using key-press-event");
$title->modify_font(new PangoFontDescription("Times New Roman Italic 10"));
$title->modify_fg(Gtk::STATE_NORMAL, GdkColor::parse("#0000ff"));
$title->set_size_request(-1, 40);
$title->set_justify(Gtk::JUSTIFY_CENTER);
$alignment = new GtkAlignment(0.5, 0, 0, 0);
$alignment->add($title);
$dialog->vbox->pack_start($alignment, 0, 0);
$dialog->vbox->pack_start(new GtkLabel(), 0, 0);

$dialog->add_buttons(array('button 1', 100,
'button 2', 101,
'button 3', 102));

$dialog->connect('key-press-event', 'on_keypress'); // note 1

$dialog->set_has_separator(0);
$dialog->show_all();

$button2 = get_button($dialog, 'button 2'); // note 2
$button2->grab_focus(); // note 3

$response = $dialog->run();

echo "response = $response\n";

function on_keypress($dialog, $event) {
    if ($event->keyval==Gdk::KEY_Return) { // note 4
        foreach(array('button 1', 'button 2', 'button 3')  as $button_label) {
            $button=get_button($dialog, $button_label);
            if ($button->is_focus()) { // note 5
                $button->clicked(); // note 6
            }
        }
        return true;
    } else {
        return false;
    }
}

function get_button($dialog, $button_label) { // note 7
    $vbox_contents = $dialog->vbox->get_children();
    $buttonbox = $vbox_contents[2];
    $buttons = $buttonbox->get_children();
    foreach($buttons as $button) {
        if ($button->get_label() == $button_label) {
            return $button;
        }
    }
}