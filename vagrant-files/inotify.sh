#!/usr/bin/env bash
while inotifywait -r -e modify,create /opt/jinya/var/profiler/; do
    rsync -avz /opt/jinya/var/profiler/ /jinya/var/profiler/
done