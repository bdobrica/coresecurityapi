#!/bin/bash
if [ -d ../plugins/wp-crm ]; then
	cp -r core/* ../plugins/wp-crm
else
	cp -r core ../plugins/wp-crm
fi
if [ -d ../themes/wp-crm ]; then
	cp -r view/* ../themes/wp-crm
else
	cp -r view ../themes/wp-crm
fi
