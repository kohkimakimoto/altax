<?php
/**
 * This class provides functions of Altax Tasks Management.
 *
 * @author kohkimakimoto <kohki.makimoto@gmail.com>
 * @version $Revision$
 */
class Altax_TaskManager
{
  protected $childPids = array();
  protected $currentTask;

  /**
   * Constractor.
   */
  public function __construct()
  {
  }

  /**
   * Execute Specifed task.
   * @param unknown $task
   * @throws Altax_Exception
   */
  public function executeTask($task, $arguments, $parentTask = null)
  {

    if (!$this->hasTask($task)) {
      throw new Altax_Exception("Task $task is not found.");
    }

    if ($parentTask !== null) {
      Altax_Logger::log("Executing task: [".$parentTask." -> ".$task."]");
    } else {
      Altax_Logger::log("Executing task: [".$task."]");
    }

    if (!function_exists('pcntl_fork')) {
      throw new Altax_Exception("Your PHP is not supported pcntl_fork function.");
    }

    // Get target hosts.
    $hosts = $this->getHosts($task);
    $localRun = false;
    if (count($hosts) === 0) {
      $localRun = true;
      $hosts = array('127.0 0.1');
      Altax_Logger::log("Running at the localhost only. This task dose not connect to remote servers.", "debug");
    }

    Altax_Logger::log("Processing to fork process.", "debug");
    Altax_Logger::log("Setup signal handler.", "debug");
    declare(ticks = 1);

    pcntl_signal(SIGTERM, array($this, "signalHander"));
    pcntl_signal(SIGINT, array($this, "signalHander"));

    // Fork process.
    foreach ($hosts as $host) {
      $pid = pcntl_fork();
      if ($pid === -1) {
        // Error
        throw new Altax_Exception("Fork Error.");
      } else if ($pid) {
        // Parent process
         $this->childPids[$pid] = $host;
      } else {
        // child process
        $this->currentTask = new Altax_Task($host, $task, $arguments, $localRun);
        $this->currentTask->execute();

        exit(0);
      }
    }

    // At the following code, only parent precess runs.
    while (count($this->childPids) > 0) {
      // Keep to wait until to finish all child processes.

      $pid = pcntl_wait($status);
      if (!$pid) {
        throw new Altax_Exception("Wait Error.");
      }

      if (!array_key_exists($pid, $this->childPids)) {
        throw new Altax_Exception("Wait Error.".$pid);
      }

      // As child process finished, removes managed child pid.
      $host = $this->childPids[$pid];
      unset($this->childPids[$pid]);

      Altax_Logger::log("[$host] Child process ".$pid." is completed.", "debug");
    }

    Altax_Logger::log("[$host] Altax process is completed.", "info");

  }

  /**
   * Get Target hosts.
   * @param unknown $task
   * @return unknown
   */
  protected function getHosts($task)
  {
    // Get target hosts
    $hosts = Altax_Config::get('tasks/'.$task.'/options/hosts', array());
    if (is_string($hosts)) {
      $hosts = array($hosts);
    }

    // Get target hosts from roles
    $roles = Altax_Config::get('tasks/'.$task.'/options/roles', array());
    if (is_string($roles)) {
      $roles = array($roles);
    }

    foreach ($roles as $role) {

      // Get hosts related the role.
      $rhosts = Altax_Config::get('roles/'.$role, array());
      $hosts = array_merge($hosts, $rhosts);
    }

    return array_unique($hosts);
  }

  protected function hasTask($task)
  {
    $tasks = Altax_Config::get('tasks');
    if (isset($tasks[$task])) {
      return true;
    } else {
      return false;
    }
  }

  public function signalHander($signo)
  {
    // TODO: Impliment.
    switch ($signo) {
      case SIGTERM:
        $this->message("Got SIGTERM.");
        break;
      case SIGTERM:
        $this->message("Got SIGTERM.");
        break;
      default:

    }
  }

  public function getCurrentTask()
  {
    return $this->currentTask;
  }

}