<?php
require_once 'Altax/Utils.php';
require_once 'Altax/Config.php';
require_once 'Altax/Exception.php';
require_once 'Altax/Logger.php';
require_once 'Altax/Task.php';
require_once 'Altax/TaskManager.php';

require_once 'Altax/Functions.php';


/**
 * Altax is a simple deployment tool.
 *
 * @author kohkimakimoto <kohki.makimoto@gmail.com>
 * @version $Revision$
 */
class Altax
{
  const VERSION = '1.1.0';

  protected $options;
  protected $arguments;
  protected $task;
  protected $taskManager;

  protected static $instance;

  public static function getInstance()
  {
    return self::$instance;
  }

  /**
   * Main method.
   */
  public static function main()
  {
    // Get options
    $options = getopt("dTtlhcf:");
    $argv = $_SERVER['argv'];
    $raw_arguments = $argv;

    // Remove program name.
    if (isset($raw_arguments[0])) {
      array_shift($raw_arguments);
    }

    // Process arguments
    $arguments = array();
    $i = 0;
    while ($raw_argument = array_shift($raw_arguments)) {
      if ('-' == substr($raw_argument, 0, 1)) {

      } else {
        if ($argv[$i] !== '-f') {
          $arguments[] = $raw_argument;
        }
      }
      $i++;
    }
    $task = array_shift($arguments);

    // Run Altax.
    self::$instance = new Altax();
    self::$instance->execute($task, $options, $arguments);

  }

  /**
   * Execute.
   * @param unknown $task
   * @param unknown $options
   */
  public function execute($task, $options, $arguments)
  {
    // Show help
    if (array_key_exists('h', $options)) {
      $this->usage();
      return;
    }

    if (count($options) === 0 && $task == null) {
      $this->usage();
      return;
    }

    $this->task = $task;
    $this->options = $options;
    $this->arguments = $arguments;

    // Load configuration file.
    Altax_Config::init(isset($this->options['f']) ? $this->options['f'] : null);
    if (isset($this->options['d'])) {
      Altax_Config::set('debug', true);
    }

    // Setup Built-in tasks
    $this->setupBuiltInTasks();

    if (array_key_exists('c', $options)) {
      $this->listConfig();
      return;
    }

    if (array_key_exists('l', $options)
        || array_key_exists('t', $options)
        || array_key_exists('T', $options)) {
      $this->listTask();
      return;
    }

    try {

      // Checks to exists a ssh command.
      exec("which ssh 2>&1", $output, $ret);
      if ($ret != 0) {
        throw new Altax_Exception("SSH command is not found.");
      }

      Altax_Logger::log("*** Altax version ".Altax::VERSION." ***");

      $this->taskManager = new Altax_TaskManager();
      $this->taskManager->executeTask($this->task, $this->arguments);

    } catch (Altax_Exception $e) {

      if (Altax_Config::get('debug')) {
        fputs(STDERR, $e);
      } else {
        fputs(STDERR, $e->getMessage()."\n");
      }

      exit(1);
    }
  }

  /**
   * Output usage.
   */
  protected function usage()
  {
    echo "\n";
    echo "Altax is a simple deployment tool running SSH in parallel.\n";
    echo "\n";
    echo "Altax version ".Altax::VERSION."\n";
    echo "Copyright (c) Kohki Makimoto <kohki.makimoto@gmail.com>\n";
    echo "Apache License 2.0\n";
    echo "\n";
    echo "Usage:\n";
    echo "  altax [-d|-h|-f|-l|-c] TASK [ARGS..]\n";
    echo "\n";
    echo "Options:\n";
    echo "  -d         : Switch the debug mode to output log on the debug level.\n";
    echo "  -h         : List available command line options (this page).\n";
    echo "  -f=FILE    : Specify to load configuration file (default altax.php).\n";
    echo "  -l         : List available tasks.\n";
    echo "  -c         : List configurations.\n";
    echo "\n";
    echo "Built-in tasks:\n";
    echo "  init       : Create default configuration file (altax.php).\n";
    echo "\n";
  }

