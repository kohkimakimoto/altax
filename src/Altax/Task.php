<?php
/**
 * Task on child process on a host.
 *
 * @author kohkimakimoto <kohki.makimoto@gmail.com>
 * @version $Revision$
 */
class Altax_Task
{
  protected $host;
  protected $task;
  protected $arguments;

  public function __construct($host, $task, $arguments, $localRun)
  {
    $this->host = $host;
    $this->task = $task;
    $this->arguments = $arguments;
    $this->localRun = $localRun;
  }

  public function execute()
  {
    Altax_Logger::log("Forked child to process to host [".$this->host."] pid = ".posix_getpid(), null, "debug");

    // Got Callback task function;
    $callback = Altax_Config::get('tasks/'.$this->task.'/callback');

    // Do Execute method. Gets Code string
    Altax_Logger::log("Processing execute", "[$this->host]", "debug");

    $callback($this->host, $this->arguments);
  }

  public function getSSHCommandBase()
  {
    if (!$this->host) {
      throw new Altax_Exception('Host is not specified.');
    }

    $sshcmd = "ssh -t";

    $sshLoginName = Altax_Config::get('hosts/'.$this->host.'/login_name');
    if ($sshLoginName) {
      $sshcmd .= " -l $sshLoginName";
    }

    $sshIdentityFile = Altax_Config::get('hosts/'.$this->host.'/identity_file');
    if ($sshIdentityFile) {
      $sshcmd .= " -i $sshIdentityFile";
    }

    $port = Altax_Config::get('hosts/'.$this->host."/port");
    if ($port) {
      $sshcmd .= " -p $port";
    }

    $host = Altax_Config::get('hosts/'.$this->host."/host", $this->host);

    $sshcmd .= " $host";
    return $sshcmd;
  }

  public function isLocalRun()
  {
    return $this->localRun;
  }

  public function getTask()
  {
    return $this->task;
  }

  public function getHost()
  {
    return $this->host;
  }

}