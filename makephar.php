#!/usr/bin/env php
<?php
/*
Copyright 2016-2018 Daniil Gentili
(https://daniil.it)
This file is part of MadelineProto.
MadelineProto is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
MadelineProto is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
See the GNU Affero General Public License for more details.
You should have received a copy of the GNU General Public License along with MadelineProto.
If not, see <http://www.gnu.org/licenses/>.
*/

if (!isset($argv[3])) {
    echo 'Usage: '.$argv[0].' inputDir output.phar ref'.PHP_EOL;
    die(1);
}

@unlink($argv[2]);

$p = new Phar(__DIR__.'/'.$argv[2], 0, $argv[2]);
$p->buildFromDirectory(realpath($argv[1]), '/^((?!tests).)*(\.php|\.py|\.tl|\.json)$/i');
$p->addFromString('vendor/danog/madelineproto/.git/refs/heads/master', $argv[3]);
$p->addFromString('.git/refs/heads/master', $argv[3]);

$p->setStub('<?php
$backtrace = debug_backtrace();
if (basename($backtrace[0]["file"]) === "phar.php") {
    chdir(dirname($backtrace[1]["file"]));
    if (!isset($phar_debug)) file_put_contents($backtrace[0]["file"], file_get_contents("https://phar.madelineproto.xyz/phar.php?v=new"));
}

Phar::interceptFileFuncs(); 
Phar::mapPhar("'.$argv[2].'"); 
require_once "phar://'.$argv[2].'/vendor/autoload.php"; 

__HALT_COMPILER(); ?>');
