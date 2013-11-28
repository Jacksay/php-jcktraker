<?php
require './Jacksay/Traker/JckTraker.php';

use Jacksay\Traker\JckTraker;

/** Helper if needed

function debug($mixedvar, $comment = "Debug") {
    JckTraker::debug($mixedvar, $comment);
}

function info($mixedvar) {
    JckTraker::info($mixedvar);
}

function success($mixedvar) {
    JckTraker::success($mixedvar);
}

function warning($mixedvar) {
    JckTraker::warning($mixedvar);
}

function error($mixedvar) {
    JckTraker::error($mixedvar);
}

function database($mixedvar) {
    JckTraker::database($mixedvar);
}
/*****/

// Usage...
JckTraker::debug("DEBUG");
JckTraker::database("database");
JckTraker::error("Error");
JckTraker::flow("Flow");
JckTraker::info("Info");
JckTraker::success("Success");
JckTraker::warning("Warning");

