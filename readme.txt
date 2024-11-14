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

== External services ==

This plugin connects to APIs of third parties to provide the list of its features. In all cases it does so when
the site administrator authorizes it. When it's authorized it saves the basic user data required to identify the account in the dashboard area.
The basic information include the username or for full name, the remote account ID, an access token, and its expiry date. This information is used
 to identify the account in the dashboard area and used to post the site content to those accounts when the user decides to share posts, and also
 when a schedule is created to do so.
For this reason we seek to make it clear which services and provide links to where their policies or terms of usage can be found.

They include:

= Rebrandly =
API link:
[API link](https://api.rebrandly.com/v1/links). The API used to create short urls for posting to social media accounts.

[Terms of Service](https://www.rebrandly.com/terms-conditions)

= X =
APIs links:
[API link version 2](https://api.linkedin.com/v2), An alternative version 2 api link used to authorize and posts contents
[API link version 1](https://upload.twitter.com/1.1) X version 1 API that is used to upload media files,
[API version 2 link](https://api.twitter.com/2) X version 2 API used to authorize and make text posts.,
[API version 2 root link](https://api.x.com/) API version 2 link, used to authorize and posts contents.
[API authorize link](https://twitter.com/i/oauth2/authorize) Used to create authorization link.
The terms of services for a developer and non-developer are [developer](https://developer.x.com/en/developer-terms/agreement-and-policy),
[general x user](https://x.com/en/tos). And here is X privacy [policy](https://x.com/en/privacy)

= Facebook =
API links:
[Facebook API link](https://graph.facebook.com/) - Used to authorize, retrieve user account(s) and post to these accounts.
[Facebook API authorization link](https://www.facebook.com/v20.0/dialog/oauth) - Used to create authorize link.
[Facebook API authorization link](https://graph.facebook.com/v20.0/oauth/access_token) - Used to retrieve user access token.
[Terms of Service](https://developers.facebook.com/terms/dfc_platform_terms/),
[Privacy Policy](https://developers.facebook.com/devpolicy/),

= Instagram =
API links: [API authorize link](https://www.instagram.com/oauth/authorize) - Use to create authorization link,
[API access token link](https://api.instagram.com/oauth/access_token) - Used to retrieve access token after user consents to log in dialog,
[API link](https://graph.instagram.com) - Used to retrieve accounts and posting to those accounts,
[Refresh token link](https://graph.instagram.com/refresh_access_token) - Used to refresh expired access tokens,
[API link](https://graph.instagram.com/v20.0/) - A version of the above API link.

[Terms of Service](https://developers.facebook.com/terms/dfc_platform_terms/).
[Privacy Policy for Devs](https://developers.facebook.com/devpolicy/)
[Privacy Policy](https://privacycenter.instagram.com/policy)

= LinkedIn =
API links:
[LinkedIn web address](https://www.linkedin.com/), The website address of LinkedIn
[API link](https://api.linkedin.com/), - The root API used to retrieve authorized accounts and posting to them.
[API link](https://api.linkedin.com/v2), - API version 2 of the above link.
[API link](https://www.linkedin.com/oauth/v2/authorization), - The root API used to authorize.

[Terms of Service](https://www.linkedin.com/legal/l/api-terms-of-use), Terms of using API.
[Privacy Policy](https://www.linkedin.com/legal/privacy-policy), A link to LinkedIn policy.

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

