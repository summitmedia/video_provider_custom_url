<?php

namespace Drupal\video_provider_custom_url;

use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Core\DependencyInjection\ServiceProviderBase;

/**
 * Overrides the video.provider_manager service.
 */
class VideoProviderCustomUrlServiceProvider extends ServiceProviderBase {

  /**
   * {@inheritdoc}
   */
  public function alter(ContainerBuilder $container) {
    $container->getDefinition('video.provider_manager')
      ->setClass(VideoProviderCustomUrlProviderManager::class);
  }

}
