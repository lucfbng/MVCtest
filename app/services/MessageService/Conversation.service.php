<?php

require_once "../models/Conversation.model.php";

class ConversationService {

    // Injection de déprendance
    private $conversationModel;

    public function __construct() {
        $this->conversationModel = new Conversation();
    }

    public function createPrivateConversation($user1_id, $user2_id) {}

    public function createGroupConversation($creator_id, $participants, $name) {}

    public function getUserConversations($user_id) {}

    public function getConversationDetails($conv_id, $user_id) {}

    public function addParticipant($conv_id, $user_id, $requester_id) {}

    public function removeParticipant($conv_id, $user_id, $requester_id) {}

    public function updateConversationSettings($conv_id, $settings, $user_id) {}
    
}