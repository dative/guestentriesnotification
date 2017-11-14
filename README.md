![Logo](resources/icon.svg) 
# Guest Entries Notification plugin for Craft CMS ![Craft 2.5](https://img.shields.io/badge/craft-2.5-red.svg?style=flat-square) ![Craft 2.4](https://img.shields.io/badge/craft-2.4-red.svg?style=flat-square)

Extend Pixel &amp; Tonic&rsquo;s [Guest Entries (v1)](https://github.com/craftcms/guest-entries/tree/v1) plugin with email notifications.

**NOTE: this plugin requires the Guest Entries plugin to be installed and configured.**

## Installation

To install Guest Entries Notification, follow these steps:

1. Download & unzip the file and place the `guestentriesnotification` directory into your `craft/plugins` directory
2.  -OR- do a `git clone https://github.com/dative/guestentriesnotification.git` directly into your `craft/plugins` folder.  You can then update it with `git pull`
3.  -OR- install with Composer via `composer require dative/guestentriesnotification`
4. Install plugin in the Craft Control Panel under Settings > Plugins
5. The plugin folder should be named `guestentriesnotification` for Craft to see it.  GitHub recently started appending `-master` (the branch name) to the name of the folder for zip file downloads.

Guest Entries Notification works on Craft 2.4.x and Craft 2.5.x.

## Guest Entries Notification Overview

I've used the [wbrowar/Craft-Guest-Entries-Email-Notification](https://github.com/wbrowar/Craft-Guest-Entries-Email-Notification) in the past, but I felt it lack support for multiple notifications hooks for the same Guest Entry section, and the ability to use templates for the email body.

## Configuring Guest Entries Notification

Once you finish configuring Guest Entries, go to Guest Entries Notification settings:

![Screenshot](resources/screenshots/settings.png)

You can add as many notifications you like, by clicking `Add a notification` at the bottom of the table. You can configure the following settings:

| Setting | Description |
| --- | --- |
| **Enabled?** | Turn notification on / off |
| **Section** | Select which *Guest Entries* configured section you want to be notified of new entry. It will only display section that has a default author set. |
| **Emails** | ***Required*** You can enter one or multiple comma-separated email addresses. Each email address will be notified individualy, and in case of failure, the faulty email address will be logged in Craft's logs. |
| **Subject** | ***Required*** The email notification subject. The *entry* object is passed and can be used as such: `New Blog Entry: {{ entry.title }}` |
| **Template Path** | ***Required*** The template path to be used as the body of the email. The *entry* object is passed. |
| **Format** | Set the email format to either *Plain Text* or *HTML* |

## Guest Entries Notification Roadmap

Some things to do, and ideas for potential features:

* Allow email address to be supplied dynamically
* Improve settings validation that validates if email addresses are valid.

## Guest Entries Notification Changelog

### 1.0

* Initial Release

Brought to you by [Dative, Inc](https://hellodative.com/)
