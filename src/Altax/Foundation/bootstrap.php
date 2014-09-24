<?php

$env['config_files'] = [
    getenv("HOME")."/.altax/config.php",
    getcwd()."/.altax/config.php",
];

$env['aliases'] = [
    'App'              => 'Altax\Facade\App',
    'Server'           => 'Altax\Facade\Server',
    'Env'              => 'Altax\Facade\Env',
    'Task'             => 'Altax\Facade\Task',
    'Input'            => 'Altax\Facade\Input',
    'Output'           => 'Altax\Facade\Output',
    'KeyPassphraseMap' => 'Altax\Facade\KeyPassphraseMap',
    'Process'          => 'Altax\Facade\Process',
    'Command'          => 'Altax\Facade\Command',
    'RemoteFile'       => 'Altax\Facade\RemoteFile',
    'Script'           => 'Altax\Facade\Script',
];

$env['providers'] = array(
    'Illuminate\Events\EventServiceProvider',
    'Illuminate\Filesystem\FilesystemServiceProvider',
    'Altax\Server\ServerServiceProvider',
    'Altax\Env\EnvServiceProvider',
    'Altax\Task\TaskServiceProvider',
    'Altax\Process\ProcessServiceProvider',
    'Altax\Shell\ShellServiceProvider',
    'Altax\RemoteFile\RemoteFileServiceProvider',
);
