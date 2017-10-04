<?php

namespace Dhalsted\SMSExceptions;

class SMSTwilioMisconfigException extends \Exception {
	
    public function __construct($message = "There is a problem with your Twilio credentials", $code = 20100, Exception $previous = null) {
        parent::__construct($message, $code, $previous);
    }

    public function __toString() {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }

}