<?php

namespace Drupal\approval_codes\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Configure Approval codes settings for this site.
 */
class SettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'approval_codes_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['approval_codes.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('approval_codes.settings');
    $form['ap_code_delimeter'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Delimiter'),
      '#default_value' => $config->get('ap_code_delimeter'),
      '#width' => 1,
    ];
    $form['def_site_level'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Default site-level code'),
      '#default_value' => $config->get('def_site_level'),
    ];
    $form['def_content_level'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Default content-level code'),
      '#default_value' => $config->get('def_content_level'),
    ];
    $nids = \Drupal::entityQuery('node')
      ->condition('type', 'approval_codes_bundle')
      ->execute();
    $rows = [];
    if (count($nids)) {
      $nodes = \Drupal\node\Entity\Node::loadMultiple($nids);
      if (count($nodes)) {

        foreach ($nodes as $node) {
          $allowed_values = $node->getFieldDefinition('field_code_level')->getFieldStorageDefinition()->getSetting('allowed_values');
          $rows[] = [
            'code_level' => $allowed_values[$node->get('field_code_level')->value],
            'code' => $node->get('field_code')->value,
            'content_type' => $node->get('field_content_type')->value,
            'delete' => \Drupal\Core\Link::fromTextAndUrl($this->t('Delete'), $node->toUrl('delete-form'))->toString(),
            'edit' => \Drupal\Core\Link::fromTextAndUrl($this->t('Edit'), $node->toUrl('edit-form'))->toString(),
          ];
        }
      }
    }
    $form['add_new_code'] = [
      '#type' => 'link',
      '#title' => t('Add Approval code'),
      '#url' => \Drupal\Core\Url::fromUserInput('/node/add/approval_codes_bundle'),
      '#attributes' => ['class' => ['button']],
    ];

    $form['codes_list'] = [
      '#type' => 'table',
      '#header' => [
        'code_level' => 'Code level',
        'code' => 'Code',
        'content_type' => $this->t('Content type'),
        'delete' => '',
        'edit' => '',
      ],
      '#rows' => $rows,
      '#empty' => 'No codes found',
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    $delimiter = mb_strlen($form_state->getValue('ap_code_delimeter'));
    if (mb_strlen($form_state->getValue('ap_code_delimeter')) < 1) {
      $form_state->setErrorByName('ap_code_delimeter', $this->t('One or more symbols'));
    }
    if (preg_match('/^[\d\w]*$/', $form_state->getValue('ap_code_delimeter'))) {
      $form_state->setErrorByName('ap_code_delimeter', $this->t("Alphabetical and numeric not supported only .,| etc ($delimiter)"));
    }

    if (!preg_match('/^[A-Z]{4}\.([A-Z]{2,4}|\d{2,4})\.([A-Z]{2,4}|\d{2,4})\.([A-Z]{2,4}|\d{2,4})\.\d{4}$/', $form_state->getValue('def_site_level'))) {
      $form_state->setErrorByName('def_site_level', $this->t("Invalid format for code"));
    }

    if (!preg_match('/^[A-Z]{4}\.([A-Z]{2,4}|\d{2,4})\.([A-Z]{2,4}|\d{2,4})\.([A-Z]{2,4}|\d{2,4})\.\d{4}$/', $form_state->getValue('def_content_level'))) {
      $form_state->setErrorByName('def_content_level', $this->t("Invalid format for code"));
    }
    $nids = \Drupal::entityQuery('node')
      ->condition('type', 'approval_codes_bundle')
      ->condition('field_code',$form_state->getValue('def_content_level'))
      ->execute();
    if(count($nids)){
      $form_state->setErrorByName('def_content_level', $this->t("Code already exists"));
    }
    $nids = \Drupal::entityQuery('node')
      ->condition('type', 'approval_codes_bundle')
      ->condition('field_code',$form_state->getValue('def_site_level'))
      ->execute();
    if(count($nids)){
      $form_state->setErrorByName('def_site_level', $this->t("Code already exists"));
    }

    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->config('approval_codes.settings')
      ->set('ap_code_delimeter', mb_substr($form_state->getValue('ap_code_delimeter'), 0, 1))
      ->set('def_site_level', $form_state->getValue('def_site_level'))
      ->set('def_site_level', $form_state->getValue('def_content_level'))
      ->save();
    parent::submitForm($form, $form_state);
  }
}
