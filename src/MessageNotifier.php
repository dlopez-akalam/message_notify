<?php
/**
 * @file
 * Contains \Drupal\message_notify\MessageNotifier.
 */

namespace Drupal\message_notify;
use Drupal\Core\Plugin\DefaultPluginManager;
use Drupal\message\MessageInterface;
use Drupal\message_notify\Exception\MessageNotifyException;

/**
 * Prepare and send notifications.
 */
class MessageNotifier {

  /**
   * The notifier plugin manager.
   *
   * @var \Drupal\Core\Plugin\DefaultPluginManager
   */
  protected $notifierManager;

  /**
   * Constructs the message notifier.
   *
   * @param \Drupal\Core\Plugin\DefaultPluginManager $notifier_manager
   *   The notifier plugin manager.
   */
  public function __construct(DefaultPluginManager $notifier_manager) {
    $this->notifierManager = $notifier_manager;
  }

  /**
   * Process and send a message.
   *
   * @param \Drupal\message\MessageInterface $message
   *   The message entity being used for the notification.
   * @param array $options
   *   Array of options to override the plugin's default ones.
   * @param string $notifier_name
   *   Optional; The name of the notifier to use. Defaults to "email"
   *   sending method.
   *
   * @return bool Boolean value denoting success or failure of the notification.
   *   Boolean value denoting success or failure of the notification.
   *
   * @throws \Drupal\message_notify\Exception\MessageNotifyException
   *   If no matching notifier plugin exists.
   */
  public function send(MessageInterface $message, array $options = [], $notifier_name = 'email') {
    if (!$this->notifierManager->hasDefinition($notifier_name, FALSE)) {
      throw new MessageNotifyException('Could not send notification using the "' . $notifier_name . '" notifier.');
    }

    /** @var \Drupal\message_notify\Plugin\Notifier\MessageNotifierInterface $notifier */
    $notifier = $this->notifierManager->createInstance($notifier_name, $options);
    // @todo Can this be injected to the constructor?
    $notifier->init($message);

    if ($notifier->access()) {
      return $notifier->send();
    }
    // @todo Throw exception instead?
    return FALSE;
  }

}
