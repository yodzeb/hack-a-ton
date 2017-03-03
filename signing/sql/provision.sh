#!/bin/bash

#rm -f cities.csv
curl http://simplemaps.com/static/data/world-cities/basic/simplemaps-worldcities-basic.csv > cities.csv
curl https://raw.githubusercontent.com/datasets/country-list/master/data.csv > countries.csv

CMD="sqlite3 base.sql"
    
    echo "DROP TABLE cities;"    | $CMD
    echo "DROP TABLE users;"     | $CMD
    echo "DROP TABLE countries;" | $CMD
    echo ".import cities.csv cities"     | $CMD -cmd ".mode csv"
    echo ".import countries.csv  countries"  | $CMD -cmd ".mode csv"
    echo ".import users.csv  users"      | $CMD -cmd ".mode csv"
