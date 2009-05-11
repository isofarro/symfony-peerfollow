<?php

require_once(sfConfig::get('sf_lib_dir').'/filter/base/BaseFormFilterPropel.class.php');

/**
 * Topicperson filter form base class.
 *
 * @package    peerfollow
 * @subpackage filter
 * @author     Your name here
 * @version    SVN: $Id: sfPropelFormFilterGeneratedTemplate.php 15484 2009-02-13 13:13:51Z fabien $
 */
class BaseTopicpersonFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'person_id' => new sfWidgetFormPropelChoice(array('model' => 'Person', 'add_empty' => true)),
      'topic_id'  => new sfWidgetFormPropelChoice(array('model' => 'Topic', 'add_empty' => true)),
    ));

    $this->setValidators(array(
      'person_id' => new sfValidatorPropelChoice(array('required' => false, 'model' => 'Person', 'column' => 'id')),
      'topic_id'  => new sfValidatorPropelChoice(array('required' => false, 'model' => 'Topic', 'column' => 'id')),
    ));

    $this->widgetSchema->setNameFormat('topicperson_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'Topicperson';
  }

  public function getFields()
  {
    return array(
      'person_id' => 'ForeignKey',
      'topic_id'  => 'ForeignKey',
      'id'        => 'Number',
    );
  }
}
