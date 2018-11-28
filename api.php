<?php

$filename = 'api.php';
$api_token = ''; // the token you want to be your auth token

header('Content-Type: application/json');

// get the HTTP method, path and body of the request
$request = apiInputClean(explode('/', $_SERVER['REQUEST_URI']));
$input_token = end($request);

if (apiTokenCheck($input_token, $api_token)){

    // get server load averages
    function serverUptime(){
        $uptimeresult = @exec('uptime');
        $uptime = explode(' up ', $uptimeresult);
        $uptime = explode(',', $uptime[1]);
        $uptime = $uptime[0].', '.$uptime[1];

        return $uptime;
    }

    // get server load averages
    function serverLoad(){
        $loadresult = @exec('cat /proc/loadavg');
        $arr = explode(" ", $loadresult);
        $loadfinal = array(
            'load 1' => $arr[0],
            'load 2' => $arr[1],
            'load 3' => $arr[2]
        );

        return $loadfinal;
    }

    // get server disk status
    function serverDisk(){
        $disk_total_space = disk_total_space(getcwd());
        $disk_free_space  = disk_free_space(getcwd());

        $disk = array(
            'total' => $disk_total_space,
            'free'  => $disk_free_space
        );

        foreach ($disk as $key => $value){
            settype($disk[$key], 'integer');
        }

        return $disk;
    }

    // get server ram status
    function serverRam(){
        $total_mem = preg_split('/ +/', @exec('grep MemTotal /proc/meminfo'));
        $total_mem = $total_mem[1];
        $free_mem = preg_split('/ +/', @exec('grep MemFree /proc/meminfo'));
        $cache_mem = preg_split('/ +/', @exec('grep ^Cached /proc/meminfo'));
        $free_mem = $free_mem[1] + $cache_mem[1];

        $ram = array(
            'total' => $total_mem,
            'free'  => $free_mem
        );

        foreach ($ram as $key => $value){
            settype($ram[$key], 'integer');
        }

        return $ram;
    }

    $apiResult = array(
        'uptime' => serverUptime(),
        'load'   => serverLoad(),
        'disk'   => serverDisk(),
        'ram'    => serverRam()
    );

    header('Content-Type: application/json');
    echo json_encode($apiResult);

}else
    echo 'API authentication failed.';


function apiInputClean($request){
    global $filename;
    $request = array_filter($request);
    $request = array_values($request);
    $i = 0;
    foreach ($request as $key){
        if ($key == $filename)
            unset($request[$i]);
        $i++;
    }
    $request = array_values($request);
    return $request;
}

function apiTokenCheck($input_token, $api_token){
    if ($input_token == $api_token)
        return true;
    else
        return false;
}
