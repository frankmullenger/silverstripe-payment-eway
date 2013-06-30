<?php

//Include RapidAPI Library
require(dirname(__FILE__).'/../thirdparty/Rapid3.0.php');

class RapidGateway extends PaymentGateway_GatewayHosted { 
	
	protected $supportedCurrencies = array(
		'NZD' => 'New Zealand Dollar',
		'AUD' => 'Australian Dollar'
	);
	
	public function __construct() {
		//Our gateway URL is on the local site
		$this->gatewayURL = Director::absoluteURL('/Rapid/pay');
	}

	public function getConfig() {
		if (!$this->config) {
			
			$config = Config::inst()->get(get_class($this), self::get_environment());
			
			//Zero values discarded up by YAML parser for some reason
			if (!isset($config['ShowDebugInfo'])) {
				$config['ShowDebugInfo'] = 0;
			}
			
			$this->config = array_merge(
				Config::inst()->get(get_class($this), 'response_codes'),
				Config::inst()->get(get_class($this), 'error_codes'),
				$config
			);
		}
		return $this->config;
	}

	public function process($data) {

		//Create RapidAPI Service
		$service = new Eway\Rapid\RapidAPI();
		$service->APIConfig = $this->getConfig();

		//Create AccessCode Request Object
		$request = new Eway\Rapid\CreateAccessCodeRequest();
		
		//Populate values for Payment Object
		//Note: TotalAmount is a Required Field When Process a Payment, TotalAmount should set to "0" or leave EMPTY when Create/Update A TokenCustomer
		$request->Payment->TotalAmount = $data['Amount'] * 100; //Total amount is in cents
		$request->Payment->CurrencyCode = $data['Currency'];

		//Url to the page for getting the result with an AccessCode
		//Note: RedirectUrl is a Required Field For all cases
		$request->RedirectUrl = $this->returnURL;

		//Method for this request. e.g. ProcessPayment, Create TokenCustomer, Update TokenCustomer & TokenPayment
		$request->Method = 'ProcessPayment';
		
		//TODO Hook for setting data on request

		//Call RapidAPI
		$result = $service->CreateAccessCode($request);

		//Check if any error returns
		if (isset($result->Errors)) {

			//Get Error Messages from Error Code. Error Code Mappings are in the Config.ini file
			$errorArray = explode(",", $result->Errors);
			$lblError = "";
			
			foreach ( $errorArray as $error ) {
				
				if (isset($service->APIConfig[$error])) {
					$lblError .= $error." ".$service->APIConfig[$error]."<br>";
				}  
				else {
					$lblError .= $error;
				}   
			}
			
			return new PaymentGateway_Failure(null, array($lblError));
		} 
		else {

			Session::set('EwayResponse', $result);

			$postData = array(
				'Amount' => $data['Amount'],
				'Currency' => $data['Currency'],
				'ReturnURL' => $this->returnURL
			);
			
			$queryString = http_build_query($postData);
			Controller::curr()->redirect($this->gatewayURL . '?' . $queryString);
			return;
		}
	}
	
	public function check($request) {

		//Create RapidAPI Service
		$service = new Eway\Rapid\RapidAPI();
		$service->APIConfig = $this->getConfig();
		
		//Build request for getting the result with the access code.
		$rapidRequest = new Eway\Rapid\GetAccessCodeResultRequest();
		$rapidRequest->AccessCode = $request->getVar('AccessCode');

		//Call RapidAPI to get the result
		$result = $service->GetAccessCodeResult($rapidRequest);

		//Save these errors onto the Payment object
		if($errors = $this->getErrors($result)) {
			return new PaymentGateway_Failure(null, $errors);
		}
		else {
			return new PaymentGateway_Success();
		}
	}
	
	public function getErrors($result) {
		
		$errors = array();
		$config = $this->getConfig();
		
		$successCodes = array('00', '08', '10', '11', '16');
		if (isset($result->ResponseCode) && !in_array($result->ResponseCode, $successCodes)) {
			$errors[$result->ResponseMessage] = $config[$result->ResponseMessage];
		}
		
		return $errors;
	}
	
	public function getSupportedCurrencies() {

    $config = $this->getConfig();
    if (isset($config['supported_currencies'])) {
      $this->supportedCurrencies = $config['supported_currencies'];
    }
    return $this->supportedCurrencies;
  }
}

class RapidGateway_Mock extends RapidGateway { 
	
}