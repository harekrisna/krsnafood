<?php

namespace App\Model;
use Nette;
use Tracy\Debugger;

class Menu extends Table   {
	
	protected $tableName = 'lunch';
	
	private $days = array("monday" => "Po", 
	                      "tuesday" => "Út",
	                      "wednesday" => "St",
	                      "thursday" => "Čt",
	                      "friday" => "Pá");
	
	
	public function insert($date, $nocook_flag = 0)	{
		return $this->getTable()
	            	->insert(array('lunch_date' => $date,
	                	           'nocook' => $nocook_flag));  
	}
	
	public function delete($lunch_id)	{
		return $this->getTable()
	    			->where('id = ?', $lunch_id)
				  	->delete();
	
	}
	
	public function getWeekTitle($offset = 0) {
        if(date("l") == "Sunday")
            $offset--;
       	$from = date("j.n", strtotime("monday this week {$offset} week"));
        $to = date("j.n.Y", strtotime("friday this week {$offset} week"));
        $week_number = number_format(date("W", strtotime("monday this week {$offset} week")));
        
        $week_title = "Týden {$week_number}. ({$from}. - {$to})";
        
        return $week_title;
    }
    
	public function getMenuWeekTitle($offset = 0) {
        if(date("l") == "Sunday")
            $offset--;
       	$from = date("j.", strtotime("monday this week {$offset} week"));
        $to = date("j. n. Y", strtotime("friday this week {$offset} week"));
        
        return "{$from} - {$to}";
    }
    
	public function getAbbrFromDate($date) {
		return strtolower(date("l", strtotime($date)));
	}
	
	public function getWeekDayDate($day_name, $offset = 0) {
		if(date("l") == "Sunday")
			$offset--; // úprava, aby týden začínal pondělkem
		return date("Y-m-d", strtotime("{$day_name} this week {$offset} week"));
	}
	
	public function strtotime($string) {
		$timestamp = strtotime($string);
		if(date("l") == "Sunday")
			$timestamp -= 604800; // úprava, aby týden začínal pondělkem
		
		return $timestamp;
	}
	
	public function getWeekDates($offset = 0) {
	  $dates = array();
	  $days = array("monday" => $this->getWeekDayDate("monday", $offset),
	                "tuesday" => $this->getWeekDayDate("tuesday", $offset),
	                "wednesday" => $this->getWeekDayDate("wednesday", $offset),
	                "thursday" => $this->getWeekDayDate("thursday", $offset),
	                "friday" => $this->getWeekDayDate("friday", $offset));
	
	  foreach($days as $day => $date) {
	      $dates[] = $date;
	  }
	  
	  return $dates;
	}
	
	public function getWeekLunchs($offset = 0, $allergens = true) {
	  $lunch = array();
	  /*
	  $lunch = array('monday - friday' => array('abbr' => 'Po - Pá',
	                                            'date' => DateTime(d-m-Y),
	                                            'id' => '1 - xxx',
	                                            'name' => 'monday - friday',
	                                            'nocook' => (0/1),
	                                            'preparation' => array('1 - 4' => 'subji - halva'),
	                                            'allergens' => array('alergeny')
	                                           )
	                );
	  */
	    foreach($this->days as $day => $abbr) {
	        $lunch[$day]['id'] = "";
	        $lunch[$day]['name'] = $day;
	        $lunch[$day]['abbr'] = $abbr;
	        $lunch[$day]['preparation'] = array();
	        $lunch[$day]['nocook'] = false;
	        $lunch[$day]['disabled'] = false;
	        $lunch[$day]['date'] = $this->getWeekDayDate($day, $offset);
	        for($i = 1; $i <= 4; $i++) {
	             $lunch[$day]['preparation'][$i] = "";
	        }
	        $lunch[$day]['allergens'] = array();
	        $lunch[$day]['limit'] = 50;
	    }
	    
	    
	    $lunchs = $this->findAll()
	                   ->where("lunch_date", $this->getWeekDates($offset));
	                   
	    foreach ($lunchs as $lunch_row) {
	        $day = strtolower(date('l', strtotime($lunch_row->lunch_date)));
	        $lunch[$day]['id'] = $lunch_row->id;
	        $lunch[$day]['date'] = $lunch_row->lunch_date;
	        $lunch[$day]['nocook'] = $lunch_row->nocook;
		    $lunch[$day]['limit'] = 50 - $lunch_row->related("order")->sum('lunch_count');
		    
		    if($lunch[$day]['limit'] <= 0) {
			    $lunch[$day]['limit'] = 0;
			    $lunch[$day]['disabled'] = true;
	        }
	        
	        $lunchPreparations = $lunch_row->related("lunch_preparation")
	                                       ->order('position');
	                           
	        foreach($lunchPreparations as $lunchPreparation) {
		    	$lunch[$day]['preparation'][$lunchPreparation->position] = $lunchPreparation->preparation->title;
				
				if($allergens) {
					$preparationAllergens = $lunchPreparation->preparation->related("preparation_allergen");
														 
					foreach($preparationAllergens as $preparationAllergen) {
						$lunch[$day]['allergens'][$preparationAllergen->allergen->id] = $preparationAllergen->allergen->title;
					}
				}
	        }
	    }
	    return $lunch;
	}
}