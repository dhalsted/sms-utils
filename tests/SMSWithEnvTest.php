<?php

namespace UnitTests;

// these tests check for functionality that depends on a valid .env config file

class SMSWithEnvTest extends \PHPUnit_Framework_TestCase
{

	protected $twilio_sid;
	protected $twilio_token;
	protected $valid_landline_number;
	protected $valid_landline_country_code;
	protected $valid_mobile_number;
	protected $valid_mobile_country_code;

	public static function setUpBeforeClass(){
		if (!is_readable(__DIR__."/../.env") || !is_file(__DIR__."/../.env")) {
           	die("Error!  the .env file cannot be located.  Do you need to copy .env.example to .env?\n");
        }

		$dotenv = new \Dotenv\Dotenv(__DIR__."/../");
		$dotenv->load();

	}

	// check that calling the validator with incorrect Twilio credentials returns an error
	public function testBadTwilioCredentials(){

	

		getenv('TWILIO_SID') === null ? $this->twilio_sid = '' : $this->twilio_sid = getenv('TWILIO_SID');
		getenv('TWILIO_TOKEN') === null ? $this->twilio_token = '' : $this->twilio_token = getenv('TWILIO_TOKEN');
		getenv('VALID_MOBILE_NUMBER') === null ? $this->valid_mobile_number = '' : $this->valid_mobile_number = getenv('VALID_MOBILE_NUMBER');
		getenv('VALID_MOBILE_COUNTRY_CODE') === null ?  $this->valid_mobile_country_code = '' : $this->valid_mobile_country_code = getenv('VALID_MOBILE_COUNTRY_CODE');	


		if ( $this->valid_mobile_number == '' || $this->valid_mobile_country_code == '' ||  $this->twilio_sid == '' || $this->twilio_token == ''){
			$this->markTestSkipped("This test can only be performed if TWILIO_SID, TWILIO_TOKEN, VALID_MOBILE_NUMBER and VALID_MOBILE_COUNTRY_CODE have been set");
		} else {
			$this->setExpectedException(\Twilio\Exceptions\ConfigurationException::class);
			$sv = new \Dhalsted\SMS\SMSValidator("",""); // this
			$sv->validateMobileNumber($this->valid_mobile_number, $this->valid_mobile_country_code);
		}
	}

	// check that calling the validator with correct Twilio credentials 
	// but a land line number returns an SMSNotMobileException
	public function testLandline(){

		$dotenv = new \Dotenv\Dotenv(__DIR__."/../");
		$dotenv->load();		

		getenv('TWILIO_SID') === null ? $this->twilio_sid = '' : $this->twilio_sid = getenv('TWILIO_SID');
		getenv('TWILIO_TOKEN') === null ? $this->twilio_token = '' : $this->twilio_token = getenv('TWILIO_TOKEN');
		getenv('VALID_LANDLINE_NUMBER') === null ? $this->valid_landline_number = '' :  $this->valid_landline_number = getenv('VALID_LANDLINE_NUMBER');
		getenv('VALID_LANDLINE_COUNTRY_CODE') === null ? $this->valid_landline_country_code = 'US' : $this->valid_landline_country_code = getenv('VALID_LANDLINE_COUNTRY_CODE');

		if ( $this->valid_landline_number == '' || $this->valid_landline_country_code == '' ||$this->twilio_sid == '' || $this->twilio_token == ''){
			$this->markTestSkipped("This test can only be performed if TWILIO_SID, TWILIO_TOKEN, VALID_LANDLINE_NUMBER and VALID_LANDLINE_COUNTRY_CODE have been set");
		} else {
			$this->setExpectedException(\Dhalsted\SMSExceptions\SMSNotMobileException::class);
			$sv = new \Dhalsted\SMS\SMSValidator($this->twilio_sid, $this->twilio_token);
			$sv->validateMobileNumber($this->valid_landline_number, $this->valid_landline_country_code);
		}
	}

	// check that calling the validator with correct Twilio credentials 
	// and a valid mobile number returns the number in E164 format
	public function testMobileNumber(){

		$dotenv = new \Dotenv\Dotenv(__DIR__."/../");
		$dotenv->load();		

		getenv('TWILIO_SID') === null ? $this->twilio_sid = '' : $this->twilio_sid = getenv('TWILIO_SID');
		getenv('TWILIO_TOKEN') === null ? $this->twilio_token = '' : $this->twilio_token = getenv('TWILIO_TOKEN');
		getenv('VALID_MOBILE_NUMBER') === null ? $this->valid_mobile_number = '' : $this->valid_mobile_number = getenv('VALID_MOBILE_NUMBER');
		getenv('VALID_MOBILE_COUNTRY_CODE') === null ?  $this->valid_mobile_country_code = '' : $this->valid_mobile_country_code = getenv('VALID_MOBILE_COUNTRY_CODE');



		if ( $this->valid_mobile_number == '' || $this->valid_mobile_country_code == '' ||  $this->twilio_sid == '' || $this->twilio_token == ''){
			$this->markTestSkipped("This test can only be performed if TWILIO_SID, TWILIO_TOKEN, VALID_MOBILE_NUMBER and VALID_MOBILE_COUNTRY_CODE have been set");
		} else {
			$sv = new \Dhalsted\SMS\SMSValidator($this->twilio_sid, $this->twilio_token);
			$validated_number = $sv->validatePhoneNumber($this->valid_mobile_number, $this->valid_mobile_country_code);
			$mobile_number = $sv->validateMobileNumber($this->valid_mobile_number, $this->valid_mobile_country_code);
			$this->assertEquals($validated_number, $mobile_number);
		}
	}

	// check that calling the validator with correct Twilio credentials 
	// and a valid mobile number returns the number in E164 format
	public function testVOIPNumber(){

		$dotenv = new \Dotenv\Dotenv(__DIR__."/../");
		$dotenv->load();		

		getenv('TWILIO_SID') === null ? $this->twilio_sid = '' : $this->twilio_sid = getenv('TWILIO_SID');
		getenv('TWILIO_TOKEN') === null ? $this->twilio_token = '' : $this->twilio_token = getenv('TWILIO_TOKEN');
		getenv('VALID_VOIP_NUMBER') === null ? $this->valid_voip_number = '' : $this->valid_voip_number = getenv('VALID_VOIP_NUMBER');
		getenv('VALID_VOIP_COUNTRY_CODE') === null ?  $this->valid_voip_country_code = '' : $this->valid_voip_country_code = getenv('VALID_VOIP_COUNTRY_CODE');



		if ( $this->valid_voip_number == '' || $this->valid_voip_country_code == '' ||  $this->twilio_sid == '' || $this->twilio_token == ''){
			$this->markTestSkipped("This test can only be performed if TWILIO_SID, TWILIO_TOKEN, VALID_VOIP_NUMBER and VALID_VOIP_COUNTRY_CODE have been set");
		} else {
			$sv = new \Dhalsted\SMS\SMSValidator($this->twilio_sid, $this->twilio_token);
			$validated_number = $sv->validatePhoneNumber($this->valid_voip_number, $this->valid_voip_country_code);
			$mobile_number = $sv->validateMobileNumber($this->valid_voip_number, $this->valid_voip_country_code);
			$this->assertEquals($validated_number, $mobile_number);
		}
	}
	
}