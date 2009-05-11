<?php

/**
 * Persontag form base class.
 *
 * @package    peerfollow
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfPropelFormGeneratedTemplate.php 15484 2009-02-13 13:13:51Z fabien $
 */
class BasePersontagForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'person_id' => new sfWidgetFormPropelChoice(array('model' => 'Person', 'add_empty' => false)),
      'tag_id'    => new sfWidgetFormPropelChoice(array('model' => 'Tag', 'add_empty' => false)),
      'id'        => new sfWidgetFormInputHidden(),
    ));

    $this->setValidators(array(
      'person_id' => new sfValidatorPropelChoice(array('model' => 'Person', 'column' => 'id')),
      'tag_id'    => new sfValidatorPropelChoice(array('model' => 'Tag', 'column' => 'id')),
      'id'        => new sfValidatorPropelChoice(array('model' => 'Persontag', 'column' => 'id', 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('persontag[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'Persontag';
  }


}
