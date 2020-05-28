<?php

namespace Drupal\news_api\Entity;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityPublishedTrait;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\user\UserInterface;

/**
 * Defines the News entity.
 *
 * @ingroup news_api
 *
 * @ContentEntityType(
 *   id = "news",
 *   label = @Translation("News"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\news_api\NewsListBuilder",
 *     "views_data" = "Drupal\news_api\Entity\NewsViewsData",
 *     "translation" = "Drupal\news_api\NewsTranslationHandler",
 *
 *     "form" = {
 *       "default" = "Drupal\news_api\Form\NewsForm",
 *       "add" = "Drupal\news_api\Form\NewsForm",
 *       "edit" = "Drupal\news_api\Form\NewsForm",
 *       "delete" = "Drupal\news_api\Form\NewsDeleteForm",
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\news_api\NewsHtmlRouteProvider",
 *     },
 *     "access" = "Drupal\news_api\NewsAccessControlHandler",
 *   },
 *   base_table = "news",
 *   data_table = "news_field_data",
 *   translatable = TRUE,
 *   admin_permission = "administer news entities",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "title",
 *     "uuid" = "uuid",
 *     "uid" = "user_id",
 *     "langcode" = "langcode",
 *     "published" = "status",
 *   },
 *   links = {
 *     "canonical" = "/article/news/{news}",
 *     "add-form" = "/article/news/add",
 *     "edit-form" = "/article/news/{news}/edit",
 *     "delete-form" = "/article/news/{news}/delete",
 *     "collection" = "/admin/news-article",
 *   },
 *   field_ui_base_route = "news.settings"
 * )
 */
class News extends ContentEntityBase implements NewsInterface {

  use EntityChangedTrait;
  use EntityPublishedTrait;

  /**
   * {@inheritdoc}
   */
  public static function preCreate(EntityStorageInterface $storage_controller, array &$values) {
    parent::preCreate($storage_controller, $values);
    $values += [
      'user_id' => \Drupal::currentUser()->id(),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getName() {
    return $this->get('title')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setName($name) {
    $this->set('title', $name);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getCreatedTime() {
    return $this->get('created')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setCreatedTime($timestamp) {
    $this->set('created', $timestamp);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getOwner() {
    return $this->get('user_id')->entity;
  }

  /**
   * {@inheritdoc}
   */
  public function getOwnerId() {
    return $this->get('user_id')->target_id;
  }

  /**
   * {@inheritdoc}
   */
  public function setOwnerId($uid) {
    $this->set('user_id', $uid);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function setOwner(UserInterface $account) {
    $this->set('user_id', $account->id());
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    // Add the published field.
    $fields += static::publishedBaseFieldDefinitions($entity_type);

    $fields['user_id'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Authored by'))
      ->setDescription(t('The user ID of author of the News entity.'))
      ->setRevisionable(TRUE)
      ->setSetting('target_type', 'user')
      ->setSetting('handler', 'default')
      ->setTranslatable(TRUE)
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'author',
        'weight' => 0,
      ])
      ->setDisplayOptions('form', [
        'type' => 'entity_reference_autocomplete',
        'weight' => 5,
        'settings' => [
          'match_operator' => 'CONTAINS',
          'size' => '60',
          'autocomplete_type' => 'tags',
          'placeholder' => '',
        ],
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['title'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Title'))
      ->setDescription(t('Enter the News Title.'))
      ->setSettings([
        'max_length' => 255,
        'text_processing' => 0,
      ])
      ->setDefaultValue('')
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'string',
        'weight' => -4,
      ])
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => -4,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE)
      ->setRequired(TRUE);
	
	$fields['description'] = BaseFieldDefinition::create('text_long')
	->setLabel(t('Description'))
	->setDescription(t('Enter the News Description.'))
	->setTranslatable(TRUE)->setSettings([
      'text_processing' => 0,
    ])
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'text_default',
        'weight' => -3,
        'width' => '400px',
      ])
      ->setDisplayOptions('form', [
        'type' => 'string_textarea',
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);
	  
      $fields['published_date'] = BaseFieldDefinition::create('datetime')
	->setLabel(t('Published date'))
	->setDescription(t('Published Date of the News Article.'))
	->setRequired(true)
	->setDisplayOptions('view', array(
		'label' => 'above',
		'type' => 'string',
		'weight' => -2,
	))
	->setDisplayOptions('form', array(
		'type' => 'date',
		'weight' => -2,
	))
	->setDisplayConfigurable('form', TRUE)
	->setDisplayConfigurable('view', TRUE);
	
      $fields['expiry_date'] = BaseFieldDefinition::create('datetime')
	->setLabel(t('Expiry date'))
	->setDescription(t('Expiry Date of the News Article.'))
	->setDisplayOptions('view', array(
		'label' => 'above',
		'type' => 'string',
		'weight' => -1,
	))
	->setDisplayOptions('form', array(
		'type' => 'date',
		'weight' => -1,
	))
	->setDisplayConfigurable('form', TRUE)
	->setDisplayConfigurable('view', TRUE);
			
    $fields['status']->setDescription(t('A boolean indicating whether the News is published.'))
      ->setDisplayOptions('form', [
        'type' => 'boolean_checkbox',
        'weight' => 1,
      ])->setDisplayConfigurable('form', TRUE);

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Created'))
      ->setDescription(t('The time that the entity was created.'));

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setDescription(t('The time that the entity was last edited.'));

    return $fields;
  }

}
