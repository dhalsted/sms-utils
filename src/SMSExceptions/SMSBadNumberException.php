<?php

namespace Dhalsted\SMSExceptions;

class SMSBadNumberException extends \Exception {
	
    public function __construct($message = "That number does not appear to be a valid phone number", $code = 20101, Exception $previous = null) {
        parent::__construct($message, $code, $previous);
    }

    public function __toString() {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }

}