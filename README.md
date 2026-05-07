# Gmail OAuth Email Sender Setup

This project uses Gmail OAuth to send email and (optionally) IMAP to read inbox data. Follow these steps to configure your Google Cloud project, app credentials, and local environment.

## 1) Create Google Cloud OAuth Credentials

1. Go to the Google Cloud Console and create/select a project.
2. Enable the Gmail API for that project.
3. Configure the OAuth consent screen.
   - User type: Internal or External
   - Add the Gmail scope:
     - https://www.googleapis.com/auth/gmail.send
4. Create OAuth 2.0 Client ID credentials.
   - Application type: Web application
   - Authorized redirect URI:
     - http://localhost/natconInvitations1/gmail_oauth_callback.php

## 2) Configure Local Environment

Update the environment values in [.env](.env). Use placeholders instead of real secrets in shared files.

Required OAuth variables:

- GOOGLE_CLIENT_ID=your_client_id
- GOOGLE_CLIENT_SECRET=your_client_secret
- GOOGLE_REDIRECT_URI=http://localhost/natconInvitations1/gmail_oauth_callback.php
- GOOGLE_SENDER_EMAIL=your_sender_email
- GMAIL_TOKEN_PATH=storage/gmail_token.json

Additional app/tracking variables:

- APP_URL=http://localhost/natconInvitations1/
- TRACKING_SECRET=change_me
- ALLOW_UNVERIFIED_TRACKING=true
- QUEUE_BATCH_SIZE=20
- QUEUE_MAX_ATTEMPTS=3
- REPLY_CHECK_LIMIT=50

Optional IMAP variables if you also use IMAP features:

- IMAP_ENABLED=true
- IMAP_HOST=imap.gmail.com
- IMAP_PORT=993
- IMAP_USERNAME=your_email
- IMAP_PASSWORD=your_app_password
- IMAP_MAILBOX=INBOX
- IMAP_ENCRYPTION=ssl

## 3) Authorize Gmail Access

1. Open [gmail_oauth_start.php](gmail_oauth_start.php) in your browser:
   - http://localhost/natconInvitations1/gmail_oauth_start.php
2. Sign in with the Gmail account that matches GOOGLE_SENDER_EMAIL.
3. Allow access. The app will store the token at GMAIL_TOKEN_PATH.

## 4) Verify Token Storage

After successful authorization, confirm that the token exists at:

- storage/gmail_token.json

## 5) Common Issues

- "redirect_uri_mismatch": Confirm GOOGLE_REDIRECT_URI exactly matches the authorized redirect URI in Google Cloud.
- "invalid_client": Re-check GOOGLE_CLIENT_ID and GOOGLE_CLIENT_SECRET.
- Missing token file: Ensure the [storage](storage) directory is writable by the web server.

## 6) Related Files

- OAuth flow start: [gmail_oauth_start.php](gmail_oauth_start.php)
- OAuth callback: [gmail_oauth_callback.php](gmail_oauth_callback.php)
- Gmail API usage: [gmail_api.php](gmail_api.php)

## 7) Cron Jobs

These scripts support queue sending and reply status updates:

- Queue processor: [cron/process_queue.php](cron/process_queue.php)
- Reply checker: [cron/check_replies.php](cron/check_replies.php)
- Status updater: [cron/update_status.php](cron/update_status.php)

Example crontab (every minute):

```
* * * * * php /path/to/project/cron/process_queue.php
* * * * * php /path/to/project/cron/check_replies.php
* * * * * php /path/to/project/cron/update_status.php
```
