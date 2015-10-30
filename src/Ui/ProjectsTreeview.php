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
 * Projects treeview.
 *
 * @author Ondřej Doněk, <ondrejd@gmail.com>
 */
class ProjectsTreeview {
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
        \GObject::TYPE_STRING
      );
    } else {
      $this->model = new \GtkListStore(
        \Gtk::TYPE_LONG,
        \Gtk::TYPE_STRING,
        \Gtk::TYPE_STRING
      );
    }

    $field_head = array('ID', 'Name', 'Created');
    $field_just = array(0.0, 0.0, 1.0);

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

    // setup selection
    $selection = $view->get_selection();
    $selection->connect('changed', array($this, 'onSelect'));
  } // end setup()


  /**
   * Updates treeview.
   *
   * @return boolean
   */
  function update() {
    echo "update treeview: ".date('H:i:s')."\n";
    $projects = $this->loadData();

    $this->model->clear();

    foreach ($projects as $project) {
      $this->model->append(array(
        $project->getId(),
        $project->getName(),
        $project->getCreated()->format('j.n. Y')
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
    $mapper = new \odTimeTracker\Model\ProjectMapper(
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
    $id = $model->get_value($iter, 0);
    $name = $model->get_value($iter, 1);
    $created = $model->get_value($iter, 2);

    print "You have selected $name ($created, #$id)\n";
  } // end onSelect($selection)
} // End ProjectsTreeview
