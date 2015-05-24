#!/bin/bash
echo $1
mysql -u root -p db_api_acreditate < $1
#/usr/bin/php ../scan.php
