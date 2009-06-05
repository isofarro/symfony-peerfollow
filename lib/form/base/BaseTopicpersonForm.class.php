<?php

/**
 * Topicperson form base class.
 *
 * @package    peerfollow
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfPropelFormGeneratedTemplate.php 15484 2009-02-13 13:13:51Z fabien $
 */
class BaseTopicpersonForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'person_id'  => new sfWidgetFormPropelChoice(array('model' => 'Person', 'add_empty' => false)),
      'topic_id'   => new sfWidgetFormPropelChoice(array('model' => 'Topic', 'add_empty' => false)),
      'karma'      => new sfWidgetFormInput(),
      'rank'       => new sfWidgetFormInput(),
      'followers'  => new sfWidgetFormInput(),
      'following'  => new sfWidgetFormInput(),
      'friends'    => new sfWidgetFormInput(),
      'updated_at' => new sfWidgetFormDateTime(),
      'id'         => new sfWidgetFormInputHidden(),
    ));

    $this->setValidators(array(
      'person_id'  => new sfValidatorPropelChoice(array('model' => 'Person', 'column' => 'id')),
      'topic_id'   => new sfValidatorPropelChoice(array('model' => 'Topic', 'column' => 'id')),
      'karma'      => new sfValidatorInteger(array('required' => false)),
      'rank'       => new sfValidatorInteger(array('required' => false)),
      'followers'  => new sfValidatorInteger(array('required' => false)),
      'following'  => new sfValidatorInteger(array('required' => false)),
      'friends'    => new sfValidatorInteger(array('required' => false)),
      'updated_at' => new sfValidatorDateTime(array('required' => false)),
      'id'         => new sfValidatorPropelChoice(array('model' => 'Topicperson', 'column' => 'id', 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('topicperson[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'Topicperson';
  }


}
