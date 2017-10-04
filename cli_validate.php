<?php

//index.php
// sample usage

require __DIR__ . '/vendor/autoload.php';

if (count($argv) < 2){
	echo ("Usage: cli_validate.php [phone number] [country_code]\n");
	echo ("eg cli_validate.php '(773) 555-1212' or cli_validate.php '(773) 555-1212' US\n");
	die( ('Country code defaults to US\n'));
}

$phone_number = $argv[1];
isset($argv[2]) ? $country_code = $argv[2] : $country_code = null;

// get Twilio credentials from .env
$dotenv = new \Dotenv\Dotenv(__DIR__);
$dotenv->load();
$twilio_sid = getenv('TWILIO_SID');
$twilio_token = getenv('TWILIO_TOKEN');

try {
	
	$sv = new Dhalsted\SMS\SMSValidator($twilio_sid, $twilio_token);
	if ($country_code === null){
		$validated_number = $sv->validateMobileNumber($phone_number);
	} else {
		$validated_number = $sv->validateMobileNumber($phone_number, $country_code);
	}

	// by default mobile and voip numbers will count as valid.  If you want to specify
	// one or the other, do something like this:
	// $validated_number = $sv->validateMobileNumber($phone_number, $country_code, array('mobile'));

	echo $validated_number."\n";

} 

// you might want to provide different feedback depending on 
// the error state

// you tried to call the Validator with missing or invalid 
// Twilio credentials.  Check .env?
catch (\Dhalsted\SMSExceptions\SMSTwilioMisconfigException $e){
	echo $e->__toString();
}

// the submitted number is invalid--can't be parsed, not enough digits, invalid region code
catch (\Dhalsted\SMSExceptions\SMSBadNumberException $e){
	echo $e->__toString();
}

// the submitted number is not a mobile number
catch (\Dhalsted\SMSExceptions\SMSNotMobileException $e){
	echo $e->__toString();
}

// something else happened
catch (\Exception $e){
	echo "Exception: " . $e->getMessage() . "\n";
}


?>