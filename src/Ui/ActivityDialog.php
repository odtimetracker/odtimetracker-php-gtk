<?php
/**
 * odtimetracker-php-gtk
 *
 * @license Mozilla Public License 2.0 https://www.mozilla.org/MPL/2.0/
 * @author Ondřej Doněk, <ondrejd@gmail.com>
 * @link https://github.com/odTimeTracker/odtimetracker-php-lib
 */

namespace odTimeTracker\Gtk\Ui;

use \odTimeTracker\Model\ActivityEntity;
use \odTimeTracker\Gtk\Ui\CommonDialog;

/**
 * Activity dialog.
 *
 * @author Ondřej Doněk, <ondrejd@gmail.com>
 */
class ActivityDialog extends CommonDialog {
  /**
   * @var ActivityEntity $activity
   */
  protected $activity;

  /**
   * @var array $entry
   */
  private $entry;

  /**
   * @var \GtkDialog $dialog
   */
  private $dialog;

  /**
   * Constructor.
   *
   * @param ActivityEntity|null $activity (Optional.)
   * @return void
   */
  function __construct($activity = null) {
    $this->mode     = ($activity instanceof ActivityEntity) ? self::MODE_EDIT : self::MODE_ADD;
    $this->activity = ($this->mode == self::MODE_ADD) ? new ActivityEntity() : $activity;
    $this->entry    = array();

    $label  = ($this->mode == self::MODE_ADD) ? 'New activity' : 'Edit activity';
    $dialog = new \GtkDialog($label, null, \Gtk::DIALOG_MODAL|\Gtk::DIALOG_NO_SEPARATOR);
    $table  = new \GtkTable();

    $this->setupForm($table);
    $dialog->vbox->pack_start($table);

    $buttons_row = new \GtkHBox();
    
    $button_cancel = new \GtkButton('Cancel');
    $button_cancel->connect('clicked', array($this, 'onCancel'));
    $align_cancel = new \GtkAlignment(0, 0, 0, 0);
    $align_cancel->add($button_cancel);
    $buttons_row->pack_start($align_cancel);
    
    $button_submit = new \GtkButton('Submit');
    $button_submit->connect('clicked', array($this, 'onSubmit'));
    $align_submit = new \GtkAlignment(0, 0, 0, 0);
    $align_submit->add($button_submit);
    $buttons_row->pack_start($align_submit);

    $table->attach($buttons_row, 1, 2, 7, 8, \Gtk::FILL, \Gtk::SHRINK);

    $this->dialog = $dialog;
    $dialog->show_all();
    $dialog->run();
    $dialog->destroy();
  } // end __construct($activity = null)

  /**
   * Retrieve activity.
   * 
   * @return ActivityEntity
   */
  public function getActivity() {
    return $this->activity;
  } // end getActivity()

  /**
   * @param \GtkTable $table
   * @return void
   */
  protected function setupForm($table) {
    // ActivityId
    $label0 = new \GtkLabel('ID:');
    $this->entry['ActivityId'] = new \GtkEntry();
    $align0 = new \GtkAlignment(1, .5, 0, 0);
    $align0->add($label0);
    $table->attach($align0, 0, 1, 0, 1);
    $table->attach($this->entry['ActivityId'], 1, 2, 0, 1);
    $this->entry['ActivityId']->set_text($this->activity->getActivityId());
    $this->entry['ActivityId']->set_property('editable', false);

    // ProjectId
    $label1 = new \GtkLabel('ID:');
    $this->entry['ProjectId'] = new \GtkEntry();
    $align1 = new \GtkAlignment(1, .5, 0, 0);
    $align1->add($label1);
    $table->attach($align1, 0, 1, 1, 2);
    $table->attach($this->entry['ProjectId'], 1, 2, 1, 2);
    $this->entry['ProjectId']->set_text($this->activity->getProjectId());
    $this->entry['ProjectId']->set_property('editable', false);

    // Name
    $label2 = new \GtkLabel('Name:');
    $this->entry['Name'] = new \GtkEntry();
    $align2 = new \GtkAlignment(1, .5, 0, 0);
    $align2->add($label2);
    $table->attach($align2, 0, 1, 2, 3);
    $table->attach($this->entry['Name'], 1, 2, 2, 3);
    $this->entry['Name']->set_text($this->activity->getName());

    // Description
    $label3 = new \GtkLabel('Description:');
    $this->entry['Description'] = new \GtkEntry();
    $align3 = new \GtkAlignment(1, .5, 0, 0);
    $align3->add($label3);
    $table->attach($align3, 0, 1, 3, 4);
    $table->attach($this->entry['Description'], 1, 2, 3, 4);
    $this->entry['Description']->set_text($this->activity->getDescription());

    // Tags
    $label4 = new \GtkLabel('Tags:');
    $this->entry['Tags'] = new \GtkEntry();
    $align4 = new \GtkAlignment(1, .5, 0, 0);
    $align4->add($label4);
    $table->attach($align4, 0, 1, 4, 5);
    $table->attach($this->entry['Tags'], 1, 2, 4, 5);
    $this->entry['Tags']->set_text($this->activity->getTags());

    // Started
    $label5 = new \GtkLabel('Started:');
    $this->entry['Started'] = new \GtkEntry();
    $align5 = new \GtkAlignment(1, .5, 0, 0);
    $align5->add($label5);
    $table->attach($align5, 0, 1, 5, 6);
    $table->attach($this->entry['Started'], 1, 2, 5, 6);
    $this->entry['Started']->set_text($this->activity->getStartedFormatted());
    $this->entry['Started']->set_property('editable', false);

    // Stopped
    $label6 = new \GtkLabel('Stopped:');
    $this->entry['Stopped'] = new \GtkEntry();
    $align6 = new \GtkAlignment(1, .5, 0, 0);
    $align6->add($label6);
    $table->attach($align6, 0, 1, 6, 7);
    $table->attach($this->entry['Stopped'], 1, 2, 6, 7);
    $this->entry['Stopped']->set_text($this->activity->getStoppedFormatted());
    $this->entry['Stopped']->set_property('editable', false);
  } // end setupForm($table)

  /**
   * Cancel dialog.
   * 
   * @param \GtkButton $button
   * @return void
   */
  public function onCancel($button) {
    $this->status = self::STATUS_CANCELLED;
    $this->dialog->destroy();
  } // end onSubmit($button)

  /**
   * Submit dialog.
   * 
   * @param \GtkButton $button
   * @return void
   */
  public function onSubmit($button) {
    $this->status = self::STATUS_SUBMITTED;
    // TODO Set `ProjectId`!
    $this->activity->setName($this->entry['Name']->get_text());
    $this->activity->setDescription($this->entry['Description']->get_text());
    $this->activity->setTags($this->entry['Tags']->get_text());
    // TODO Set `Created`!
    // TODO Set `Stopped`!
    $this->dialog->destroy();
  } // end onSubmit($button)
} // End of ActivityDialog