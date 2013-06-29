<?php

class Rapid extends Page_Controller {

	/**
	 * Controller action for showing payment form
	 *
	 * @param SS_HTTPRequest
	 */
	public function pay($request) {

		return array(
			'Title' => 'Enter your credit card details',
			'Content' => '',
			'Form' => $this->PayForm()
		);
	}

	/**
	 * Return the payment form
	 */
	public function PayForm() {
		$request = $this->getRequest();

		//TODO Use SilverStripe Session class
		$response = Session::get('EwayResponse');
		
		$months = array('01','02','03','04','05','06','07','08','09','10','11','12');
		$years = range(date('y'), date('y') + 10); //Note: years beginning with 0 might cause issues

		$fields = new FieldList(
			HiddenField::create('EWAY_ACCESSCODE', '', $response->AccessCode),
			TextField::create('EWAY_CARDNAME', 'Card holder', 'Test User'),
			TextField::create('EWAY_CARDNUMBER', 'Card Number', '4444333322221111'),
			DropdownField::create('EWAY_CARDEXPIRYMONTH', 'Expiry Month', array_combine($months, $months)),
			DropdownField::create('EWAY_CARDEXPIRYYEAR', 'Expiry Year', array_combine($years, $years)),

			// TextField::create('EWAY_CARDSTARTMONTH', 'Valid from month', ''), //UK only
			// TextField::create('EWAY_CARDSTARTYEAR', 'Valid from year', ''), //UK only
			// TextField::create('EWAY_CARDISSUENUMBER', 'Issue number', ''),
			
			TextField::create('EWAY_CARDCVN', 'CVN Number', '123')
		);

		$actions = new FieldList(
			FormAction::create('', 'Process')	
		);

		$form = new Form($this, 'PayForm', $fields, $actions);
		$form->setFormAction($response->FormActionURL);
		return $form;
	}

}