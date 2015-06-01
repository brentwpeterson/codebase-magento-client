#!/bin/bash

black='\033[0;30m'
red='\033[0;31m'
green='\033[0;32m'
brownorange='\033[0;33m'
blue='\033[0;34m'
purple='\033[0;35m'
cyan='\033[0;36m'
lightgray='\033[0;37m'

darkgray='\033[1;30m'
lightred='\033[1;31m'
lightgreen='\033[1;32m'
yellow='\033[1;33m'
lightblue='\033[1;34m'
lightpurple='\033[1;35m'
lightcyan='\033[1;36m'
white='\033[1;37m'

NC='\033[0m'

AOE_SCHEDULER_REPO_URL="git@github.com:AOEpeople/Aoe_Scheduler.git"
CODEBASE_MAGENTO_CLIENT_REPO_URL="git@github.com:brentwpeterson/codebase-magento-client.git"

installModman() {

    # Install modman
    echo "${green}---- Installing modman... ----${NC}"
    wget -q --no-check-certificate "https://raw.github.com/colinmollenhour/modman/master/modman-installer" -nv
    bash modman-installer
    cp -f ~/bin/modman /usr/bin
    cp -f ~/bin/modman /usr/local/bin
    rm -f modman-installer
    mkdir .modman
}

installMagentoModule() {
    # Git Checkout Codebase Magento Client
    echo "${green}---- Installing/Updating Codbase Magento Client... ----${NC}"
    cd .modman
    mkdir temp
    cd temp
    git clone ${CODEBASE_MAGENTO_CLIENT_REPO_URL}

    # Git Checkout Aoe Scheduler
    echo "${green}---- Installing/Updating Aoe Scheduler... ----${NC}"
    git clone ${AOE_SCHEDULER_REPO_URL}

    # remove .git folder and .gitignore
    find . -name ".git" -exec rm -rf "{}" \;
    find . -name .gitignore -type f -exec rm -f {} \;

    # move or overrite current to source to old
    rsync -av * .* ..
    cd ..
    rm -rf temp


}

deployAllModule() {
    #Deploy all modman module
    echo "${green}---- Deploy all module using modman... ----${NC}"
    cd ..
    ~/bin/modman deploy-all
}

install() {
    # Full installation of Modman, Codebase Magento Client and AOE Scheduler
    installModman
    installMagentoModule
    deployAllModule
}

if [ $1 = "install" ]; then
	install
elif [ $1 = "update" ]; then
    installMagentoModule
    deployAllModule
fi
