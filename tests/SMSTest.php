<?php

namespace UnitTests;

class SMSTest extends \PHPUnit_Framework_TestCase
{

	protected $twilio_sid;
	protected $twilio_token;
	protected $valid_landline_number;
	protected $valid_landline_country_code;
	protected $valid_mobile_number;
	protected $valid_mobile_country_code;

	// get Twilio credentials from .env
	protected function setUp(){
		$dotenv = new \Dotenv\Dotenv(__DIR__."/../");
		$dotenv->load();
		$this->twilio_sid = getenv('TWILIO_SID');
		$this->twilio_token = getenv('TWILIO_TOKEN');
		$this->valid_landline_number = getenv('VALID_LANDLINE_NUMBER');
		$this->valid_landline_country_code = getenv('VALID_LANDLINE_COUNTRY_CODE');
		$this->valid_mobile_number = getenv('VALID_MOBILE_NUMBER');
		$this->valid_mobile_country_code = getenv('VALID_MOBILE_COUNTRY_CODE');
	}

	// check that an invalid region or country code returns an error
	public function testCheckInvalidRegion(){
		$this->setExpectedException(\libphonenumber\NumberParseException::class);
		$sv = new \Dhalsted\SMS\SMSValidator($this->twilio_sid, $this->twilio_token);
		$sv->validatePhoneNumber("773 555 1212", "xx");

	}

	// check that a truly invalid phone number returns an error
	public function testCheckInvalidNumber(){
		$this->setExpectedException(\Dhalsted\SMSExceptions\SMSBadNumberException::class);
		$sv = new \Dhalsted\SMS\SMSValidator($this->twilio_sid, $this->twilio_token);
		$sv->validatePhoneNumber("phone 773 555", "US");

	}

	// check that a phone number from a non-existent area code returns an error
	public function testCheckInvalidAreaCode(){
		$this->setExpectedException(\Dhalsted\SMSExceptions\SMSBadNumberException::class);
		$sv = new \Dhalsted\SMS\SMSValidator($this->twilio_sid, $this->twilio_token);
		$sv->validatePhoneNumber("(123) 444-3333", "US");

	}

	// check that calling the validator with incorrect Twilio credentials returns an error
	public function testBadTwilioCredentials(){
		if ( isset($this->valid_landline_number) && isset($this->valid_landline_country_code) ){
			$this->setExpectedException(\Twilio\Exceptions\ConfigurationException::class);
			$sv = new \Dhalsted\SMS\SMSValidator("","");
			$sv->validateMobileNumber($this->valid_mobile_number, $this->valid_mobile_country_code);
		}
	}

	// check that calling the validator with correct Twilio credentials 
	// but a land line number returns an error
	public function testTwilioCredentials(){
		if ( isset($this->valid_landline_number) && isset($this->valid_landline_country_code) ){
			$this->setExpectedException(\Dhalsted\SMSExceptions\SMSNotMobileException::class);
			$sv = new \Dhalsted\SMS\SMSValidator($this->twilio_sid, $this->twilio_token);
			$sv->validateMobileNumber($this->valid_landline_number, $this->valid_landline_country_code);
		}
	}

	// check that calling the validator with correct Twilio credentials 
	// and a valid mobile number returns the number in E164 format
	public function testMobileNumber(){
		if ( isset($this->valid_landline_number) && isset($this->valid_landline_country_code) ){
			$sv = new \Dhalsted\SMS\SMSValidator($this->twilio_sid, $this->twilio_token);
			$validated_number = $sv->validatePhoneNumber($this->valid_mobile_number, $this->valid_mobile_country_code);
			$mobile_number = $sv->validateMobileNumber($this->valid_mobile_number, $this->valid_mobile_country_code);
			$this->assertEquals($validated_number, $mobile_number);
		}
	}
	
}