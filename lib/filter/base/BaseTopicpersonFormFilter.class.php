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
      'person_id'  => new sfWidgetFormPropelChoice(array('model' => 'Person', 'add_empty' => true)),
      'topic_id'   => new sfWidgetFormPropelChoice(array('model' => 'Topic', 'add_empty' => true)),
      'rank'       => new sfWidgetFormFilterInput(),
      'followers'  => new sfWidgetFormFilterInput(),
      'following'  => new sfWidgetFormFilterInput(),
      'friends'    => new sfWidgetFormFilterInput(),
      'updated_at' => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => true)),
    ));

    $this->setValidators(array(
      'person_id'  => new sfValidatorPropelChoice(array('required' => false, 'model' => 'Person', 'column' => 'id')),
      'topic_id'   => new sfValidatorPropelChoice(array('required' => false, 'model' => 'Topic', 'column' => 'id')),
      'rank'       => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'followers'  => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'following'  => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'friends'    => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'updated_at' => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
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
      'person_id'  => 'ForeignKey',
      'topic_id'   => 'ForeignKey',
      'rank'       => 'Number',
      'followers'  => 'Number',
      'following'  => 'Number',
      'friends'    => 'Number',
      'updated_at' => 'Date',
      'id'         => 'Number',
    );
  }
}
