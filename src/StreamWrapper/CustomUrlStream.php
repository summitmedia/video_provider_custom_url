<?php

namespace Drupal\video_provider_custom_url\StreamWrapper;

use Drupal\video\StreamWrapper\VideoRemoteStreamWrapper;

/**
 * Defines a CustomUrl (customurl://) stream wrapper class.
 */
class CustomUrlStream extends VideoRemoteStreamWrapper {

  /**
   * {@inheritdoc}
   */
  public function getName() {
    return $this->t('Custom Url player');
  }

  /**
   * {@inheritdoc}
   */
  public function getDescription() {
    return $this->t('Video served in Custom Url.');
  }

  /**
   * {@inheritdoc}
   */
  public function getExternalUrl() {
    return '//' . $this->getTarget();
  }

}
