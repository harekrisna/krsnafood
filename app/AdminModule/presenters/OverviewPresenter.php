<?php

namespace App\AdminModule\Presenters;
use Nette,
	App\Model;
use Tracy\Debugger;
use Nette\Application\UI\Form;

final class OverviewPresenter extends BasePresenter {        
    /** @persistent int*/
	public $year;

    /** @persistent int*/
	public $month;
	
    /** @persistent int*/
	public $group_by;
	
	/** @persistent string*/
	public $list;
	
	/** @persistent array*/
	public $filter = array();
	
	private $months = array(1 => 'leden', 'únor', 'březen', 'duben', 'květen', 'červen', 'červenec', 'srpen', 'září', 'říjen', 'listopad', 'prosinec');
    
    protected function startup()  {
        parent::startup();
		
        if (!$this->getUser()->isLoggedIn()) {
            $this->redirect('Sign:in');
        }

    }  	
    
    function beforeRender() {		      
		$this->template->year = $this->year;
		$this->template->month = $this->month;
		$this->template->months = $this->months;
		$this->template->month_title = $this->months[$this->month];
		$this->template->group_by = $this->group_by;
		$this->template->menu = array();
		
		foreach($this->months as $month_number => $month_title) {
			$this->template->menu[$this->link('setMonth', $month_number)] = $month_number;
		}
		
        $data_chart = $this->date->dates_month($this->month, $this->year);
        $data_chart = array_fill_keys(array_keys($data_chart), 0); // nastaví všem datům výchozí počet 0

        // data pro graf
		$orders_in_month = $this->order->findAll()
									   ->select("lunch.lunch_date, SUM(lunch_count) AS count")
							     	   ->where("YEAR(lunch.lunch_date) = ? AND MONTH(lunch.lunch_date) = ?", $this->year, $this->month)
							     	   ->group("lunch.lunch_date");
		
		foreach($orders_in_month as $order) {
			$date_index = substr((string) $order->lunch_date, 0, 10);
			$data_chart[$date_index] = $order->count;
		}
        
        $this->template->data_chart = $data_chart;		
		
		if(isset($this->filter['payment_type_id'])) {
			$this['filterForm']['payment_type_id']->setDefaultValue($this->filter['payment_type_id']);
		}
			
		$cash_query = $this->order->findAll()
								  ->select("SUM(lunch_count) AS count")
								  ->where("YEAR(lunch.lunch_date) = ? AND MONTH(lunch.lunch_date) = ?", $this->year, $this->month)
								  ->where("person_name LIKE ? AND address LIKE ?", '%'.$this->filter['person_name'].'%', '%'.$this->filter['address'].'%')
								  ->where("payment_type.type = ?", "hotově");
		
		if(isset($this->filter['payment_type_id'])) {
			$cash_query->where('payment_type_id = ?', $this->filter['payment_type_id']);
		}								  
		
		$this->template->cash = $cash_query->fetch();
											
		$bill_query = $this->order->findAll()
								  ->select("SUM(lunch_count) AS count")
								  ->where("YEAR(lunch.lunch_date) = ? AND MONTH(lunch.lunch_date) = ?", $this->year, $this->month)
								  ->where("person_name LIKE ? AND address LIKE ?", '%'.$this->filter['person_name'].'%', '%'.$this->filter['address'].'%')
								  ->where("payment_type.type = ?", "na účet");
		
		if(isset($this->filter['payment_type_id'])) {
			$bill_query->where('payment_type_id = ?', $this->filter['payment_type_id']);
		}								  
		
		$this->template->bill = $bill_query->fetch();

		$fact_query = $this->order->findAll()
								  ->select("SUM(lunch_count) AS count")
								  ->where("YEAR(lunch.lunch_date) = ? AND MONTH(lunch.lunch_date) = ?", $this->year, $this->month)
								  ->where("person_name LIKE ? AND address LIKE ?", '%'.$this->filter['person_name'].'%', '%'.$this->filter['address'].'%')
								  ->where("payment_type.type = ?", "fakturou");
		
		if(isset($this->filter['payment_type_id'])) {
			$fact_query->where('payment_type_id = ?', $this->filter['payment_type_id']);
		}								  
		
		$this->template->fact = $fact_query->fetch();

		$pouk_query = $this->order->findAll()
								  ->select("SUM(lunch_count) AS count")
								  ->where("YEAR(lunch.lunch_date) = ? AND MONTH(lunch.lunch_date) = ?", $this->year, $this->month)
								  ->where("person_name LIKE ? AND address LIKE ?", '%'.$this->filter['person_name'].'%', '%'.$this->filter['address'].'%')
								  ->where("payment_type.type = ?", "poukázkou");
		
		if(isset($this->filter['payment_type_id'])) {
			$pouk_query->where('payment_type_id = ?', $this->filter['payment_type_id']);
		}								  
		
		$this->template->pouk = $pouk_query->fetch();
    }
        
