# core-dump-debug-base
This directory contains code which can be used as a base code to debug the core issues by generating core dump files and gdb tool.

## Environment and Setup

### Prerequisites

* `php`: version 8.3.7
* `OS` : Docker / Apache on Linux
* `google/cloud-firestore` : version 1.47.3
* `grpc` 1.69.0

### Setup

Clone this repository [core-dump-debug-base](https://github.com/ajinkyakulkarni75/core-dump-debug-base.git).

```sh
$ git clone https://github.com/ajinkyakulkarni75/core-dump-debug-base.git
$ cd core-dump-debug-base
```
### Copy default credentials file

After cloning the core-dump-base repo check the local credentials file and copy that into the [/app](https://github.com/ajinkyakulkarni75/core-dump-debug-base/tree/main/app) folder. Also make sure you have a [CGP Project](https://pantheon.corp.google.com/) and firestore database named firestore `firestore-mock` is created along with the collection with collection ID `schedules`.

### Build and run using docker file

Build the docker container and run the container to open a docker container shell

```sh
$ sudo sh -c 'echo "/tmp/core.%e.%p" > /proc/sys/kernel/core_pattern'
$ docker build -t firestore-grpc-new . 
$ docker run -it --ulimit core=-1 --security-opt seccomp=unconfined -v /tmp:/tmp -v "$(pwd)/logs/":/var/log/mylogs/ --entrypoint bash firestore-grpc-new
```

### Run PHP Script

Once inside the container shell check the grpc installation version and then run PHP script.

```sh
$ php -i | grep grpc
$ php /var/www/html/index.php 2> /var/log/mylogs/grpc.log\
```

### Check core dump file and debug using gdb tool

```sh
$ ls -l /tmp/core.*
$ gdb /usr/local/bin/php /tmp/core.*
```
