[![MIT Licence](https://img.shields.io/badge/License-MIT-blue.svg)](https://github.com/AlexCarvalhoDev/php-server-status-json/blob/master/LICENSE)

# PHP Server Status JSON
A server status API with token verification.

#### Available Metrics
* Uptime (string)
* Load average (array)
  * Load 1
  * Load 2
  * Load 3
* RAM (array)
  * Total (MB)
  * Free (MB)
* Disk (array)
  * Total (MB)
  * Free (MB)

#### Installation
Just download/copy it to your web server:
```
wget https://raw.githubusercontent.com/AlexCarvalhoDev/php-server-status-json/master/api.php
```

#### Usage
GET request:
```
http://<your_server>/api.php/<your_token>
```

#### Feel free to suggest improvements that you want :D
