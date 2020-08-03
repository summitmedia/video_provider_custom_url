<?php

namespace Drupal\video_provider_custom_url;

use Drupal\video\ProviderManager;

/**
 * Gathers the provider plugins.
 */
class VideoProviderCustomUrlProviderManager extends ProviderManager {

  /**
   * Const CUSTOMURL_TYPE_ID.
   */
  const CUSTOMURL_TYPE_ID = 'customurl';

  /**
   * {@inheritdoc}
   */
  public function loadApplicableDefinitionMatches(array $definitions, $user_input) {
    if (isset($definitions[self::CUSTOMURL_TYPE_ID])) {
      $customurl = $definitions[self::CUSTOMURL_TYPE_ID];
      unset($definitions[self::CUSTOMURL_TYPE_ID]);

      // Use custom url provider only if all others aren`t valid.
      if (parent::loadApplicableDefinitionMatches($definitions, $user_input) === FALSE) {
        $customurl_definition_matches = parent::loadApplicableDefinitionMatches([self::CUSTOMURL_TYPE_ID => $customurl], $user_input);
        if ($customurl_definition_matches === FALSE) {
          return FALSE;
        }
        if (empty($customurl_definition_matches['matches'][0])) {
          return FALSE;
        }
        // Change data keys.
        $uri = $customurl_definition_matches['matches'][0];
        unset($customurl_definition_matches['matches'][0]);
        $customurl_definition_matches['matches']['uri'] = $uri;
        $customurl_definition_matches['matches']['type'] = self::CUSTOMURL_TYPE_ID;

        // Set empty array value for consistency.
        // @see video_provider_custom_url_node_presave()
        $customurl_definition_matches['matches']['thumbnail'] = [];

        // Use URI hash as id.
        $customurl_definition_matches['matches']['id'] = hash('adler32', $uri);

        return $customurl_definition_matches;
      }
    }

    return parent::loadApplicableDefinitionMatches($definitions, $user_input);
  }

}
