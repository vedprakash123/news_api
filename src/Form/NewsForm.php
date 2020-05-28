<?php

namespace Drupal\news_api\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Form controller for News edit forms.
 *
 * @ingroup news_api
 */
class NewsForm extends ContentEntityForm {

  /**
   * The current user account.
   *
   * @var \Drupal\Core\Session\AccountProxyInterface
   */
  protected $account;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    // Instantiates this form class.
    $instance = parent::create($container);
    $instance->account = $container->get('current_user');
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    /* @var \Drupal\news_api\Entity\News $entity */
    $form = parent::buildForm($form, $form_state);

    return $form;
  }
  
    /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
      parent::validateForm($form, $form_state);
      $values = $form_state->getValues();
	  if(isset($values['expiry_date'][0]['value']) && !empty($values['expiry_date'][0]['value'])) {
		  if(strtotime($values['published_date'][0]['value']) >= strtotime($values['expiry_date'][0]['value'])) {
			$form_state->setErrorByName('expiry_date][0][value', $this->t('Expiry date should be greater than the published date.'));  
		  }
	  }
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $entity = $this->entity;

    $status = parent::save($form, $form_state);

    switch ($status) {
      case SAVED_NEW:
        $this->messenger()->addMessage($this->t('Created the %label News.', [
          '%label' => $entity->label(),
        ]));
        break;

      default:
        $this->messenger()->addMessage($this->t('Saved the %label News.', [
          '%label' => $entity->label(),
        ]));
    }
    $form_state->setRedirect('entity.news.canonical', ['news' => $entity->id()]);
  }

}
