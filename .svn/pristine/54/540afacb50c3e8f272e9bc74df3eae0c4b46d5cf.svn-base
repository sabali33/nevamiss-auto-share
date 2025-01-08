=== Nevamiss Auto Share ===

Contributors: sabali33
Donate link: https://wise.com/pay/me/eliasua
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl.html
Tags: Social Share,Facebook Share, Post, X Share, Schedules
Tested up to: 6.7
Stable tag: 1.0.0
Requires PHP: 8.0
Requires at least: 5.6

This plugin allows site users to auto-share their site content to authorized social media accounts.

== Description ==

This project is a WordPress plugin that allows site administrators to share their content to social media networks
It requires configuration and authorization of the supported social media networks.

### How to set it up ###

- Install this plugin at Dashboard > Plugins > Add New.
- Go to Auto Share > Settings > General > API Keys & Secrets the add API keys for the networks you intend to post to.
- Login to the configured networks at Auto Share > Settings > Network Accounts.
- That's all

### Features ###

- Instantly share post to social media accounts
- Create crons called schedules to share contents at selected times
- Support multiple login to different accounts on the same network.
- Re-order the order of sharing for schedule posts.
- Track shared posts for schedules
- Display upcoming posts and last shared posts
- URL shortnering

### Supported Social Media Networks ###

- Facebook
- X
- Linkedin
- Instagram

### Supported URL Shortners ###

- Rebrandly

== Frequently Asked Questions ==

= Do I need API keys for each network? =

Yes you need API keys for the supported networks.

= Am I going to pay for the API keys usage? =

For some networks such as Facebook, Instagram and X you need to subscribe to their products to be able to get
production API credentials. For LinkedIn probably no unless you are a heavy user.

= How long will my authorized account last? =

On average authorized remain active for a period of sixty days. There are some that never expire and you can confirm
that after login into a network in the expiry date column.

= Do you intend add more social media networks? =

Absolutely. We intend to add support for Flickr, SnapChat, TikTok, VK, Weibo, Xing and more.

= What are the next features coming up? =

- We intend to integrate AI to suggest captions for posts.

- Create a UI interface where site managers can post custom content based on a category of posts.

== External Services ==

This plugin connects to third-party APIs to provide its features. These connections occur only after explicit authorization by the site administrator. The plugin enables features such as posting content to social media accounts, creating short URLs, and retrieving user account details. Below is a comprehensive explanation of how the plugin interacts with these external services and ensures compliance with privacy and security standards.

### Data Collection and Handling
The plugin collects and processes the following data to enable integration with external services:
- **Usernames or full names**: Used for account identification.
- **Remote account IDs**: Required for managing external accounts via the plugin.
- **Access tokens and expiry dates**: Used for authentication and secure API communication.

This data is:
1. Stored securely in the WordPress database, encrypted when applicable.
2. Used solely to perform authorized actions, such as posting content or scheduling tasks.
3. Removed when the user revokes authorization or deletes the plugin.

The plugin does not collect or share any additional data beyond what is required to facilitate its features.

### User Consent and Privacy
- The plugin requires **explicit user authorization** before connecting to any external service. Users are presented with a consent screen detailing:
  - The purpose of the connection.
  - The data that will be shared.
  - Links to the external service’s privacy policy.
- No data is transmitted to third-party services without user consent.
- Users can revoke access at any time via the plugin’s settings. Upon revocation, all related data is securely deleted.

The plugin complies with global privacy regulations, including GDPR and CCPA. It minimizes data collection and ensures that users can exercise their rights to data access, correction, and deletion.

### Security Practices
- All API communications occur over secure HTTPS connections.
- Access tokens are stored encrypted and are not exposed to unauthorized users.
- Expired tokens are automatically removed, and users are prompted to reauthorize when necessary.
- The plugin implements error handling for API requests, including retries and logging failures in a secure log file.

### List of External Services
Below are the external services used by the plugin, their APIs, and links to their respective terms and policies:

