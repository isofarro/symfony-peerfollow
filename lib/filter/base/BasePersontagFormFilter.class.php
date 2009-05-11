<?php

require_once(sfConfig::get('sf_lib_dir').'/filter/base/BaseFormFilterPropel.class.php');

/**
 * Persontag filter form base class.
 *
 * @package    peerfollow
 * @subpackage filter
 * @author     Your name here
 * @version    SVN: $Id: sfPropelFormFilterGeneratedTemplate.php 15484 2009-02-13 13:13:51Z fabien $
 */
class BasePersontagFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'person_id' => new sfWidgetFormPropelChoice(array('model' => 'Person', 'add_empty' => true)),
      'tag_id'    => new sfWidgetFormPropelChoice(array('model' => 'Tag', 'add_empty' => true)),
    ));

    $this->setValidators(array(
      'person_id' => new sfValidatorPropelChoice(array('required' => false, 'model' => 'Person', 'column' => 'id')),
      'tag_id'    => new sfValidatorPropelChoice(array('required' => false, 'model' => 'Tag', 'column' => 'id')),
    ));

    $this->widgetSchema->setNameFormat('persontag_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'Persontag';
  }

  public function getFields()
  {
    return array(
      'person_id' => 'ForeignKey',
      'tag_id'    => 'ForeignKey',
      'id'        => 'Number',
    );
  }
}
