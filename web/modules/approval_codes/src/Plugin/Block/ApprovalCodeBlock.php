<?php

namespace Drupal\approval_codes\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides an example block.
 *
 * @Block(
 *   id = "approval_codes_example",
 *   admin_label = @Translation("Approval code block"),
 *   category = @Translation("Approval codes")
 * )
 */
class ApprovalCodeBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {

    $build['content'] = [
      '#markup' => \Drupal::service('approval_codes.getcode')->Code(),

    ];
    $build['#cache']['max-age'] = 0;
    return $build;
  }

}
