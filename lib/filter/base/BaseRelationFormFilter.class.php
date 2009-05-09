<?php

require_once(sfConfig::get('sf_lib_dir').'/filter/base/BaseFormFilterPropel.class.php');

/**
 * Relation filter form base class.
 *
 * @package    peerfollow
 * @subpackage filter
 * @author     Your name here
 * @version    SVN: $Id: sfPropelFormFilterGeneratedTemplate.php 16976 2009-04-04 12:47:44Z fabien $
 */
class BaseRelationFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'person_id'    => new sfWidgetFormPropelChoice(array('model' => 'Person', 'add_empty' => true)),
      'following_id' => new sfWidgetFormPropelChoice(array('model' => 'Person', 'add_empty' => true)),
    ));

    $this->setValidators(array(
      'person_id'    => new sfValidatorPropelChoice(array('required' => false, 'model' => 'Person', 'column' => 'id')),
      'following_id' => new sfValidatorPropelChoice(array('required' => false, 'model' => 'Person', 'column' => 'id')),
    ));

    $this->widgetSchema->setNameFormat('relation_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'Relation';
  }

  public function getFields()
  {
    return array(
      'person_id'    => 'ForeignKey',
      'following_id' => 'ForeignKey',
      'id'           => 'Number',
    );
  }
}
