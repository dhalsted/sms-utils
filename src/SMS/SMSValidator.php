<?php

namespace Dhalsted\SMS;

class SMSValidator{

	private $twilio_sid;
	private $twilio_token;

	/**
	 * Without Twilio credentials you can't verify mobile
	*/

	public function __construct($twilio_sid = '', $twilio_token = ''){
		$this->twilio_sid = $twilio_sid;
		$this->twilio_token = $twilio_token;
	}


	/**
     *  If $phone_number can be interpreted as a phone number for 
     *  $country_code and appears to be valid, return a normalized version.
     *  Works for any phone number, not just mobile.
     *
     *  Otherwise, throw an appropriate Exception.
     *
     *  @sms_number String Candidate SMS number to validate
     *  @country_code String  Country code for number; defaults to 'US'
	 *
	 *
	 * 	@return String  normalized national number, formatted per E164
	 *
     */

	public function validatePhoneNumber($phone_number, $country_code = 'US'){

		// get an instance of the phone number
		$phoneUtil = \libphonenumber\PhoneNumberUtil::getInstance();

		try {
			$proto = $phoneUtil->parse($phone_number, strtoupper($country_code));
			if (!$phoneUtil->isValidNumber($proto)){
		    	throw new \Dhalsted\SMSExceptions\SMSBadNumberException();
		    }

		} catch (\libphonenumber\NumberParseException $e) {
			throw new \Dhalsted\SMSExceptions\SMSBadNumberException();
		}

		return $phoneUtil->format($proto, \libphonenumber\PhoneNumberFormat::E164);
	}

	/**
     *  If $sms_number can be interpreted as a phone number for 
     *  $country_code and Twilio thinks it is a mobile number, 
     *  return a normalized version.
     *
     *  Otherwise, throw an appropriate Exception.
     *
     *  @sms_number String Candidate SMS number to validate
     *  @country_code String  Country code for number; defaults to 'US'
	 *
	 *
	 * 	@return String normalized national number, formatted per E164
	 *
     */

	public function validateMobileNumber($sms_number, $country_code = "US", $types = array("mobile", "voip")){

		try {

			$validatedNumber = $this->validatePhoneNumber($sms_number, $country_code);
			$client = new \Twilio\Rest\Client($this->twilio_sid, $this->twilio_token);
		    $number = $client->lookups
			    ->phoneNumbers($validatedNumber)
			    ->fetch(
			        array("type" => "carrier")
			    );

			$valid_type = false;
			foreach ($types as $type){
				if ($number->carrier["type"] == strtolower($type)){
					$valid_type = true;
				}
			}

			if (!$valid_type){
				throw new \Dhalsted\SMSExceptions\SMSNotMobileException(); // number was not one of the types requested for validation
			}
			
		// a number of exception types can be thrown.
		} catch (\libphonenumber\NumberParseException $e) { // libphonenumber can't parse this
			throw new \Dhalsted\SMSExceptions\SMSBadNumberException();
		} catch (\Dhalsted\SMSExceptions\SMSBadNumberException $e){ // submitted number was bad -- could be parsed, but not valid
			throw new \Dhalsted\SMSExceptions\SMSBadNumberException();
		} catch (\Twilio\Exceptions\ConfigurationException $e){ // credentials for Twilio incomplete or missing
			throw new \Dhalsted\SMSExceptions\SMSTwilioMisconfigException();
		} catch (\Twilio\Exceptions\RestException $e) { // credentials supplied for Twilio are not valid
			throw new \Dhalsted\SMSExceptions\SMSTwilioMisconfigException();
		}
		catch (\Exception $e){  // something else went wrong
			throw $e;
		}

		// continue

		return $validatedNumber;

		
	}
}