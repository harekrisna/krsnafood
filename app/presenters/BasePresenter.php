<?php

namespace App\Presenters;

use Nette,
	App\Model;
use Tracy\Debugger;


/**
 * Base presenter for all application presenters.
 */
abstract class BasePresenter extends Nette\Application\UI\Presenter
{
	protected $menu;
	protected $order;
	protected $address;
	protected $cartage;
	protected $paymentType;
	protected $lunchPreparation;

	protected function startup()	{
		parent::startup();
        $this->menu = $this->context->getService("menu");
        $this->order = $this->context->getService("order");
        $this->address = $this->context->getService("address");
        $this->cartage = $this->context->getService("cartage");
        $this->paymentType = $this->context->getService("paymentType");
        $this->lunchPreparation = $this->context->getService("lunchPreparation");
		$container = $this->presenter->context->getService("container");
		$httpRequest = $container->getByType('Nette\Http\Request');
		$test = $httpRequest->getQuery('test');
		// if($test != 1) exit;
	}
	
	public function beforeRender() {
	    $detect = new Mobile_Detect;
	    $this->template->isMobile = $detect->isMobile();
		$this->template->isChrome = strpos($_SERVER['HTTP_USER_AGENT'], 'Chrome') !== false;
		if (strpos($_SERVER['HTTP_USER_AGENT'], 'rv:11.0') !== false && strpos($_SERVER['HTTP_USER_AGENT'], 'Trident/7.0;')!== false)	{
			$this->template->isMSIE = true;
		}
		else {
			$this->template->isMSIE = false;
		}
		
		if (strpos($_SERVER['HTTP_USER_AGENT'], 'Safari') && !strpos($_SERVER['HTTP_USER_AGENT'], 'Chrome')) {
			$this->template->isSafari = true;
		}
		else {
			$this->template->isSafari = false;				
		}
		
    }
}
