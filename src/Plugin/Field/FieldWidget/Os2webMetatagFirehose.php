<?php

namespace Drupal\os2web_hjemmeside\Plugin\Field\FieldWidget;

use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\metatag\Plugin\Field\FieldWidget\MetatagFirehose;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\metatag\MetatagManagerInterface;
use Drupal\metatag\MetatagTagPluginManager;
use Drupal\Core\Config\ConfigFactoryInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * OS2Web Advanced widget for metatag field.
 *
 * @FieldWidget(
 *   id = "os2web_metatag_firehose",
 *   label = @Translation("OS2Web Advanced meta tags form"),
 *   field_types = {
 *     "metatag"
 *   }
 * )
 */
class Os2webMetatagFirehose extends MetatagFirehose {

  /**
   * The module handler.
   *
   * @var \Drupal\Core\Extension\ModuleHandlerInterface
   */
  protected $moduleHandler;

  /**
   * {@inheritdoc}
   */
  public function __construct($plugin_id, $plugin_definition, FieldDefinitionInterface $field_definition, array $settings, array $third_party_settings, MetatagManagerInterface $manager, MetatagTagPluginManager $plugin_manager, ConfigFactoryInterface $config_factory, ModuleHandlerInterface $moduleHandler) {
    parent::__construct($plugin_id, $plugin_definition, $field_definition, $settings, $third_party_settings, $manager, $plugin_manager, $config_factory);
    $this->moduleHandler = $moduleHandler;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $plugin_id,
      $plugin_definition,
      $configuration['field_definition'],
      $configuration['settings'],
      $configuration['third_party_settings'],
      $container->get('metatag.manager'),
      $container->get('plugin.manager.metatag.tag'),
      $container->get('config.factory'),
      $container->get('module_handler')
    );
  }

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return [
      'description_maxlength' => 0,
      'hide_basic_elements' => [
        'description' => FALSE,
        'abstract' => FALSE,
        'keywords' => FALSE,
      ],
    ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $element = parent::settingsForm($form, $form_state);
    $hide_basic_elements = $this->getSetting('hide_basic_elements');
    $options = [];
    $default = $this->defaultSettings();
    foreach ($default['hide_basic_elements'] as $element_name => $value) {
      $options[$element_name] = $this->t('Hide @metatag_name', ['@metatag_name' => $element_name]);
    }
    $element['hide_basic_elements'] = [
      '#type' => 'checkboxes',
      '#title' => $this->t('Hide basic metatag on form'),
      '#options' => $options,
      '#default_value' => $hide_basic_elements,
    ];

    $element['description_maxlength'] = [
      '#type' => 'number',
      '#title' => $this->t('Description max length'),
      '#default_value' => $this->getSetting('description_maxlength'),
    ];
    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = parent::settingsSummary();
    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $element = parent::formElement($items, $delta, $element, $form, $form_state);
    $hide_basic_elements = $this->getSetting('hide_basic_elements');
    foreach ($hide_basic_elements as $key => $hide) {
      if ($hide) {
        $element['basic'][$key]['#access'] = FALSE;
      }
    }

    $maxlength = $this->getSetting('description_maxlength') ? $this->getSetting('description_maxlength') : FALSE;
    if ($maxlength && $this->moduleHandler->moduleExists('textfield_counter')) {
      $element_description = &$element['basic']['description'];
      $position = 'after';
      $entity = $items->getEntity();
      $keys = [$entity->getEntityTypeId(), 'metatag'];
      $keys[] = $entity->id() ? $entity->id() : 0;
      $key = implode('-', $keys);
      $element_description['#textfield-maxlength'] = $maxlength;
      $element_description['#attributes']['class'][] = $key;
      $element_description['#attributes']['class'][] = 'textfield-counter-element';
      $field_definition_id = "{$entity->getEntityTypeId()}--{$entity->bundle()}--{$items->getFieldDefinition()->getName()}";
      $element_description['#attributes']['data-field-definition-id'] = $field_definition_id;
      $element_description['#attached']['library'][] = 'textfield_counter/counter';
      $element_description['#attached']['drupalSettings']['textfieldCounter'][$key]['key'][] = $key;
      $element_description['#attached']['drupalSettings']['textfieldCounter'][$key]['maxlength'] = (int) $maxlength;
      $element_description['#attached']['drupalSettings']['textfieldCounter'][$key]['counterPosition'] = $position;
      $element_description['#attached']['drupalSettings']['textfieldCounter'][$key]['textCountStatusMessage'] = $this->t('Maxlength: <span class="maxlength_count">@maxlength</span><br />Used: <span class="current_count">@current_length</span><br />Remaining: <span class="remaining_count">@remaining_count</span>');
      $element_description['#attached']['drupalSettings']['textfieldCounter'][$key]['countHTMLCharacters'] = FALSE;
    }

    return $element;
  }

}
