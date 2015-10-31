<?php
/**
 * odtimetracker-php-gtk
 *
 * @license Mozilla Public License 2.0 https://www.mozilla.org/MPL/2.0/
 * @author Ondřej Doněk, <ondrejd@gmail.com>
 * @link https://github.com/odTimeTracker/odtimetracker-php-lib
 */

namespace odTimeTracker\Gtk\Ui;

use \odTimeTracker\Model\ProjectEntity;

/**
 * Activity dialog.
 *
 * @author Ondřej Doněk, <ondrejd@gmail.com>
 */
class ProjectDialog {
  const MODE_ADD = 'add';
  const MODE_EDIT = 'edit';

  const STATUS_CANCELLED = 'cancel';
  const STATUS_INPROCESS = 'inprocess';
  const STATUS_SUBMITTED = 'submit';

  /**
   * @var string $mode
   */
  protected $mode = self::MODE_ADD;

  /**
   * @var string $status
   */
  protected $status = self::STATUS_INPROCESS;

  /**
   * @var ProjectEntity $project
   */
  protected $project;

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
   * @param ProjectEntity|null $project (Optional.)
   * @return void
   */
  function __construct($project = null) {
    $this->mode    = ($project instanceof ProjectEntity) ? self::MODE_EDIT : self::MODE_ADD;
    $this->project = ($this->mode == self::MODE_ADD) ? new ProjectEntity() : $project;
    $this->entry   = array();

    $label  = ($this->mode == self::MODE_ADD) ? 'New project' : 'Edit project';
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
    
    $row = 4;//count($fields)
    $table->attach($buttons_row, 1, 2, $row, $row + 1, \Gtk::FILL, \Gtk::SHRINK);

    $this->dialog = $dialog;
    $dialog->show_all();
    $dialog->run();
    $dialog->destroy();
  } // end __construct($fields)

  /**
   * Retrieve project.
   * 
   * @return ProjectEntity
   */
  public function getProject() {
    return $this->project;
  } // end getProject()

  /**
   * Retrieve result status.
   * 
   * @return string
   */
  public function getStatus() {
    return $this->status;
  } // end getStatus()

  /**
   * @param \GtkTable $table
   * @return void
   */
  protected function setupForm($table) {
    // ProjectId
    $label0 = new \GtkLabel('ID:');
    $this->entry['ProjectId'] = new \GtkEntry();
    $align0 = new \GtkAlignment(1, .5, 0, 0);
    $align0->add($label0);
    $table->attach($align0, 0, 1, 0, 1);
    $table->attach($this->entry['ProjectId'], 1, 2, 0, 1);
    $this->entry['ProjectId']->set_text($this->project->getProjectId());
    $this->entry['ProjectId']->set_property('editable', false);

    // Name
    $label1 = new \GtkLabel('Name:');
    $this->entry['Name'] = new \GtkEntry();
    $align1 = new \GtkAlignment(1, .5, 0, 0);
    $align1->add($label1);
    $table->attach($align1, 0, 1, 1, 2);
    $table->attach($this->entry['Name'], 1, 2, 1, 2);
    $this->entry['Name']->set_text($this->project->getName());

    // Description
    $label2 = new \GtkLabel('Description:');
    $this->entry['Description'] = new \GtkEntry();
    $align2 = new \GtkAlignment(1, .5, 0, 0);
    $align2->add($label2);
    $table->attach($align2, 0, 1, 2, 3);
    $table->attach($this->entry['Description'], 1, 2, 2, 3);
    $this->entry['Description']->set_text($this->project->getDescription());

    // Created
    $label3 = new \GtkLabel('Created:');
    $this->entry['Created'] = new \GtkEntry();
    $align3 = new \GtkAlignment(1, .5, 0, 0);
    $align3->add($label3);
    $table->attach($align3, 0, 1, 3, 4);
    $table->attach($this->entry['Created'], 1, 2, 3, 4);
    $this->entry['Created']->set_text($this->project->getCreatedFormatted());
    $this->entry['Created']->set_property('editable', false);
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
    $this->project->setName($this->entry['Name']->get_text());
    $this->project->setDescription($this->entry['Description']->get_text());
    $this->dialog->destroy();
  } // end onSubmit($button)
} // End of ProjectDialog