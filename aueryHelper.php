<?php

class queryHelper{
  private $parameters = array();
  private $parameter_id = 0;
  private $select = '';
  private $table = '';
  private $join = array();
  private $where = array();
  private $insert = array();
  private $update = array();
  private $page = 1;
  private $limit = 10;
  private $pagination = false;
  private $limit_flag = false;
  private $sort = array();
  private $query_type = 'select'; //select, insert, update, delete


  private function addParameter($value){
    $this->parameter_id++;
    $key = 'par_' . $this->parameter_id;
    $this->parameters[$key] = $value;
    return $key;
  }

  public function addParameterManual($key, $value){
    if(!isset($this->parameters[$key])){
      $this->parameters[$key] = $value;
    }
  }

  public function addWhereString($where){
    $tmp = array('AND', $where);
    array_push($this->where, $tmp);
  }

  public function orWhereString($where){
    $tmp = array('OR', $where);
    array_push($this->where, $tmp);
  }

  public function addSearchText($keys, $text){
    $par_name = $this->addParameter($text);
    $search_query = '(';
    $counter = 0;
    foreach ($keys as $item){
        if($counter > 0) $search_query .= ' OR ';
        $search_query .= '(' . $item . ' LIKE CONCAT(\'%\', :' . $par_name . ', \'%\'))';
        $counter++;
    }
    $search_query .= ')';
    $tmp = array('AND', $search_query);
    array_push($this->where, $tmp);
  }

  public function addWhere($db_name, $data, $sign = '='){
    $par_name = $this->addParameter($data);
    if(strtoupper($sign) == 'LIKE'){
      $text = $db_name . ' LIKE CONCAT(\'%\', :' . $par_name . ', \'%\')';
    }else{
      $text = $db_name . ' ' . $sign . ' :' . $par_name;
    }
    $tmp = array('AND', $text);
    array_push($this->where, $tmp);
  }

  public function addOrWhere($db_name, $data, $sign = '='){
    $par_name = $this->addParameter($data);
    if(strtoupper($sign) == 'LIKE'){
      $text = $db_name . ' LIKE CONCAT(\'%\', :' . $par_name . ', \'%\')';
    }else{
      $text = $db_name . ' ' . $sign . ' :' . $par_name;
    }
    $tmp = array('OR', $text);
    array_push($this->where, $tmp);
  }

  public function getParameters(){
    return $this->parameters;
  }

  public function addDelete(){
    $this->query_type = 'delete';
  }

  public function addInsert($field, $data){
    $this->query_type = 'insert';
    $par_name = $this->addParameter($data);
    $tmp = array('column' => $field, 'parameter' => $par_name);
    array_push($this->insert, $tmp);
  }

  public function addUpdate($field, $data){
    $this->query_type = 'update';
    $par_name = $this->addParameter($data);
    $text = $field . ' = :' . $par_name;
    array_push($this->update, $text);
  }

  public function addUpdates($data){
    foreach($data as $key => $value){
      $this->addUpdate($key, $value);
    }
  }

  public function addInserts($data){
    foreach($data as $key => $value){
      $this->addInsert($key, $value);
    }
  }

  public function addField($select, $array_type = false){
    $this->query_type = 'select';
    $select_text = '';
    if($array_type){
      $select_text = '';
      foreach($select as $item){
        $select_text .= $item . ',';
      }
      $select_text = substr($select_text, 0, -1);
    }else{
      $select_text = $select;
    }
    $this->select = $select_text;
  }

  public function addTable($table){
    $this->table = $table;
  }

  public function addLeftJoin($table, $on){
    $tmp = array('LEFT', $table, $on);
    array_push($this->join, $tmp);
  }

  public function addRightJoin($table, $on){
    $tmp = array('RIGHT', $table, $on);
    array_push($this->join, $tmp);
  }

  public function addJoin($table, $on){
    $tmp = array('MID', $table, $on);
    array_push($this->join, $tmp);
  }

  public function getQuery(){
    $query = '';

    if($this->query_type == 'select'){
      if($this->select != ''){
        $query = 'SELECT ' . $this->select . ' FROM ' . $this->table;
      }else{
        $query = 'SELECT * FROM ' . $this->table;
      }
    }

    if($this->query_type == 'update'){
      $query = 'UPDATE ' . $this->table;
    }

    if(count($this->update) > 0){
      $count = 0;
      foreach ($this->update as $item){
        if($count > 0){
          $query .= ', ' . $item;
        }else{
          $query .= ' SET ' . $item;
        }
        $count++;
      }
    }

    if($this->query_type == 'insert'){
      $query = 'INSERT INTO ' . $this->table;
    }

    if($this->query_type == 'delete'){
      $query = 'DELETE FROM ' . $this->table;
    }

    if(count($this->insert) > 0){
      $counter = 0;
      $columns = ' (';
      $parameters = '(';
      foreach ($this->insert as $item){
        $columns .= ($counter == 0 ? '' : ', ') . $item['column'];
        $parameters .= ($counter == 0 ? '' : ', ') . ':' . $item['parameter'];
        $counter++;
      }
      $columns .= ')';
      $parameters .= ')';
      $query .= $columns . ' VALUES ' . $parameters;
    }

    if(count($this->join) > 0){
      foreach ($this->join as $item){
        if(strtoUpper($item[0]) == 'LEFT'){
          $query .= ' LEFT JOIN ' . $item[1] . ' ON ' . $item[2];
        }else if(strtoUpper($item[0]) == 'RIGHT'){
          $query .= ' RIGHT JOIN ' . $item[1] . ' ON ' . $item[2];
        }else{
          $query .= ' JOIN ' . $item[1] . ' ON ' . $item[2];
        }
      }
    }

    $where_count = 0;
    if(count($this->where) > 0){
      foreach ($this->where as $item){
        if($where_count == 0){
          $query .= ' WHERE ' . $item[1];
        }else{
          if($item[0] == 'AND'){
            $query .= ' AND ' . $item[1];
          }else{
            $query .= ' OR ' . $item[1];
          }
        }
        $where_count++;
      }
    }

    if(count($this->sort) > 0){
      if(strtoUpper($this->sort[0][1]) == 'ASC'){
        $direction = 'ASC';
      }else{
        $direction = 'DESC';
      }
      $query .= ' ORDER BY ' . $this->sort[0][0] . ' ' . $direction;
    }

    if($this->pagination == true){
      $query .= ' LIMIT ' . $this->limit;
      $query .= ' OFFSET ' . (int)$this->page * $this->limit;
    }

    if($this->limit_flag == true){
      $query .= ' LIMIT ' . $this->limit;
    }

    return $query;
  }

  public function addSort($sort, $sort_dir = 'asc'){
    $tmp = array($sort, $sort_dir);
    array_push($this->sort, $tmp);
  }

  public function addPagination($page, $pageSize){
    $this->page = $page;
    $this->limit = $pageSize;
    $this->pagination = true;
  }

  public function limit($limit){
    $this->limit_flag = true;
    $this->limit = $limit;
  }
}
