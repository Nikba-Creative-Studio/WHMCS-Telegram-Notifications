<?php
use WHMCS\Config\Setting;

// Function to send a message to the Telegram bot
if (!function_exists('telegram_notify')) {
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
}

// Function to build the message based on the notification type
if (!function_exists('build_message')) {
    function build_message($type, $data) {
        // Get the base URL of the WHMCS installation dynamically
        $baseURL = Setting::getValue('SystemURL');
    
        // Define a list of patterns or keywords to ignore
        $ignorePatterns = [
            '/^Cron\b/i', // Ignore subjects starting with "Cron"
            '/root@/',    // Ignore emails from root@
            '/csf/',      // Ignore subjects containing "csf"
            // Add more patterns as needed
        ];
    
        // Check if the subject matches any of the ignore patterns
        if ($type === 'TicketOpen' || $type === 'TicketUserReply') {
            foreach ($ignorePatterns as $pattern) {
                if (preg_match($pattern, $data['subject'])) {
                    // If a match is found, return null or an empty string to skip the notification
                    return null;
                }
            }
        }
    
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
}

// Function to send a message via the Telegram API
if (!function_exists('send_telegram_message')) {
    function send_telegram_message($botToken, $chatID, $message) {
        $url = "https://api.telegram.org/bot{$botToken}/sendMessage";
        $data = [
            'chat_id' => $chatID,
            'text' => $message,
            'parse_mode' => 'Markdown',
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
        logModuleCall('telegram_notifications', 'send_telegram_message', $data, $response, null);
    }
}

// Configuration function for the module
if (!function_exists('telegram_notifications_config')) {
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
}

// Activate function
if (!function_exists('telegram_notifications_activate')) {
    function telegram_notifications_activate() {
        // Any activation process can be handled here
        return [
            'status' => 'success',
            'description' => 'Telegram Notifications module activated successfully.',
        ];
    }
}

// Deactivate function
if (!function_exists('telegram_notifications_deactivate')) {
    function telegram_notifications_deactivate() {
        // Any deactivation process can be handled here
        return [
            'status' => 'success',
            'description' => 'Telegram Notifications module deactivated successfully.',
        ];
    }
}

// Upgrade function (optional)
if (!function_exists('telegram_notifications_upgrade')) {
    function telegram_notifications_upgrade($vars) {
        $version = $vars['version'];
        // Handle upgrades between versions
    }
}

if (!function_exists('telegram_notifications_output')) {
    function telegram_notifications_output($vars) {
        include __DIR__ . '/admin_display.php';
    }
}


