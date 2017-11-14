<?php
/**
 * Guest Entries Notification plugin for Craft CMS
 *
 * Guest Entries Email Notification
 *
 * @author    Rodrigo Passos
 * @copyright Copyright (c) 2017 Rodrigo Passos
 * @link      https://hellodative.com/
 * @package   GuestEntriesNotification
 * @since     1.0.0
 */

namespace Craft;

class GuestEntriesNotificationPlugin extends BasePlugin
{
  /**
   * Called after the plugin class is instantiated; do any one-time initialization here such as hooks and events:
   *
   * craft()->on('entries.saveEntry', function(Event $event) {
   *    // ...
   * });
   *
   * or loading any third party Composer packages via:
   *
   * require_once __DIR__ . '/vendor/autoload.php';
   *
   * @return mixed
   */
  public function init()
  {
    parent::init();

    craft()->on('guestEntries.onSuccess', function(GuestEntriesEvent $event) {
      // get entry object
      $entryModel = $event->params['entry'];
      $sectionId = $entryModel['attributes']['sectionId'];

      $notifications = array();

      // Grab enabled section notifications
      foreach ($this->getSettings()->notifications as $notification) {
        $notification = (object) $notification;
        if ( $notification->section == $sectionId && $notification->isEnabled == '1' ) {
          array_push($notifications, $notification);
        }
      }

      foreach ($notifications as $notification) {

        // Create the EmailModel
        $emailNotification = new EmailModel();
        // Explode email array
        $recipientList = array_map( 
          function($email) {
            $email = trim($email);
            return filter_var($email, FILTER_VALIDATE_EMAIL)
                   ? $email
                   : FALSE;
          }, 
          explode(',', $notification->emails)
        );

        // Parse subject
        $emailNotification->subject = craft()->templates->renderString(
          $notification->subject,
          array(
            "entry" => $entryModel
          )
        );
        
        // Check if template path exist
        if( craft()->templates->findTemplate( $notification->templatePath ) ) {
          // render notification template
          $body = craft()->templates->render($notification->templatePath, array(
            'entry' => $entryModel
          ));

          // Set based on email format
          if( $notification->emailFormat == 'text') {
            $emailNotification->body = $body;
          } else {
            $emailNotification->htmlBody = $body;
          }

          foreach ($recipientList as $recipient) {
            try
            {
              $emailNotification->toEmail = $recipient;
        
              // Send the email
              craft()->email->sendEmail( $emailNotification );
            }
            catch ( \Exception $e )
            {
              GuestEntriesNotificationPlugin::log(
                Craft::t("Can't send notification to recipient: ") . $recipient,
                LogLevel::Error
              );
              return false;
            }
          }
          
        } else {
          GuestEntriesNotificationPlugin::log(
            Craft::t("Can't find template: ") . $notification->templatePath,
            LogLevel::Error
          );
          // Skip this notification
          continue;
        }
      }
    });
  }

  /**
   * Returns the user-facing name.
   *
   * @return mixed
   */
  public function getName()
  {
        return Craft::t('Guest Entries Notification');
  }

  /**
   * Plugins can have descriptions of themselves displayed on the Plugins page by adding a getDescription() method
   * on the primary plugin class:
   *
   * @return mixed
   */
  public function getDescription()
  {
      return Craft::t('Guest Entries Email Notification');
  }

  /**
   * Plugins can have links to their documentation on the Plugins page by adding a getDocumentationUrl() method on
   * the primary plugin class:
   *
   * @return string
   */
  public function getDocumentationUrl()
  {
      return 'https://github.com/dative/guestentriesnotification/blob/master/README.md';
  }

  /**
   * Plugins can now take part in Craft’s update notifications, and display release notes on the Updates page, by
   * providing a JSON feed that describes new releases, and adding a getReleaseFeedUrl() method on the primary
   * plugin class.
   *
   * @return string
   */
  public function getReleaseFeedUrl()
  {
      return 'https://raw.githubusercontent.com/dative/guestentriesnotification/master/releases.json';
  }

