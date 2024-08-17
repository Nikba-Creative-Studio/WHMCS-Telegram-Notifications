<?php
// Configuration function for the module
function telegram_notifications_config() {
    $configarray = [
        "name" => "Telegram Notifications",
        "description" => "This addon sends notifications to a Telegram channel.",
        "version" => "1.0",
        "author" => "Nikba Creative Studio",
        "fields" => [
            "bot_token" => [
                "FriendlyName" => "Bot Token",
                "Type" => "text",
                "Size" => "50",
                "Description" => "Enter your Telegram Bot Token here.",
                "Default" => "",
            ],
            "chat_id" => [
                "FriendlyName" => "Chat ID",
                "Type" => "text",
                "Size" => "50",
                "Description" => "Enter your Telegram Chat ID here.",
                "Default" => "",
            ],
            "ClientAdd" => [
                "FriendlyName" => "Notify on New Client",
                "Type" => "yesno",
                "Description" => "Send notification when a new client is added.",
            ],
            "InvoicePaid" => [
                "FriendlyName" => "Notify on Invoice Paid",
                "Type" => "yesno",
                "Description" => "Send notification when an invoice is paid.",
            ],
            "TicketOpen" => [
                "FriendlyName" => "Notify on Ticket Open",
                "Type" => "yesno",
                "Description" => "Send notification when a new ticket is opened.",
            ],
            "TicketUserReply" => [
                "FriendlyName" => "Notify on Ticket Reply",
                "Type" => "yesno",
                "Description" => "Send notification when a user replies to a ticket.",
            ],
        ],
    ];

    return $configarray;
}

// Activate function
function telegram_notifications_activate() {
    // Any activation process can be handled here
    return [
        'status' => 'success',
        'description' => 'Telegram Notifications module activated successfully.',
    ];
}

// Deactivate function
function telegram_notifications_deactivate() {
    // Any deactivation process can be handled here
    return [
        'status' => 'success',
        'description' => 'Telegram Notifications module deactivated successfully.',
    ];
}

// Upgrade function (optional)
function telegram_notifications_upgrade($vars) {
    $version = $vars['version'];
    // Handle upgrades between versions
}

