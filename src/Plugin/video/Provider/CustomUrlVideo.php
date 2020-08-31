<?php

namespace Drupal\video_provider_custom_url\Plugin\video\Provider;

use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\Url;
use Drupal\video\ProviderPluginBase;

/**
 * A custom url provider plugin.
 *
 * Regexp url validator was taking from here
 * https://daringfireball.net/2010/07/improved_regex_for_matching_urls.
 *
 * @VideoEmbeddableProvider(
 *   id = "customurl",
 *   label = @Translation("Сustom video url"),
 *   description = @Translation("Custom url video provider"),
 *   regular_expressions = {
 *     "/\b(?:(?:https?):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i"
 *   },
 *   mimetype = "video/customurl",
 *   stream_wrapper = "customurl"
 * )
 */
class CustomUrlVideo extends ProviderPluginBase {

  use StringTranslationTrait;

  /**
   * Provides helpers to operate on files and stream wrappers.
   *
   * @var \Drupal\file\FileStorage
   */
  protected $fileStorage;

  /**
   * Provides helpers to cache remoteThumbnailUri string.
   *
   * @var string
   */
  protected $remoteThumbnailUri;

  /**
   * {@inheritdoc}
   */
  public function renderEmbedCode($settings) {
    $image_thumbnail_uri = $this->getRemoteThumbnailUri();
    $anchor = $this->t('Watch the video');
    if (!empty($image_thumbnail_uri)) {
      $anchor = [
        '#theme' => 'image_style',
        '#style_name' => 'video_thumbnail',
        '#uri' => $image_thumbnail_uri,
      ];
    }

    $output['container'] = [
      '#type' => 'container',
      '#attributes' => [
        'class' => 'video-provider-custom-url-container',
      ],
    ];

    $output['container']['link'] = [
      '#type' => 'link',
      '#attributes' => ['target' => '_blank', 'class' => 'video-link'],
      '#title' => $anchor,
      '#url' => Url::fromUri($this->getVideoMetadata()['uri']),
    ];

    $output['#attached']['library'][] = 'video_provider_custom_url/video_provider_custom_url.video_thumbnail';

    return $output;
  }

  /**
   * {@inheritdoc}
   */
  public function getRemoteThumbnailUrl() {
    return file_create_url($this->getRemoteThumbnailUri());
  }

  /**
   * {@inheritdoc}
   */
  public function getLocalThumbnailUri() {
    $data = $this->getVideoMetadata();
    $ext = pathinfo($this->getRemoteThumbnailUri(), PATHINFO_EXTENSION);
    return $this->getUploadLocation() . '/' . $data['id'] . '.' . $ext;
  }

  /**
   * Helper function to get file storage service.
   *
   * @return \Drupal\Core\Entity\EntityStorageInterface|\Drupal\file\FileStorage
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  protected function getFileStorage() {
    if (!empty($this->fileStorage)) {
      return $this->fileStorage;
    }

    return \Drupal::entityTypeManager()->getStorage('file');
  }

  /**
   * Helper function to return image thumbnail URI.
   */
  protected function getRemoteThumbnailUri() {
    if (!empty($this->remoteThumbnailUri)) {
      return $this->remoteThumbnailUri;
    }

    $data = $this->getVideoMetadata();
    if (@empty($data['thumbnail']['target_id'])) {
      return NULL;
    }
    $data = $this->getVideoMetadata();
    $file = $this->getFileStorage()->load($data['thumbnail']['target_id']);

    return $file->getFileUri();
  }
}
