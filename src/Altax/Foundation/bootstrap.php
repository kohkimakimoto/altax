<?php

$c1 = getenv("HOME")."/.altax/config.php";
$c2 = getcwd()."/.altax/config.php";

if ($c1 === $c2) {
    $env['config.paths'] = [
        $c1,
    ];
} else {
    $env['config.paths'] = [
        $c1,
        $c2,
    ];
}

$env['server.port'] = 22;
$env['server.key'] = getenv("HOME")."/.ssh/id_rsa";
$env['server.username'] = getenv("USER");
$env['command.shell'] = '/bin/bash -l -c';
$env['script.paths'] = [
    getenv("HOME")."/.altax/scripts",
    getcwd()."/.altax/scripts",
];
$env['script.working'] = sys_get_temp_dir()."/altax";

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
