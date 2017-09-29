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

	echo $validated_number."\n";

} 

// you might want to provide different feedback depending on 
// the error state

// libphone number can't parse the submitted number

catch (\libphonenumber\NumberParseException $e){
	echo "Number parse exception: ". $e->getMessage() . "\n";
} 

// the submitted number is invalid--not enough digits, for example
catch (\Dhalsted\SMSExceptions\SMSBadNumberException $e){
	echo "Bad number exception: ". $e->getMessage() . "\n";
}

// the submitted number is not a mobile number
catch (\Dhalsted\SMSExceptions\SMSNotMobileException $e){
	echo "Not mobile exception: ". $e->getMessage() . "\n";
}
 
// you tried to call Twilio without credentials --something is probably misconfigured
catch (\Twilio\Exceptions\ConfigurationException $e){
	echo "Twilio configuration exception: ". $e->getMessage() . "\n";
}

// you tried to call Twilio's REST interface and got a 401 (access denied),
// so you need to double-check your Twilio credentials
catch (\Twilio\Exceptions\RestException $e){
	echo "Twilio rest exception: Status code". $e->getStatusCode().", message ". $e->getMessage() . "\n";
}

// something else happened
catch (\Exception $e){
	echo "Exception: " . $e->getMessage() . "\n";
}


?>