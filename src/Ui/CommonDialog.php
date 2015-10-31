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
 * Abstract class for add/edit dialogs.
 *
 * @author Ondřej Doněk, <ondrejd@gmail.com>
 */
class CommonDialog {
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
   * Retrieve dialog's mode.
   * 
   * @return string
   */
  public function getMode() {
    return $this->mode;
  } // end getMode()

  /**
   * Retrieve result status.
   * 
   * @return string
   */
  public function getStatus() {
    return $this->status;
  } // end getStatus()
} // End of CommonDialog