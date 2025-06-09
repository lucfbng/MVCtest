<?php

require_once "../services/MessageService/DataValidator.service.php";

class ConversationController {
    private $dataValidator;

    public function __construct() {
        $this->dataValidator = new DataValidatorService();
    }

    public function createConversation() {}

    public function getConversations() {}

    public function getConversationDetails() {}

    public function deleteConversation() {}

    public function addParticipant() {}

    public function removeParticipant() {}
}