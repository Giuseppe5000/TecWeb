<?php

class UserAlredyExistsException extends Exception {
    public function errorMessage() {
        return "Username o email già in uso";
    }
}

class UserRegisterGenericException extends Exception {
    public function errorMessage() {
        return "Errore durante la registrazione";
    }
}

class PrepareStatementException extends Exception {
    public function __construct(string $message) {
        parent::__construct($message);
        $this->message = "Errore nella preparazione della query: " . $message;
    }

    public function errorMessage() {
        return $this->message;
    }
}
