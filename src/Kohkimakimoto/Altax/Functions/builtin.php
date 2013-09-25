<?php

use Kohkimakimoto\Altax\Context;

function host()
{
    $host = null;
    $options = array();
    $roles = null;

    $context = Context::getInstance();

    $args = func_get_args();
    if (count($args) < 2) {
        throw new \Exception("Missing argument. function host() must 2 arguments at minimum.");
    }

    if (count($args) === 2) {
        $host = $args[0];
        $roles = $args[1];
    } else {
        $host = $args[0];
        $options = $args[1];
        $roles = $args[2];
    }

    $hosts = $context->get('hosts');

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

    $context->set('hosts', $hosts);
}

/**
 * Register role.
 * @param String           $role
 * @param Array or String  $hosts
 * @throws Altax_Exception
 */
function role($role, $hosts)
{
    $context = Context::getInstance();

    if (is_string($hosts)) {
       $hosts = array($hosts);
    }

    $roles = $context->get('roles');

    foreach ($hosts as $host) {
        if (!isset($roles[$role])) {
            $roles[$role] = array();
        }
        $roles[$role][] = $host;
    }

    $roles[$role] = array_unique($roles[$role]);

    $context->set('roles', $roles);
}

function desc($desc)
{
    $context = Context::getInstance();
    $context->set('desc', $desc);
}

function task()
{
    $context = Context::getInstance();
}
