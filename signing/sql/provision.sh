#!/bin/bash

curl http://simplemaps.com/static/data/world-cities/basic/simplemaps-worldcities-basic.csv -O cities.csv

CMD="sqlite3 base.sql"
    
    echo "DROP TABLE cities;" | $CMD
    echo "DROP TABLE users;"  | $CMD
    echo ".import cities.csv cities" | $CMD -cmd ".mode csv"
    echo ".import users.csv  users"  | $CMD -cmd ".mode csv"