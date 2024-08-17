<?php

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

use WHMCS\Config\Setting;

// Function to send a message to the Telegram bot
function telegram_notify($type, $data) {
    // Retrieve settings from the addon configuration
    $botToken = get_query_val('tbladdonmodules', 'value', ['module' => 'telegram_notifications', 'setting' => 'bot_token']);
    $chatID = get_query_val('tbladdonmodules', 'value', ['module' => 'telegram_notifications', 'setting' => 'chat_id']);
    
    // Check if the notification type is enabled in the settings
    $isEnabled = get_query_val('tbladdonmodules', 'value', ['module' => 'telegram_notifications', 'setting' => $type]);
    
    if ($isEnabled) {
        $message = build_message($type, $data);
        send_telegram_message($botToken, $chatID, $message);
    }
}

// Function to build the message based on the notification type
function build_message($type, $data) {
    // Get the base URL of the WHMCS installation dynamically
    $baseURL = Setting::getValue('SystemURL');

    switch ($type) {
        case 'ClientAdd':
            $clientURL = "{$baseURL}/admin/clientssummary.php?userid={$data['id']}";
            return "🆕 New Client Added\nID: {$data['id']}\nName: {$data['name']}\nEmail: {$data['email']}\n[View Client]({$clientURL})";
        case 'InvoicePaid':
            $invoiceURL = "{$baseURL}/admin/invoices.php?action=edit&id={$data['id']}";
            return "💵 Invoice Paid\nInvoice ID: {$data['id']}\nUser ID: {$data['userid']}\nUser Name: {$data['name']}\nTotal: {$data['total']}\n[View Invoice]({$invoiceURL})";
        case 'TicketOpen':
            $ticketURL = "{$baseURL}/admin/supporttickets.php?action=view&id={$data['id']}";
            return "🎫 New Ticket Opened\nTicket ID: {$data['id']}\nUser ID: {$data['userid']}\nUser Name: {$data['name']}\nSubject: {$data['subject']}\n[View Ticket]({$ticketURL})";
        case 'TicketUserReply':
            $ticketURL = "{$baseURL}/admin/supporttickets.php?action=view&id={$data['ticketid']}";
            return "💬 New Ticket Reply\nTicket ID: {$data['ticketid']}\nUser ID: {$data['userid']}\nUser Name: {$data['name']}\nMessage: {$data['message']}\n[View Ticket]({$ticketURL})";
        default:
            return "Unknown notification type.";
    }
}

// Function to send a message via the Telegram API
function send_telegram_message($botToken, $chatID, $message) {
    $url = "https://api.telegram.org/bot{$botToken}/sendMessage";
    $data = [
        'chat_id' => $chatID,
        'text' => $message,
    ];

    // Use cURL to send the POST request
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    curl_close($ch);

    // Optionally, you can log the response for debugging
    logModuleCall('telegram_notifications', 'send_telegram_message', $data, $response);
}


// Hook to send a notification when a new client is added
add_hook('ClientAdd', 1, function($vars) {
    $clientDetails = [
        'id' => $vars['userid'],
        'name' => $vars['firstname'] . ' ' . $vars['lastname'],
        'email' => $vars['email'],
    ];
    telegram_notify('ClientAdd', $clientDetails);
});

// Hook to send a notification when an invoice is paid
add_hook('InvoicePaid', 1, function($vars) {
    $invoiceData = localAPI('GetInvoice', ['invoiceid' => $vars['invoiceid']]);
    $clientDetails = localAPI('GetClientsDetails', ['clientid' => $invoiceData['userid']]);
    $invoiceDetails = [
        'id' => $vars['invoiceid'],
        'userid' => $invoiceData['userid'],
        'name' => $clientDetails['fullname'],
        'total' => $invoiceData['total'],
    ];
    telegram_notify('InvoicePaid', $invoiceDetails);
});

// Hook to send a notification when a new ticket is opened
add_hook('TicketOpen', 1, function($vars) {
    $clientDetails = localAPI('GetClientsDetails', ['clientid' => $vars['userid']]);
    $ticketDetails = [
        'id' => $vars['ticketid'],
        'userid' => $vars['userid'],
        'name' => $clientDetails['fullname'],
        'subject' => $vars['subject'],
    ];
    telegram_notify('TicketOpen', $ticketDetails);
});

// Hook to send a notification when a user replies to a ticket
add_hook('TicketUserReply', 1, function($vars) {
    $clientDetails = localAPI('GetClientsDetails', ['clientid' => $vars['userid']]);
    $replyDetails = [
        'ticketid' => $vars['ticketid'],
        'userid' => $vars['userid'],
        'name' => $clientDetails['fullname'],
        'message' => $vars['message'],
    ];
    telegram_notify('TicketUserReply', $replyDetails);
});
