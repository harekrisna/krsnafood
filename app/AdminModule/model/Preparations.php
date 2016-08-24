<?php

namespace App\AdminModule\Model;
use Nette;
use Nette\Diagnostics\Debugger; 

/**
 * Model starající se o tabulku person  
 */
class Preparations extends Table
{

  protected $tableName = 'preparation';

  public function insert($title, $category_id)	{
  	try {
      	return $this->getTable()
                    ->insert(array('title' => $title,
                               	   'category_id' => $category_id));
    } catch(\PDOException $e) {
        if($e->getCode() == 23000)
            return false;
        else
            throw $e;
    }
 }
 
  public function update($id, $data)  {  	  
        return $this->getTable()
        			->where(array("id" => $id))
        			->update($data);
  } 
 
  public function delete($preparation_id)  {  	  
  
  	  $this->getTable()
	       ->where('id = ?', $preparation_id)
	       ->delete();
  
  }
  
  
}