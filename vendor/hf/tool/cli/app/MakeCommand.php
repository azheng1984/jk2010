<?php
class MakeCommand {
  private $content;
  public function execute() {
    $this->content = file_get_contents('config/make.config.php');
    $this->buildClassLoaderCache();
    $this->buildApplicationCache();
  }

  public function buildClassLoaderCache() {
    file_put_contents('cache/class_loader.cache.php', "<?php\nreturn ".var_export(array(0=>array(), 1=>array()), true));
  }

  public function buildApplicationCache() { //just for hf web application
    //scan app file and reflect class
    $dir = dir('app');
    foreach ($dir as $item) {
      //dispath item to processor like application
      //processor find "target" via suffix.
      //processors add thire own cache (extensible)
      //register processors in make.config.php
      $suffix = substr($item, -10);
      if ($suffix === 'Screen.php') {
        //add to view
      }
      if ($suffix === 'Action.php') {
        require $item;
        $tmp = explode('.', $item, 2);
        $reflector = new ReflectionClass($tmp[0]);
        //reflect method
      }
    }
    file_put_contents('cache/application.cache.php', "<?php\nreturn ".var_export(array(0=>array(), 1=>array()), true));
  }
}