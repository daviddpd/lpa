<?php

require "cli.args.php";

$a = new CommandLine();
$x = $a->parseArgs($argv);
print_r ( $x );


?>