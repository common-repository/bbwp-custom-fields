<?php
// exit if file is called directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class BBWPListTable{

  private $columns = array();
  private $items = array();
  private $sortable = array();
  public $actions = array();
  public $bulk_actions = array();


/******************************************/
/***** get_columns **********/
/******************************************/
	public function get_columns($columns = array()){

    if(isset($columns) && is_array($columns) && count($columns) >= 1)
      $this->columns = $columns;

	}// get_columns method end here

/******************************************/
/***** prepare_items **********/
/******************************************/
	public function prepare_items($data = array()) {

    if(isset($data) && is_array($data) && count($data) >= 1)
      $this->items = $data;

	}// prepare_items method end here

  /******************************************/
  /***** prepare_items **********/
  /******************************************/
  	public function display() {
      //db($this->$columns);
      if(isset($this->items) && count($this->items) >= 1 && isset($this->columns) && count($this->columns) >= 1){

        $tablenavtop = '<div class="tablenav top">';
        if(count($this->bulk_actions) >= 1){
          $tablenavtop .= '<div class="alignleft actions bulkactions"><select name="bulk_action" id="bulk-action-selector-top"><option value="">Bulk Actions</option>';
          foreach ($this->bulk_actions as $key => $value) {
            $tablenavtop .= '<option value="'.$key.'">'.$value.'</option>';
          }
          $tablenavtop .= '</select><input type="submit" id="doaction" class="button action" value="Apply"></div>';
        }
        $tablenavtop .= '</div>';


        $thead = '<tr><td id="cb" class="manage-column column-cb check-column"><label class="screen-reader-text" for="cb-select-all-1">Select All</label><input id="bb-select-all-checkbox" data-name="fields" type="checkbox"></td>';
        $i = 1;
        foreach($this->columns as $key=>$value){
          $primarycolumn = '';
          if($i == 1)
            $primarycolumn = 'column-primary';

          if(is_array($this->sortable) && in_array($key,$this->sortable))
            $thead .= '<th scope="col" id="'.$key.'" class="sortable asc manage-column column-'.$key.' '.$primarycolumn.'"><a href="#"><span>'.$value.'</span><span class="sorting-indicator"></span></a></th>';
          else
            $thead .= '<th scope="col" id="'.$key.'" class="manage-column column-'.$key.' '.$primarycolumn.'">'.$value.'</th>';
          $i++;
        }
        $thead .= "</tr>";

        $i = 1;
        $tbody = "";
        foreach($this->items as $values){
          if(is_array($values) && count($values) >= 1){
            $tbody .= '<tr>';
            //$tbody .= '<tr class="ui-state-default">';
            $j = 1;
            foreach($values as $key=>$value){

              if(!array_key_exists($key, $this->columns)){
                continue;
              }
              /*if(is_array($value)){ $value = implode(', ', $value); }
              if(!$value){ $value = '&nbsp;'; }*/
              $actions_html = array(
                'delete' => '<span class="delete"><a href="?page='.$_REQUEST['page'].'&action=delete&'.$key."=".$value.'">Delete</a></span>',
                'edit' => '<span class="edit"><a href="?page='.$_REQUEST['page'].'&action=edit&'.$key."=".$value.'"">Edit</a></span>',
              );

              $primarycolumn = '';
              $action = "";
              if($j == 1){
                $primarycolumn = 'column-primary';
                $tbody .= '<th scope="row" class="check-column"><input id="cb-select-'.$value.'" type="checkbox" name="fields[]" value="'.$value.'">
                <div class="locked-indicator"></div>
                </th>';
              }
              if(isset($this->actions) && is_array($this->actions) && count($this->actions) >= 1 && array_key_exists($key,$this->actions)){
                $action .= '<div class="row-actions">';
                foreach($this->actions[$key] as $action_key => $action_value){
                    if($action_key == 'delete')
                      $action .= '<input type="hidden" name="sort_field[]" value="'.$value.'" />';
                    $action .= $actions_html[$action_value]."  | ";
                }
                $action = trim($action, "| ");
                $action .= '</div>';
              }

              $tbody .= '<td class="'.$key.' column-'.$key.' has-row-actions '.$primarycolumn.'" data-colname="'.$key.'">'.$value.$action.'</td>';
              $j++;
            }
            $tbody .= "</tr>";
          }
          $i++;
        }


        echo $tablenavtop.'<table class="wp-list-table widefat fixed striped"><thead>'.$thead.'</thead><tbody class="bytebunch-wp-sortable">'.$tbody.'</tbody><tfoot>'.$thead.'</tfoot></table>';

        }
  	}// prepare_items method end here


/******************************************/
/***** get_sortable_columns **********/
/******************************************/
public function get_sortable_columns($column = false) {
  if(isset($column) && count($column) >= 1){
    $this->sortable = $column;
  }
}

/******************************************/
/***** Edit and dlete button on id column **********/
/******************************************/
/*
function column_ID($item) {

  $actions = array(
            //'edit'      => sprintf('<a href="?page=%s&action=%s&id=%s">Edit</a>',$_REQUEST['page'],'edit',$item['ID']),
            'delete'    => sprintf('<a href="?page=%s&action=%s&id=%s">Delete</a>',$_REQUEST['page'],'delete',$item['ID']),
        );
  return sprintf('%1$s %2$s', $item['ID'], $this->row_actions($actions) );
}*/

}// class Booking_List_Table end here

/*
  $data = array();
  $data[] = array("ID" => "1", "title" => 'its good', "date" => "22");
  $data[] = array("ID" => "2", "title" => 'its very bad', "date" => "ss22");
  $tableColumns = array("ID" => "ID", "title" => "Title", "date" => "Date");
  if(count($user_registered_pages) >= 1){
      $ListTableByteBunch = new ListTableByteBunch();
      $ListTableByteBunch->prepare_items($data, $tableColumns);
      $ListTableByteBunch->display();
  }*/
