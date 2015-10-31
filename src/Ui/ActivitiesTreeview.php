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
 * Activities treeview.
 *
 * @author Ondřej Doněk, <ondrejd@gmail.com>
 */
class ActivitiesTreeview {
  /**
   * Cached data (activities) used in the current treeview's model. Keys
   * correspond to IDs of activities.
   * @var array $cache
   */
  protected $cache;

  /**
   * @var \GtkListStore $model
   */
  protected $model;

  /**
   * @var \GtkBox $parent
   */
  protected $parent;

  /**
   * Constructor.
   *
   * @param \GtkBox $parent
   * @return void
   */
  public function __construct($parent) {
    $this->parent = $parent;
    $this->setup();
    $this->update();
  } // end __construct($parent)

  /**
   * Setup treeview.
   *
   * @return $void
   */
  protected function setup() {
    // Set up a scroll window
    $scrolled_win = new \GtkScrolledWindow();
    $scrolled_win->set_policy(\Gtk::POLICY_AUTOMATIC, \Gtk::POLICY_AUTOMATIC);
    $this->parent->pack_start($scrolled_win);

    // Creates the list store
    if (defined('GObject::TYPE_STRING')) {
      $this->model = new \GtkListStore(
        \GObject::TYPE_LONG,
        \GObject::TYPE_STRING,
        \GObject::TYPE_STRING,
        \GObject::TYPE_STRING
      );
    } else {
      $this->model = new \GtkListStore(
        \Gtk::TYPE_LONG,
        \Gtk::TYPE_STRING,
        \Gtk::TYPE_STRING,
        \Gtk::TYPE_STRING
      );
    }

    $field_head = array('ID', 'Projekt', 'Aktivita', 'Doba trvání');
    $field_just = array(0.0, 0.0, 0.0, 0.0);

    // Creates the view to display the list store
    $view = new \GtkTreeView($this->model);
    $scrolled_win->add($view);

    // Creates the columns
    for ($col=0; $col<count($field_head); ++$col) {
      $renderer = new \GtkCellRendererText();
      $renderer->set_property('xalign', $field_just[$col]);
      $column = new \GtkTreeViewColumn($field_head[$col], $renderer, 'text', $col);
      $column->set_alignment($field_just[$col]);
      $column->set_sort_column_id($col);

      // set the header font and color
      $label = new \GtkLabel($field_head[$col]);
      $label->modify_font(new \PangoFontDescription('Arial Bold'));
      $label->modify_fg(\Gtk::STATE_NORMAL, \GdkColor::parse('#0000FF'));
      $column->set_widget($label);
      $label->show();

      // setup self-defined function to display alternate row color
      $column->set_cell_data_func($renderer, array($this, 'formatCol'), $col);
      $view->append_column($column);
    }

    // set-up selection
    $selection = $view->get_selection();
    $selection->connect('changed', array($this, 'onSelect'));

    // set-up tooltip
    $view->set_property('has-tooltip', true);
    $view->connect('query-tooltip', array($this, 'onTooltip'));
  } // end setup()

  /**
   * Updates treeview.
   *
   * @return boolean
   */
  function update() {
    echo "update treeview: ".date('H:i:s')."\n";
    $activities = $this->loadData();

    $this->cache = array();
    $this->model->clear();

    foreach ($activities as $activity) {
      // Update cache
      $this->cache[$activity->getId()] = $activity;
      // Update model
      $this->model->append(array(
        $activity->getId(),
        $activity->getProject()->getName(),
        $activity->getName(),
        $activity->getDurationFormatted()
      ));
    }

    return true;
  } // end update()

  /**
   * Loads data for treeview.
   *
   * @return array
   */
  protected function loadData() {
    $mapper = new \odTimeTracker\Model\ActivityMapper(
      \odTimeTracker\Gtk\Application::getinstance()->getPdo()
    );

    return $mapper->selectAll();
  } // end loadData()

  /**
   * Formats column of our treeview.
   *
   * @param \GtkTreeViewColumn $col
   * @param \GtkCellRendererText $cell
   * @param \GtkListStore $model
   * @param \GtkListStore $iter
   * @param integer $colNum
   * @return void
   */
  function formatCol($col, $cell, $model, $iter, $colNum) {
    $path = $model->get_path($iter);
    $row = $path[0];

    $val = $model->get_value($iter, $colNum);
    $cell->set_property('text', $val);

    $activity_id = $model->get_value($iter, 0);
    $is_running = false;

    if (array_key_exists($row, $this->cache)) {
      $is_running = $this->cache[$activity_id]->isRunning();
    }

    if ($is_running === true) {
      $cell->set_property('font',  'Ubuntu Sans Bold 10');
    } else {
      $cell->set_property('font', 'Ubuntu Sans 10');
    }

    $row_color = ($row % 2 == 1) ? '#dddddd' : '#ffffff';
    $cell->set_property('cell-background', $row_color);
  } // end formatCol($col, $cell, $model, $iter, $colNum)

  /**
   * Called when selection was changed.
   *
   * @param \GtkTreeSelection $selection
   */
  function onSelect($selection) {
    list($model, $iter) = $selection->get_selected();

    if (!($iter instanceof \GtkTreeIter)) {
      return;
    }

    $id = $model->get_value($iter, 0);
    $project = $model->get_value($iter, 1);
    $name = $model->get_value($iter, 2);
    $created = $model->get_value($iter, 3);

    print "You have selected activity #$id $name of [$project] created on $created.\n";
  } // end onSelect($selection)

  /**
   * Handler for `query-tooltip` event.
   *
   * @param \GtkTreeView $widget
   * @param integer $x
   * @param integer $y
   * @param mixed $keyboard_mode
   * @param \GtkTooltip $tooltip
   * @return void
   */
  public function onTooltip($widget, $x, $y, $keyboard_mode, $tooltip) {
    $path = $widget->get_path_at_pos($x, $y);
    if (is_null($path)) {
      return false;
    }

    $col_title = $path[1]->get_title();
    $path2 = $path[0][0] - 1;

    $model = $widget->get_model();
    $iter = $model->get_iter($path[0][0]);
    $activity_id = $model->get_value($iter, 0);
    $activity = $this->cache[$activity_id];

    if ($path2 < 0) {
      return false;
    }

    $html = ''.
      '<b>'.$activity->getName().'</b>'.PHP_EOL.PHP_EOL.
      '<b>Project</b>: '.$activity->getProject()->getName().''.PHP_EOL.
      '<b>Tags</b>: '.$activity->getTags().''.PHP_EOL.
      '<b>Started</b>: '.$activity->getStartedFormatted().''.PHP_EOL.
      '<b>Stopped</b>: '.$activity->getStoppedFormatted().''.PHP_EOL.
      '<b>Duration</b>: '.$activity->getDurationFormatted().'';

    if (!empty($activity->getDescription())) {
      $html .= PHP_EOL.PHP_EOL.strip_tags($activity->getDescription());
    }

    $widget->set_tooltip_cell($tooltip, $path2, $path[1], null);
    $tooltip->set_markup($html);

    return true;
  } // end onTooltip($widget, $x, $y, $keybord_mode, $tooltip)
} // End ActivitiesTreeview
