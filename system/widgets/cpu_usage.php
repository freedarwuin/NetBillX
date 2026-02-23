<?php
// Linux
if (strtoupper(substr(PHP_OS,0,3)) === 'LIN') {
    $load = sys_getloadavg(); // [1min,5min,15min]
    $cores = (int)shell_exec('nproc');
    echo round($load[0] * 100 / $cores, 2);
    exit;
}

// Windows
if (strtoupper(substr(PHP_OS,0,3)) === 'WIN') {
    @exec('wmic cpu get loadpercentage', $output);
    echo isset($output[1]) ? (int)trim($output[1]) : 0;
    exit;
}

// Default si no se detecta
echo 0;