#### Rebrandly
- **Purpose**: Short URL generation for social media posts.
- **API**: [API Link](https://api.rebrandly.com/v1/links)
- **Terms of Service**: [Rebrandly Terms](https://www.rebrandly.com/terms-conditions)
- **Privacy Policy**: [Rebrandly Privacy Policy](https://www.rebrandly.com/privacy-policy)

#### X (formerly Twitter)
- **Purpose**: Content posting, media upload, and account management.
- **APIs**:  
  - [Authorization](https://twitter.com/i/oauth2/authorize): Used for user login and token generation.  
  - [Media Upload](https://upload.twitter.com/1.1): Used for uploading media files.  
  - [Posting](https://api.twitter.com/2): Used for posting text-based content.  
- **Terms of Service**: [Developer Terms](https://developer.x.com/en/developer-terms/agreement-and-policy)  
- **Privacy Policy**: [X Privacy Policy](https://x.com/en/privacy)

#### Facebook
- **Purpose**: Content posting, account retrieval, and page management.
- **APIs**:  
  - [Authorization](https://www.facebook.com/v20.0/dialog/oauth): Used to initiate user login and consent.  
  - [Access Tokens](https://graph.facebook.com/v20.0/oauth/access_token): Used to retrieve access tokens after user consent.  
  - [Graph API](https://graph.facebook.com/): Used to manage user accounts and post content.  
- **Terms of Service**: [Facebook Platform Terms](https://developers.facebook.com/terms/dfc_platform_terms/)  
- **Privacy Policy**: [Facebook Privacy Policy](https://developers.facebook.com/devpolicy/)

#### Instagram
- **Purpose**: Content posting and account management.
- **APIs**:  
  - [Authorization](https://www.instagram.com/oauth/authorize): Used for user login and token generation.  
  - [Access Tokens](https://api.instagram.com/oauth/access_token): Used to retrieve access tokens.  
  - [Graph API](https://graph.instagram.com/): Used for posting content and retrieving account details.  
- **Terms of Service**: [Instagram Platform Terms](https://developers.facebook.com/terms/dfc_platform_terms/)  
- **Privacy Policies**:  
  - [Developer Privacy Policy](https://developers.facebook.com/devpolicy/)  
  - [General Instagram Privacy Policy](https://privacycenter.instagram.com/policy)

#### LinkedIn
- **Purpose**: Content posting and account management.
- **APIs**:  
  - [Authorization](https://www.linkedin.com/oauth/v2/authorization): Used to create login and consent flow.  
  - [API Root](https://api.linkedin.com/): Used for accessing user data and posting content.  
  - [Version 2 API](https://api.linkedin.com/v2): An updated version of the API.  
- **Terms of Service**: [LinkedIn API Terms](https://www.linkedin.com/legal/l/api-terms-of-use)  
- **Privacy Policy**: [LinkedIn Privacy Policy](https://www.linkedin.com/legal/privacy-policy)

---

### Plugin Privacy Policy
This plugin provides a privacy policy section in the plugin’s settings page, summarizing:
1. The data collected and its purpose.
2. The list of external services used.
3. User rights regarding data access, correction, and deletion.
4. A direct link to the plugin documentation.


### When Does the Plugin Communicate with These Services?
The plugin reaches out to the supported social media networks APIs in the following circumstances:
- When a user authenticates via their social media account to grant access.
- When posting content on behalf of the user (through schedules).
- When a user manually decides to instantly share a WordPress post to authorized social media accounts.
- Create a UI interface where site managers can post custom content based on a category of posts.

== Changelog ==

= 1.0.0 =

== Upgrade Notice ==

= 1.0.0 =

Initial release

== Screenshots ==
1. What has been shared and coming to share are displayed at screenshot-1.(png|jpg|jpeg|gif).
2. A list of created schedules are displayed in screenshot-2.(png|jpg|jpeg|gif)
3. A list of authorized accounts are displayed in screenshot-3.(png|jpg|jpeg|gif)
4. A list of database logs are displayed in screenshot-4.(png|jpg|jpeg|gif)

