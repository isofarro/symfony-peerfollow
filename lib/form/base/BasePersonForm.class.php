<?php

/**
 * Person form base class.
 *
 * @package    peerfollow
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfPropelFormGeneratedTemplate.php 15484 2009-02-13 13:13:51Z fabien $
 */
class BasePersonForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'           => new sfWidgetFormInputHidden(),
      'username'     => new sfWidgetFormInput(),
      'bio'          => new sfWidgetFormTextarea(),
      'image'        => new sfWidgetFormTextarea(),
      'no_followers' => new sfWidgetFormInput(),
      'fullname'     => new sfWidgetFormInput(),
      'website'      => new sfWidgetFormTextarea(),
      'status'       => new sfWidgetFormInput(),
    ));

    $this->setValidators(array(
      'id'           => new sfValidatorPropelChoice(array('model' => 'Person', 'column' => 'id', 'required' => false)),
      'username'     => new sfValidatorString(array('max_length' => 32)),
      'bio'          => new sfValidatorString(array('required' => false)),
      'image'        => new sfValidatorString(array('required' => false)),
      'no_followers' => new sfValidatorInteger(array('required' => false)),
      'fullname'     => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'website'      => new sfValidatorString(array('required' => false)),
      'status'       => new sfValidatorString(array('max_length' => 255)),
    ));

    $this->validatorSchema->setPostValidator(
      new sfValidatorPropelUnique(array('model' => 'Person', 'column' => array('username')))
    );

    $this->widgetSchema->setNameFormat('person[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'Person';
  }


}
