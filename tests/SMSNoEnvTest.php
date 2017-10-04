<?php

namespace UnitTests;


// these tests do not depend on a valid .env config file

class SMSNoEnvTest extends \PHPUnit_Framework_TestCase
{

	// check that an invalid region or country code returns an SMSBadNumberException

	/**
     * @expectedException \Dhalsted\SMSExceptions\SMSBadNumberException
     * @expectedExceptionCode 20101
    */
	public function testCheckInvalidRegion(){

		$sv = new \Dhalsted\SMS\SMSValidator();
		$sv->validatePhoneNumber("773 555 1212", "xx");
	}

	// check that a truly invalid phone number returns an SMSBadNumberException

	/**
     * @expectedException \Dhalsted\SMSExceptions\SMSBadNumberException
     * @expectedExceptionCode 20101
    */

	public function testCheckInvalidNumber(){
		$sv = new \Dhalsted\SMS\SMSValidator();
		$sv->validatePhoneNumber("phone 773 555", "US");
	}

	// check that a likely-looking but invalid (for example, truncated)
	// phone number returns an SMSBadNumberException

	/**
     * @expectedException \Dhalsted\SMSExceptions\SMSBadNumberException
     * @expectedExceptionCode 20101
    */

	public function testCheckTruncatedNumber(){
		$sv = new \Dhalsted\SMS\SMSValidator();
		$sv->validatePhoneNumber("312 773 555", "US");
	}

	// check that a phone number from a non-existent area code returns an error

	/**
     * @expectedException \Dhalsted\SMSExceptions\SMSBadNumberException
     * @expectedExceptionCode 20101
    */

	public function testCheckInvalidAreaCode(){
		$sv = new \Dhalsted\SMS\SMSValidator();
		$sv->validatePhoneNumber("(123) 444-3333", "US");
	}

}