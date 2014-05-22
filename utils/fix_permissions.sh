#!/bin/bash
#
# This script fixes permissions on Magento subtree
#
# Usage:
# cd to virtual host folder
# run script by issuing utils/fix_permissions.sh . command
#
# Author Sven Varkel <sven@mageflow.com>
# Copyright 2013 MageFlow Ltd

project_folder=$1
if [ -z $project_folder ]; then echo "Project directory parameter not specified. Using current folder $(pwd)"; project_folder="."; fi

os="Linux"
web_user="www-data"
environment=$(uname -a)
if [[ $environment == *Darwin* ]]
then
    os="OSX"
fi

if [ -e "/etc/redhat-release" ]
then
    distro="redhat"
    web_user="web"
else
    #Assuming quite dubiously here all others are debian/ubuntu:)
    distro="debian"
    web_user="www-data"
fi
if [ $os == "Linux" ]
then
    echo "Working in Linux"
    echo "Fixing group"
    sudo chown -R ${web_user}:dev $project_folder
    echo "Done."
    echo "Fixing permissions of directories"
    find $project_folder -type d | sudo xargs -I{} chmod g+rws,o-ws "{}"
    echo "Done."
    echo "Fixing permissions of files"
    find $project_folder -type f | sudo xargs -I{} chmod g+rw,o-w "{}"
    sudo chmod a+x $project_folder/utils/*.sh
    chmod +x $project_folder/public/mage
    echo "Done."
fi

if [ $os == "OSX" ]
then
    echo "Working in OSX"
    echo "Fixing group"
    sudo chgrp -R localaccounts $project_folder
    echo "Done."
    echo "Fixing permissions of directories"
    find $project_folder -type d | sudo xargs -I{} chmod g+rws,o-ws "{}"
    echo "Done."
    echo "Fixing permissions of files"
    find $project_folder -type f | sudo xargs -I{} chmod g+wr,o-w "{}"
    sudo chmod a+x $project_folder/utils/*.sh
    echo "Done."
fi
