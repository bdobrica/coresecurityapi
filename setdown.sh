#!/bin/bash
rm -Rf core view
cp -r ../plugins/wp-crm core
cp -r ../themes/wp-crm view
rm -Rf core/libs/RGraph*
rm -Rf core/libs/ZendGdata*
rm -Rf core/libs/tmp
rm -Rf core/libs/phpword
rm -Rf core/libs/tcpdf
rm -Rf core/libs/gdata*
rm -Rf core/libs/gpwd.php
rm -Rf core/libs/*old*

rm -Rf core/ui
rm -Rf core/ui2

rm -Rf view/ui
rm -Rf view/genius
