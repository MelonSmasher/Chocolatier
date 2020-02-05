#! /usr/bin/env bash

start() {
    cd laradock;
    docker-compose up -d nginx mariadb redis workspace;
    cd ../;
}

stop() {
    docker stop $(docker ps -a -q);
}

restart() {
    stop;
    start;
}

clean() {
    stop;
    docker system prune -a --volumes;
}

cleanstart() {
    clean;
    start;
}

workspace() {
    cd laradock;
    docker-compose exec workspace bash && cd ../;
}

case "$1" in
  start)
    start
    ;;
  stop)
    stop
    ;;
  restart)
    restart
    ;;
  clean)
    clean
    ;;
  cleanstart)
    cleanstart
    ;;
  workspace)
    workspace
    ;;
  *)
    echo "Usage: $0 {start|stop|restart|clean|cleanstart|workspace}" >&2
    exit 1
    ;;
esac