  /**
   * Returns the version number.
   *
   * @return string
   */
  public function getVersion()
  {
      return '1.0.0';
  }

  /**
   * As of Craft 2.5, Craft no longer takes the whole site down every time a plugin’s version number changes, in
   * case there are any new migrations that need to be run. Instead plugins must explicitly tell Craft that they
   * have new migrations by returning a new (higher) schema version number with a getSchemaVersion() method on
   * their primary plugin class:
   *
   * @return string
   */
  public function getSchemaVersion()
  {
      return '1.0.0';
  }

  /**
   * Returns the developer’s name.
   *
   * @return string
   */
  public function getDeveloper()
  {
      return 'Rodrigo Passos';
  }

  /**
   * Returns the developer’s website URL.
   *
   * @return string
   */
  public function getDeveloperUrl()
  {
      return 'https://hellodative.com/';
  }

  /**
   * Returns whether the plugin should get its own tab in the CP header.
   *
   * @return bool
   */
  public function hasCpSection()
  {
      return false;
  }

  /**
   * Called right before your plugin’s row gets stored in the plugins database table, and tables have been created
   * for it based on its records.
   */
  public function onBeforeInstall()
  {
  }

  /**
   * Called right after your plugin’s row has been stored in the plugins database table, and tables have been
   * created for it based on its records.
   */
  public function onAfterInstall()
  {
  }

  /**
   * Called right before your plugin’s record-based tables have been deleted, and its row in the plugins table
   * has been deleted.
   */
  public function onBeforeUninstall()
  {
  }

  /**
   * Called right after your plugin’s record-based tables have been deleted, and its row in the plugins table
   * has been deleted.
   */
  public function onAfterUninstall()
  {
  }

  /**
   * Defines the attributes that model your plugin’s available settings.
   *
   * @return GuestEntriesNotificationModel
   */
  protected function getSettingsModel()
  {
    return new GuestEntriesNotificationModel();
  }

  /**
   * Returns the HTML that displays your plugin’s settings.
   *
   * @return mixed
   */
  public function getSettingsHtml()
  {
    // Check if Guest Entries is in place
    $guestEntriesPlugin = craft()->plugins->getPlugin('guestentries', true);

    if ($guestEntriesPlugin == NULL) {
      return craft()->templates->render('guestentriesnotification/setup_error', array(
        'errorMsg' => 'You need to setup Guest Entries Plugin before you proceed.'
      ));
    }

    $guestEntriesSettings = $guestEntriesPlugin->getSettings();

    // Create array if guest entries enabled sections
    $sections = array();

    // Loop throught sections that have guest entries enabled
    foreach ($guestEntriesSettings->defaultAuthors as $key => $value) {
      if ($value != 'none') {
        // Create a key / value pair with sectionId -> sectionName
        $sections[craft()->sections->getSectionByHandle($key)->id] = craft()->sections->getSectionByHandle($key)->name;
      }
    }

    // No guest entries enabled sections
    if ( empty($sections) ) {
      return craft()->templates->render('guestentriesnotification/setup_error', array(
        'errorMsg' => 'There is no section set to use Guest Entries.'
      ));
    }
    
    $settings    = $this->getSettings();

    $emailFormatOptions = array(
      'text' => 'Plain Text',
      'html' => 'HTML'
    );

    return craft()->templates->render('guestentriesnotification/settings', array(
      'errors' => $settings->getErrors(),
      'notifications' => $settings->notifications,
      'sections' => $sections,
      'formats' => $emailFormatOptions
    ));
  }

  /**
   * If you need to do any processing on your settings’ post data before they’re saved to the database, you can
   * do it with the prepSettings() method:
   *
   * @param mixed $settings  The plugin's settings
   *
   * @return mixed
   */
  public function prepSettings($settings)
  {
      // Check if empty
      $notification = $settings ? $settings : array('notifications' => '');
      return $notification;
  }

}