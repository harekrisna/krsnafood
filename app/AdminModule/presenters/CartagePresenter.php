<?php

namespace App\AdminModule\Presenters;
use Nette,
	App\Model;
use Nette\Diagnostics\Debugger;
use Nette\Application\UI\Form;

final class CartagePresenter extends BasePresenter {    
    protected function startup()  {
        parent::startup();
    
        if (!$this->getUser()->isLoggedIn()) {
            $this->redirect('Sign:in');
        }
    }  

    public function beforeRender() {
        $this->template->menu = array();
        $this->setLayout("wide.layout");
    }
        
    function actionList() {		
        $cartages = $this->cartage
 		                 ->findAll();
 
        $this->template->cartages = $cartages;
    }    
}
