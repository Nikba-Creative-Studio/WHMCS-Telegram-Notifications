<?php

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

// Include the main module file
if (!function_exists('telegram_notify')) {
    require_once __DIR__ . '/telegram_notifications.php';
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
    // Check if the ticket is opened by a registered user
    if (!empty($vars['userid']) && $vars['userid'] != 0) {
        // If the ticket is opened by a registered user, get the client details
        $clientDetails = localAPI('GetClientsDetails', ['clientid' => $vars['userid']]);
        $name = $clientDetails['fullname'];
    } else {
        // If the ticket is opened by a guest, use the contact name
        // Get ticketid info from localAPI
        $ticketid = $vars['ticketid'];
        $ticketDetails = localAPI('GetTicket', ['ticketid' => $ticketid]);
        $name = !empty($ticketDetails['name']) ? $ticketDetails['name'] . '🥷' : 'Guest';
        
    }

    $ticketDetails = [
        'id' => $vars['ticketid'],
        'userid' => $vars['userid'],
        'name' => $name,
        'subject' => $vars['subject'],
    ];

    // Trimite notificarea în Telegram
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
