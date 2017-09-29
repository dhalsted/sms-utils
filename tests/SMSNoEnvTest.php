<?php

namespace UnitTests;


// these tests do not depend on a valid .env config file

class SMSNoEnvTest extends \PHPUnit_Framework_TestCase
{

	// check that an invalid region or country code returns an error
	public function testCheckInvalidRegion(){
		$this->setExpectedException(\libphonenumber\NumberParseException::class);
		$sv = new \Dhalsted\SMS\SMSValidator();
		$sv->validatePhoneNumber("773 555 1212", "xx");

	}

	// check that a truly invalid phone number returns an error
	public function testCheckInvalidNumber(){
		$this->setExpectedException(\Dhalsted\SMSExceptions\SMSBadNumberException::class);
		$sv = new \Dhalsted\SMS\SMSValidator();
		$sv->validatePhoneNumber("phone 773 555", "US");

	}

	// check that a phone number from a non-existent area code returns an error
	public function testCheckInvalidAreaCode(){
		$this->setExpectedException(\Dhalsted\SMSExceptions\SMSBadNumberException::class);
		$sv = new \Dhalsted\SMS\SMSValidator();
		$sv->validatePhoneNumber("(123) 444-3333", "US");

	}

}