    function actionDefault() {
        $this->year = date("Y");
        $this->month = date("n");
        $this->list = "groupList";
        $this->group_by = "person_name";
        $this->filter['person_name'] = "";
        $this->filter['address'] = "";
        $this->filter['payment_type_id'] = null;
        $this->redirect($this->list);
    }
            
    public function actionSetYear($year) {
	    $this->year = $year ;
        $this->month = 1;
        $this->redirect($this->list);
    }    
    
    public function actionSetMonth($month) {
        $this->month = $month;
        $this->redirect($this->list);
    }    

    public function actionSetList($list) {
	    $this->list = $list;
        $this->redirect($this->list);
    }
    
    public function actionSetGroupBy($group_by) {
	    $this->group_by = $group_by;
        $this->redirect($this->list);
    }
    protected function createComponentFilterForm() {
        $form = new Form;
        $form->addText('person_name', 'Jméno:', 40)
		     ->setDefaultValue($this->filter['person_name']);
		     
        $form->addText('address', 'Adresa:', 40)
   		     ->setDefaultValue($this->filter['address']);
   		     
        $form->addSelect('payment_type_id', 'Platba:', $this->paymentType->findAll()->fetchPairs('id', 'type'))
	         ->setPrompt('- vše -');
        $form->addSubmit('filter', 'Filtrovat');
        $form->onSuccess[] = array($this, 'filterFormSucceeded');
        return $form;
    }     
    
    public function filterFormSucceeded(Form $form, $values) {
	    $this->filter['person_name'] = $values->person_name;
	    $this->filter['address'] = $values->address;
	    $this->filter['payment_type_id'] = $values->payment_type_id;
	    
        $this->redirect($this->list);
    }
    
    public function actionFullList() {
		$itemize = $this->group_by == "address" ? "person_name" : "address";	    
		
        $orders_db = $this->order->findAll()  // seskupené objednávky podle sloupce group_by
							     ->where("YEAR(lunch.lunch_date) = ? AND MONTH(lunch.lunch_date) = ?", $this->year, $this->month)
								 ->where("person_name LIKE ? AND address LIKE ?", '%'.$this->filter['person_name'].'%', '%'.$this->filter['address'].'%')
								 ->group($this->group_by)
								 ->order($this->group_by);
									 
		if(isset($this->filter['payment_type_id'])) {
			$orders_db->where('payment_type_id = ?', $this->filter['payment_type_id']);
		}
			
		$groups = array();
		foreach($orders_db as $order) {
			$orders[$order->id] = $order->toArray();
			$groups[$order->id]['orders'] = $this->order->findAll() // všechny objednávky v dané skupině
													    ->where("YEAR(lunch.lunch_date) = ? AND MONTH(lunch.lunch_date) = ?", $this->year, $this->month)
														->where("person_name LIKE ? AND address LIKE ?", '%'.$this->filter['person_name'].'%', '%'.$this->filter['address'].'%')
														->where($this->group_by." = ?", $order[$this->group_by])
														->order("lunch.lunch_date");
														
			if(isset($this->filter['payment_type_id'])) {
				$groups[$order->id]['orders']->where('payment_type_id = ?', $this->filter['payment_type_id']);
			}
		}
		
		$this->template->payment_type = $this->paymentType->findAll()
														  ->fetchPairs('id', 'type');
														  
        $this->template->groups = $groups;
    }
    
          
    public function actionGroupList() {        
		$itemize = $this->group_by == "address" ? "person_name" : "address";
		
        $orders_db = $this->order->findAll()
								 ->select("order.*, SUM(lunch_count) AS lc")
							     ->where("YEAR(lunch.lunch_date) = ? AND MONTH(lunch.lunch_date) = ?", $this->year, $this->month)
								 ->where("person_name LIKE ? AND address LIKE ?", '%'.$this->filter['person_name'].'%', '%'.$this->filter['address'].'%')
								 ->group($this->group_by)
								 ->order($this->group_by);

		if(isset($this->filter['payment_type_id'])) {
			$orders_db->where('payment_type_id = ?', $this->filter['payment_type_id']);
		}
				
		$orders = array();
		foreach($orders_db as $order) {
			$orders[$order->id] = $order->toArray();		
			$orders[$order->id]["itemize"] = $this->order->findAll()
													     ->where("YEAR(lunch.lunch_date) = ? AND MONTH(lunch.lunch_date) = ?", $this->year, $this->month)
														 ->where("person_name LIKE ? AND address LIKE ?", '%'.$this->filter['person_name'].'%', '%'.$this->filter['address'].'%')
														 ->where($this->group_by." = ?", $order[$this->group_by])
														 ->select("DISTINCT ".$itemize)
														 ->order("lunch.lunch_date");
														 
			if(isset($this->filter['payment_type_id'])) {
				$orders[$order->id]["itemize"]->where('payment_type_id = ?', $this->filter['payment_type_id']);
			}
		}

		$view = $this->group_by == "address" ? "groupAddressList" : "groupNamesList";
		$this->setView($view);
        $this->template->itemize = $itemize;
        $this->template->orders = $orders;
    }
    
}
