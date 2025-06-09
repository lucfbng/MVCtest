<?php

require_once "../models/Conversation.model.php";

class ConversationService {

    // Injection de déprendance
    private $conversationModel;

    public function __construct() {
        $this->conversationModel = new Conversation();
    }
    
    
    
}