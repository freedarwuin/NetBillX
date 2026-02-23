<?php

// Linux
if (strtoupper(substr(PHP_OS, 0, 3)) === 'LIN') {
    $load = sys_getloadavg();
    $cores = (int)shell_exec('nproc');
    if ($cores > 0) {
        echo round(($load[0] * 100) / $cores, 2);
        exit;
    }
}

// Windows
if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
    @exec('wmic cpu get loadpercentage', $output);
    if (isset($output[1])) {
        echo (int)trim($output[1]);
        exit;
    }
}

echo 0;