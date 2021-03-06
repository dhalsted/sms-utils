<?php

namespace Dhalsted\SMSExceptions;

class SMSNotMobileException extends \Exception {
	
    public function __construct($message = "Number does not appear to be of a valid type", $code = 20102, Exception $previous = null) {
        parent::__construct($message, $code, $previous);
    }

    public function __toString() {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }

}