  /**
   * List config
   */
  public function listConfig()
  {
    $largestLength = Altax_Utils::arrayKeyLargestLength(Altax_Config::getAllOnFlatArray());
    echo "\n";
    echo "Configurations :\n";
    foreach (Altax_Config::getAllOnFlatArray() as $key => $val) {
      if ($largestLength === strlen($key)) {
        $sepalator = str_repeat(" ", 0);
      } else {
        $sepalator = str_repeat(" ", $largestLength - strlen($key));
      }

      echo "  [".$key."] ";
      echo $sepalator;
      if (is_callable($val)) {
        echo "=> function()\n";
      } else if (is_array($val)) {
        echo "=> array()\n";
      } else {
        echo "=> ".$val."\n";
      }
    }
    echo "\n";
  }

  /**
   * Lists tasks
   */
  public function listTask()
  {
    $largestLength = Altax_Utils::arrayKeyLargestLength(Altax_Config::get('tasks'));

    echo "\n";
    echo "Available tasks :\n";
    foreach (Altax_Config::get('tasks') as $key => $task) {
      if ($largestLength === strlen($key)) {
        $sepalator = str_repeat(" ", 0);
      } else {
        $sepalator = str_repeat(" ", $largestLength - strlen($key));
      }

      echo "  $key";
      echo $sepalator;
      if (@$task['desc']) {
        echo "  : ".$task['desc'];
      } else {
        echo "  : ";
      }
      echo "\n";
    }
    echo "\n";
  }

  public function getTask()
  {
    return $this->task;
  }

  public function getTaskManager()
  {
    return $this->taskManager;
  }

  protected function setupBuiltInTasks()
  {
    desc('Create default configuration file ('.Altax_Config::DEFAULT_CONFIG.').');
    task('init', function($host, $args){
      $cwd = getcwd();
      $configFile = $cwd."/".Altax_Config::DEFAULT_CONFIG;
      if (file_exists($configFile)) {
        throw new Altax_Exception("File $configFile already exists.");
      }

      $content = "";
      $content = "\x3c\x3fphp\n";
      $content .= "/**\n";
      $content .= " * Altax Configurations.\n";
      $content .= " *\n";
      $content .= " * You need to modify this file for your environment.\n";
      $content .= " *\n";
      $content .= " * @see https://github.com/kohkimakimoto/altax/wiki/Reference-Configurations\n";
      $content .= " * @author yourname <youremail@yourcompany.com>\n";
      $content .= " */\n";
      $content .= "\n";
      $content .= "//\n";
      $content .= "// The Following is hosts and roles settings to deploy.\n";
      $content .= "//\n";
      $content .= "role('web', '127.0.0.1');\n";
      $content .= "\n";
      $content .= "// or\n";
      $content .= "\n";
      $content .= "// role('web', array('192.168.0.1', '192.168.0.2'));\n";
      $content .= "\n";
      $content .= "// or\n";
      $content .= "\n";
      $content .= "// host('192.168.0.1', 'web');\n";
      $content .= "// host('192.168.0.2', 'web');\n";
      $content .= "\n";
      $content .= "// or (Specify SSH Configurations) \n";
      $content .= "\n";
      $content .= "// host('192.168.0.2', array('port' => '22', 'login_name' => 'yourname', 'identity_file' => '/home/yourname/.ssh/id_rsa'), 'web');\n";
      $content .= "\n";
      $content .= "\n";
      $content .= "//\n";
      $content .= "// The Following is task definitions.\n";
      $content .= "//\n";
      $content .= "desc('This is a sample task.');\n";
      $content .= "task('sample',array('roles' => 'web'), function(\$host, \$args){\n";
      $content .= "\n";
      $content .= "  run('echo Hellow World!');\n";
      $content .= "\n";
      $content .= "});\n";
      $content .= "\n";

      file_put_contents($configFile, $content);
      echo "Create $configFile\n";
    });
  }


  public function getOptions()
  {
    return $this->options;
  }


}