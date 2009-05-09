<?php

require_once(sfConfig::get('sf_lib_dir').'/filter/base/BaseFormFilterPropel.class.php');

/**
 * Person filter form base class.
 *
 * @package    peerfollow
 * @subpackage filter
 * @author     Your name here
 * @version    SVN: $Id: sfPropelFormFilterGeneratedTemplate.php 16976 2009-04-04 12:47:44Z fabien $
 */
class BasePersonFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'username'     => new sfWidgetFormFilterInput(),
      'bio'          => new sfWidgetFormFilterInput(),
      'image'        => new sfWidgetFormFilterInput(),
      'no_followers' => new sfWidgetFormFilterInput(),
      'fullname'     => new sfWidgetFormFilterInput(),
      'website'      => new sfWidgetFormFilterInput(),
      'status'       => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'username'     => new sfValidatorPass(array('required' => false)),
      'bio'          => new sfValidatorPass(array('required' => false)),
      'image'        => new sfValidatorPass(array('required' => false)),
      'no_followers' => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'fullname'     => new sfValidatorPass(array('required' => false)),
      'website'      => new sfValidatorPass(array('required' => false)),
      'status'       => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('person_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'Person';
  }

  public function getFields()
  {
    return array(
      'id'           => 'Number',
      'username'     => 'Text',
      'bio'          => 'Text',
      'image'        => 'Text',
      'no_followers' => 'Number',
      'fullname'     => 'Text',
      'website'      => 'Text',
      'status'       => 'Text',
    );
  }
}
