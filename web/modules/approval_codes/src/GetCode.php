<?php

namespace Drupal\approval_codes;

/**
 * Getcode service.
 */
class GetCode {

  /**
   *
   * @return string
   */
  public function Code() {
    $code = '';
    $config = \Drupal::config('approval_codes.settings');
    $code = $config->get('def_site_level');
    if ($node = \Drupal::routeMatch()->getParameter('node')) {
      $nodeType = $node->bundle();
      $nids = \Drupal::entityQuery('node')
        ->condition('type', 'approval_codes_bundle')
        ->condition('field_content_type', $nodeType)
        ->condition('field_code_level', 2) // content level
        ->condition('status', 1)
        ->execute();
      if($nodes = \Drupal\node\Entity\Node::loadMultiple($nids)){
        $node = reset($nodes);
        $code = $node->field_code->value;
      }
      else{
        $code = $config->get('def_content_level');
      }
    }
    else {
      $nids = \Drupal::entityQuery('node')
        ->condition('type', 'approval_codes_bundle')
        ->condition('field_code_level', 1) // site level
        ->condition('status', 1)
        ->execute();
      if($nodes = \Drupal\node\Entity\Node::loadMultiple($nids)){
        $node = reset($nodes);
        $code = $node->field_code->value;
      }
    }
    return $code;
  }
}
