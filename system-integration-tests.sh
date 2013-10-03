#!/bin/bash

INSTALLDIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"

# From: http://tldp.org/LDP/abs/html/colorizing.html
# Colorizing the installation process.

black='\E[1;30;40m'
red='\E[1;31;40m'
green='\E[1;32;40m'
yellow='\E[1;33;40m'
blue='\E[1;34;40m'
magenta='\E[1;35;40m'
cyan='\E[1;36;40m'
white='\E[1;37;40m'

cecho ()                     # Color-echo.
                             # Argument $1 = message
                             # Argument $2 = color
{
  local default_msg="No message passed."
                             # Doesn't really need to be a local variable.

  message=${1:-$default_msg}   # Defaults to default message.
  color=${2:-$white}           # Defaults to white, if not specified.

  echo -e "$color"
  echo -e "$message"
  
  tput sgr0                     # Reset to normal.

  return
}

# Check where is installed OSF
OSFFOLDER="/usr/share/osf/"

cecho "Where is OSF installed on your server (default: $OSFFOLDER):" $magenta

read NEWOSFFOLDER

[ -n "$NEWOSFFOLDER" ] && OSFFOLDER=$NEWOSFFOLDER

# Make sure there is no trailing slashes
OSFFOLDER=$(echo "${OSFFOLDER}" | sed -e "s/\/*$//")


OSFPHPAPIDOWNLOADURL="https://github.com/StructuredDynamics/osf-PHP-API/zipball/master"

echo -e "\n\n"
cecho "----------------------------------"
cecho " Installing the OSF PHP API "
cecho "----------------------------------"
echo -e "\n\n"

# Current location: /usr/share/osf/

sudo wget $OSFPHPAPIDOWNLOADURL

while [ $? -ne 0 ]; do
  cecho "Connection error while downloading the latest version of the OSF-PHP-API; retrying...\n" yellow
  sudo rm -rf master.zip
  sudo wget $OSFPHPAPIDOWNLOADURL
done

cecho "\n\n9.3) Decompressing OSF PHP API...\n"

sudo unzip "master"  

cd `ls -d structureddynamics*/`

cd "StructuredDynamics/osf/"

sudo cp -a php $OSFFOLDER"/StructuredDynamics/osf/"

cd ../../

sudo rm -rf `ls -d structureddynamics*/`

sudo rm master

echo -e "\n\n"
cecho "--------------------"
cecho " Installing PHPUnit "
cecho "--------------------"
echo -e "\n\n"

cecho "\n\nInstall PHPUnit...\n"

sudo apt-get install -y php-pear

pear channel-discover pear.phpunit.de
pear channel-discover pear.symfony-project.com
pear upgrade-all

sudo pear install --force --alldeps phpunit/PHPUnit


# Download the tests suites, and move them into the OSF folder.
sudo mkdir tests

cd tests

cecho "\n\nDownload the latest system integration tests for OSF...\n"

sudo wget https://github.com/StructuredDynamics/osf-Tests-Suites/zipball/master

while [ $? -ne 0 ]; do
  cecho "Connection error while downloading the latest version of the OSF Tests Suites; retrying...\n" yellow
  sudo rm -rf master.zip
  sudo wget https://github.com/StructuredDynamics/osf-Tests-Suites/zipball/master
done

unzip master

cd `ls -d structureddynamics*/`

cd StructuredDynamics/osf/

# Move the tests suites to OSF's folder structure
sudo mv * $OSFFOLDER"/StructuredDynamics/osf/"

cd ../../../

sudo rm -rf `ls -d structureddynamics*/`

# Go to the tests' folder, and change the configuration files
cd $OSFFOLDER"/StructuredDynamics/osf/tests/"

DOMAINNAME="localhost"

cecho "What is the domain name where the OSF instance is accessible (default: $DOMAINNAME):" $magenta

read NEWDOMAINNAME

[ -n "$NEWDOMAINNAME" ] && DOMAINNAME=$NEWDOMAINNAME

cecho "\n\nConfigure tests...\n"

sudo sed -i "s>REPLACEME>"$OSFFOLDER"/StructuredDynamics/osf>" phpunit.xml

sudo sed -i "s>$this-\>osfInstanceFolder = \"/usr/share/osf/\";>$this-\>osfInstanceFolder = \""$OSFFOLDER"/\";>" Config.php

sudo sed -i "s>$this-\>endpointUrl = \"http://localhost/ws/\";>$this-\>endpointUrl = \"http://"$DOMAINNAME"/ws/\";>" Config.php

sudo sed -i "s>$this-\>endpointUri = \"http://localhost/wsf/ws/\";>$this-\>endpointUri = \"http://"$DOMAINNAME"/wsf/ws/\";>" Config.php


cecho "\n\nRun the system integration tests suites...\n"

sudo phpunit --configuration phpunit.xml --verbose --colors --log-junit log.xml

cecho "\n\n=============================\nIf errors are reported after these tests, please check the "$INSTALLDIR"/tests/log.xml file to see where the errors come from. If you have any question that you want to report on the mailing list, please do include that file in your email: http://groups.google.com/group/open-semantic-framework\n=============================\n\n"

