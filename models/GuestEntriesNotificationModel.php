<?php
/**
* Guest Entries Notification plugin for Craft CMS
*
* Guest Entries Notification Settings Model
*
* @author    Rodrigo Passos
* @copyright Copyright (c) 2017 Rodrigo Passos
* @link      https://hellodative.com/
* @package   GuestEntriesNotification
* @since     1.0.0
*/

namespace Craft;

class GuestEntriesNotificationModel extends BaseModel
{
  /**
  * Defines this model's attributes.
  *
  * @return array
  */
  protected function defineAttributes()
  {
    return array_merge(parent::defineAttributes(), array(
      'notifications'     => array(
        AttributeType::Mixed, 
        'default' => ''
      ),
    ));
  }

  public function validate($attributes = null, $clearErrors = true)
  {
    $validates = parent::validate($attributes, $clearErrors);

    if ($attributes === null || in_array('notifications', $attributes))
    {
      // Perform custom validation here
      $notifications = $this->getAttribute('notifications');

      if ( empty($notifications) ) {
        $validates = true;
      } else {
        foreach ($notifications as $i => $notification) {
          $errors = array();

          if ( $this->validateEmail($notification['emails'])) {
            $errors['emails'] = Craft::t('Emails is required');
          }

          if ( $this->validateSubject($notification['subject'])) {
            $errors['subject'] = Craft::t('Subject is required');
          }

          if ( $this->validateTemplatePath($notification['templatePath'])) {
            $errors['templatePath'] = Craft::t('Template Path is required');
          }

          if ( ! empty($errors) ) {
            $this->addError($i, $errors);
          }          
        }

        $validates = empty( $this->getErrors() );
      }
    }

    return $validates;
  }

  /**
   * Validate Emails
   *
   * @param String $emails
   * @return bool
   **/
  private function validateEmail($emails = '')
  {
    return empty($emails);
  }

  /**
   * Validate Template Path
   *
   * @param String $templatePath
   * @return bool
   **/
  private function validateTemplatePath($templatePath = '')
  {
    return empty($templatePath);
  }

  /**
   * Validate Subject
   *
   * @param String $templatePath
   * @return bool
   **/
  private function validateSubject($subject = '')
  {
    return empty($subject);
  }

  
  
}