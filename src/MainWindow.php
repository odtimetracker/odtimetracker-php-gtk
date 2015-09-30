<?php
namespace odtimetracker\gtk;

/**
 * Main window.
 *
 * @author Ondřej Doněk, <ondrejd@gmail.com>
 */
class MainWindow extends \GtkWindow
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

        $this->setup_window();
        $this->setup_ui();

        if ($show === true) {
            $this->show_all();
        }
    } // end __construct()

    /**
     * @return void
     */
    protected function setup_window()
    {
        $this->set_title('odTimeTracker');
        $this->set_size_request(480, 120);
        $this->set_resizable(false);
        $this->stick();
        $this->set_border_width(5);

        $this->connect_simple('destroy', array('gtk', 'main_quit'));
    } // end setup_window()

    /**
     * @return void
     */
    protected function setup_ui()
    {
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

        $this->add($vbox);
    } // end setup_ui()
} // End of MainWindow
