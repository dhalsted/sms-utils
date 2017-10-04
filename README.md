# sms-utils
sms-utils contains methods I've found useful for handling text messaging.  I primarily use Twilio for this but eventually I hope to add support for other vendors.

The first version contains code to validate that a string represents a valid SMS number--something that Twilio won't choke on.  This means it must meet certain formatting restrictions and, in addition, must be a number for a text-capable phone (ie, not a landline).  The rules for this are complicated.  The SMS Validator class will take a string, check that it can be parsed as a phone number, check that it is not a landline, and return a version of the number formatted for use with Twilio.  It can handle numbers from different countries and regions.  Format validation is handled through Google's libphonenumber and the check for SMS capability is run through Twilio.

Note that you'll need Twilio API credentials to use the Validator.

## Installation

Clone or download the repository.  Then
```
composer install
```

To run tests, first copy .env.example to .env and then run
```
composer test
```

This will run a set of tests that don't require Twilio credentials.  To run a more complete set of tests, fill in .env with Twilio credentials and some sample phone numbers, and then do
```
composer test 
```

again.

## Quickstart

To use the validator, do something like
```
php cli_validate.php '(444) 111-2222'
```
where (444) 111-2222 is a valid US landline.  You should get 
```
Not mobile exception: Number does not appear to be a mobile number
```
Then try it with a mobile number.  In this case, you'll get back
```
+14441112222
```
To call the SMSValidator class in your own code, just do
```
$sv = new \Dhalsted\SMS\SMSValidator(TWILIO_SID, TWILIO_TOKEN);
$validated_number = $sv->validateMobileNumber($phone_number, $country_code);
```
By default, the Validator checks whether the submitted phone number is a mobile or VOIP number.  If you want to check specifically for one or the other, do something like this:
```
$validated_number = $sv->validateMobileNumber($phone_number, $country_code, array('mobile'));
```

SMSValidator throws several kinds of exceptions which can help your users understand why a number will not work for texts.

* SMSTwilioMisconfigException (code 20100) occurs if missing, incomplete or invalid Twilio credentials are submitted.

* SMSBadNumberException (code 20101) occurs if the data submitted for a phone number can't be parsed or is incomplete.  Submitting a non-existent country or region code, a string that cannot be parsed ("phone number 888 555 1212" instead of "888 555 1212"), or a truncated number will raise this exception.

* SMSNotMobileException (code 20102) occurs if a number is submitted that is not a valid type, by default either mobile or VOIP.


