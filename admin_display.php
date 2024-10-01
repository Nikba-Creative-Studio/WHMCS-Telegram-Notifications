<?php

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

use WHMCS\Database\Capsule;

// Afișează logurile modulului
function telegram_notifications_admin_display() {
    // Query pentru a extrage logurile din baza de date
    $logs = Capsule::table('tblmodulelog')
        ->where('module', 'telegram_notifications')
        ->orderBy('id', 'desc')
        ->limit(100) // Limitează la ultimele 100 de loguri
        ->get();

    echo '<table class="table table-bordered">';
    echo '<thead><tr><th>ID</th><th>Date</th><th>Response Text</th><th>Link</th></tr></thead>';
    echo '<tbody>';

    if ($logs->isEmpty()) {
        echo '<tr><td colspan="5">No logs found for this module.</td></tr>';
    } else {
        foreach ($logs as $log) {
            $response = json_decode($log->response, true);
            $result = $response['result'] ?? null;
            $result['date'] = date('Y-m-d H:i:s', $result['date']);
            echo '<tr>';
            echo '<td>' . $log->id . '</td>';
            echo '<td>' . $log->date . '</td>';
            echo '<td>' . htmlspecialchars($result['text']) . '</td>';
            echo '<td><a href="'. htmlspecialchars($result['entities'][0]['url']) .'" target="_blank">View</a></td>';
            echo '</tr>';
        }
    }

    echo '</tbody>';
    echo '</table>';
}

// Afișează logurile la accesarea modulului din Addons
telegram_notifications_admin_display();
