<?php

$autoloadBaseDir = __DIR__ . DIRECTORY_SEPARATOR . "lib" . DIRECTORY_SEPARATOR;

spl_autoload_register(
    function ($className) use ($autoloadBaseDir)
    {
        $className = ltrim($className, "\\");
        $filePath = $autoloadBaseDir . str_replace("\\", DIRECTORY_SEPARATOR, $className) . ".php";

        if (is_file($filePath))
        {
            require_once $filePath;
        }
    }
);