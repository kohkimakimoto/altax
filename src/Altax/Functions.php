<?php

/**
 * Register task.
 * @param unknown $name
 * @param unknown $options
 * @param unknown $callback
 */
function task()
{
  $name = null;
  $options = null;
  $callback = null;

  $args = func_get_args();
  if (count($args) < 2) {
    throw new Altax_Exception("Missing argument. function task() must 2 arguments at minimum.");
  }

  if (count($args) === 2) {
    $name = $args[0];
    $callback = $args[1];
  } else {
    $name = $args[0];
    $options = $args[1];
    $callback = $args[2];
  }

  $tasks = Altax_Config::get('tasks');
  if (!isset($tasks[$name])) {
    $tasks[$name] = array();
  }

  if (Altax_Config::get('desc')) {
    // If it has description, set it up to this task.
    $tasks[$name]['desc'] = Altax_Config::get('desc');
    Altax_Config::delete('desc');
  }

  $tasks[$name]['callback'] = $callback;
  $tasks[$name]['options']  = $options;
  Altax_Config::set('tasks', $tasks);
}

/**
 * Register host.
 * @param String          $host
 * @param Array           $options
 * @param Array or String $roles
 * @throws Altax_Exception
 */
function host()
{
  $host = null;
  $options = array();
  $roles = null;

  $args = func_get_args();
  if (count($args) < 2) {
    throw new Altax_Exception("Missing argument. function host() must 2 arguments at minimum.");
  }

  if (count($args) === 2) {
    $host = $args[0];
    $roles = $args[1];
  } else {
    $host = $args[0];
    $options = $args[1];
    $roles = $args[2];
  }

  $hosts = Altax_Config::get('hosts');

  $hosts[$host] = $options;

  if ($roles) {
    // Register related role
    if (is_string($roles)) {
      role($roles, $host);
    } else if (is_array($roles)) {
      foreach ($roles as $role) {
        role($role, $host);
      }
    }
  }

  Altax_Config::set('hosts', $hosts);
}

/**
 * Register role.
 * @param String           $role
 * @param Array or String  $hosts
 * @throws Altax_Exception
 */
function role($role, $hosts)
{
  if (is_string($hosts)) {
    $hosts = array($hosts);
  }

  $roles = Altax_Config::get('roles');

  foreach ($hosts as $host) {

    if (!isset($roles[$role])) {
      $roles[$role] = array();
    }
    $roles[$role][] = $host;
  }

  $roles[$role] = array_unique($roles[$role]);

  Altax_Config::set('roles', $roles);
}

function desc($desc)
{
  Altax_Config::set('desc', $desc);
}
/**
 * Run command
 * @param unknown $command
 */
function run($command, $options = array())
{
  $task = Altax::getInstance()->getTaskManager()->getCurrentTask();
  if ($task->isLocalRun()) {
    run_local($command, $options);
    return;
  }

  $sshcmd = null;
  $sshcmd = $task->getSSHCommandBase();
  $sshcmd .= ' "';

  if (isset($options['user'])) {
    $sshcmd .= " sudo -u".$options['user']." ";
  }

  $sshcmd .= "sh -c '";

  if (isset($options['cwd'])) {
    $sshcmd .= "cd ".$options['cwd']."; ";
  }

  $sshcmd .= $command;

  $sshcmd .= '\'"';

  $output = null;
  $ret = null;

  $host = $task->getHost();

  Altax_Logger::log("Running command: [$command]", "[$host]", "info");
  Altax_Logger::log("Running SSH command: [$sshcmd]", "[$host]", "debug");

  //
  // Get Pseudo-terminal used for temporary.
  //
  // In order to execute command that needs termial, SSH command uses -t option to get a pseudo-terminal.
  // But default pseudo-terminal is connectting other process as Altax Task.
  // the STDOUT of Altax Tasks put data into STDIN of other Altax task in parallel process.
  // It' bad to causes of errors.
  //
  // So, following code is to get Pseudo-terminal used for temporary.
  // this Pseudo-terminal is disconnected other terminal of parallel process
  //
  $descriptorspec = array(
    0 =>  array("file", '/dev/ptmx', 'r'),
  );

  $process = proc_open($sshcmd, $descriptorspec, $pipes);
  foreach ($pipes as $pipe) {
    fclose($pipe);
  }
  proc_close($process);
}

function run_local($command, $options = array())
{
  $task = Altax::getInstance()->getTaskManager()->getCurrentTask();

  $sshcmd = null;

  if (isset($options['user'])) {
    $sshcmd .= " sudo -u".$options['user']." ";
  }

  $sshcmd .= "sh -c '";

  if (isset($options['cwd'])) {
    $sshcmd .= "cd ".$options['cwd']."; ";
  }

  $sshcmd .= $command;

  $sshcmd .= '\'';

  $output = null;
  $ret = null;

  $host = $task->getHost();

  Altax_Logger::log("Local Executing Command [$sshcmd]", "[$host]", "debug");

  $descriptorspec = array();

  // Not Use SSH
  $process = proc_open($sshcmd, $descriptorspec, $pipes);
  foreach ($pipes as $pipe) {
    fclose($pipe);
  }
  proc_close($process);
}

function run_task($name, $arguments = array())
{
  $taskManager = Altax::getInstance()->getTaskManager();
  $currentTask = $taskManager->getCurrentTask()->getTask();
  $taskManager->executeTask($name, $arguments, $currentTask);
}

/**
 * Set a value in global configuration.
 * @param unknown $key
 * @param unknown $value
 */
function set($key, $value)
{
  Altax_config::set($key, $value);
}

/**
 * Get a value from global configuration.
 * @param unknown $key
 * @param string $default
 */
function get($key, $default = null)
{
  return Altax_config::get($key, $default);
}
