# Telegram Notifications for WHMCS

**Telegram Notifications** is a WHMCS addon that allows you to send real-time notifications to a specified Telegram channel or group for various events occurring in your WHMCS installation. This addon is highly configurable and easy to set up.

## Features

- Send notifications for new client registrations.
- Send notifications when an invoice is paid.
- Send notifications when a new support ticket is opened.
- Send notifications when a user replies to a support ticket.
- Easy configuration through the WHMCS admin panel.

## Installation

1. **Download the addon:**
   - Clone the repository or download the ZIP file and extract it.

   ```bash
   git clone https://github.com/Nikba-Creative-Studio/WHMCS-Telegram-Notifications.git
   ```

2. **Upload the addon:**
   - Upload the `telegram_notifications` folder to the `modules/addons/` directory of your WHMCS installation.

3. **Activate the addon:**
   - Log in to your WHMCS admin panel.
   - Go to **Setup** > **Addon Modules**.
   - Locate **Telegram Notifications** and click the **Activate** button.

4. **Configure the addon:**
   - After activation, click on **Configure** to set up your Telegram Bot Token, Chat ID, and select which notifications you want to enable.

## Configuration

1. **Bot Token:**
   - Obtain your Bot Token by creating a new bot using the [BotFather](https://core.telegram.org/bots#botfather) on Telegram.

2. **Chat ID:**
   - Get your Chat ID where you want to receive notifications. You can get this by messaging your bot and using a tool like [get_id_bot](https://t.me/get_id_bot) to retrieve the chat ID.

3. **Enable Notifications:**
   - You can enable or disable specific notifications (e.g., ClientAdd, InvoicePaid, TicketOpen, TicketUserReply) via checkboxes in the addon settings.

## Usage

Once configured, the addon will automatically send notifications to the specified Telegram chat whenever the selected events occur in WHMCS. You can monitor new client registrations, invoice payments, and support ticket activity in real-time.

## Files

- **hooks.php:** Contains the WHMCS hooks for triggering notifications based on events.
- **telegram_notifications.php:** Contains the core logic for sending messages to Telegram and configuration management.
- **whmcs.json:** Defines the configuration options and metadata for the addon.

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## Contributing

Contributions are welcome! Please fork this repository, make your changes, and submit a pull request. Ensure your code adheres to the coding standards and includes appropriate documentation.

## Support

For support, issues, or feature requests, please open an issue on GitHub or contact the repository maintainer.

## Credits

Developed by [Nikba Creative Studio](https://github.com/Nikba-Creative-Studio/).