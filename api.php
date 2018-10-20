<?php

$filename = 'api.php';
$api_token = ''; // the token you want to be your auth token

// get the HTTP method, path and body of the request
$request = apiInputClean(explode('/', $_SERVER['REQUEST_URI']));
$input = json_decode(file_get_contents('php://input'),true);
$input_token = $request[0];

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
        $loadresult = @exec('uptime');
        preg_match("/averages?: ([0-9\.]+),[\s]+([0-9\.]+),[\s]+([0-9\.]+)/",$loadresult,$load);
        unset($load[0]);
        $loadfinal = array_values($load);

        foreach ($loadfinal as $key => $value){
            settype($loadfinal[$key], 'float');
        }

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
