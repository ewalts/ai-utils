#!/bin/bash 
#######################################################################################
###> New x bash -> disk.sh -> Initial creation user => ericw => 2022-11-08_13:44:23 ###
#######################################################################################
#_#>
# CLI Colors    
#Red='\e[0;31m'; BRed='\e[1;31m'; BIRed='\e[1;91m'; Gre='\e[0;32m'; BGre='\e[1;32m'; BBlu='\e[1;34m'; BWhi='\e[1;37m'; RCol='\e[0m';

volId=$(cat playbook/vars/deployment_output_vars.yml|grep vol|awk '{print $2}')
echo "Volume ID: $volId"
cs=$(aws ec2 describe-volumes --volume-ids $volId|grep Size)
echo $cs'G'
s=$(echo $cs|awk '{print $2}')
read -p "Resize? type size in G or n for no, or just hit enter: " size
if [[ $size == 'n'  ]]; then
    echo "Goodbye"
elif [[ $size == '' ]]; then
    echo "Goodbye"
elif [[ $size == $s ]]; then
    echo "Disk is $size"
elif [[ $size > $s ]]; then
    aws ec2 modify-volume --volume-id $volId  --size $size;
fi
