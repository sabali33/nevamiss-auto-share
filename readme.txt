=== Nevamiss Auto Share ===

Contributors: sabali33
Donate link: https://wise.com/pay/me/eliasua
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl.html
Tags: Social Share,Facebook Share, Post, X Share, Schedules
Tested up to: 6.6
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
<<<<<<< HEAD
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
||||||| cdf05eb
- Create a UI interface where site managers can post custom content based on a category of posts.
=======
- Create a UI interface where site managers can post custom content based on a category of posts.

== Third Party Services ==

This plugin depends on APIs of third parties and for this reason we seek to make it clear which services
and provide links to where their terms of usage can be found. They include:
= Rebrandly =
API link: https://api.rebrandly.com/v1/links.
The terms of usage can be found [here](https://www.rebrandly.com/terms-conditions)

= X =
APIs links: https://api.linkedin.com/v2, https://upload.twitter.com/1.1, https://api.twitter.com/2, https://twitter.com/i/oauth2/authorize.
The terms of services for a developer and non-developer are [developer](https://developer.x.com/en/developer-terms/agreement-and-policy),
[general x user](https://x.com/en/tos). And here is X privacy [policy](https://x.com/en/privacy)

= Facebook =
API links: https://graph.facebook.com/
Terms of using this APIs can be read [here](https://developers.facebook.com/terms/dfc_platform_terms/).
A link to Facebook policy is [here](https://developers.facebook.com/devpolicy/)

= Instagram =
API links: https://www.instagram.com/oauth/authorize, https://api.instagram.com/oauth/access_token, https://graph.instagram.com
Terms of using this APIs can be read [here](https://developers.facebook.com/terms/dfc_platform_terms/).
A link to Meta policy is [here](https://developers.facebook.com/devpolicy/) and Instagram terms of use, this [link](https://privacycenter.instagram.com/policy)

= LinkedIn =
API links: https://www.linkedin.com/, https://api.linkedin.com/, https://graph.instagram.com
Terms of using this APIs can be read [here](https://www.linkedin.com/legal/l/api-terms-of-use).
A link to LinkedIn policy is [here](https://www.linkedin.com/legal/privacy-policy)
>>>>>>> main
