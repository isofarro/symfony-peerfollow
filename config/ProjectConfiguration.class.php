<?php
if (file_exists('/usr/share/php/symfony/')) {
	require_once '/usr/share/php/symfony/autoload/sfCoreAutoload.class.php';
} else {
	require_once '/usr/share/pear/symfony/autoload/sfCoreAutoload.class.php';
}
sfCoreAutoload::register();

class ProjectConfiguration extends sfProjectConfiguration
{
  public function setup()
  {
    // for compatibility / remove and enable only the plugins you want
    $this->enableAllPluginsExcept(array('sfDoctrinePlugin', 'sfCompat10Plugin'));
  }
}
