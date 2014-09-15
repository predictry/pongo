#! /bin/bash

packer build EC2_PERSOAN_ACCOUNT  >output.txt
ami=$(tail -n 1 output.txt | grep -E -o 'ami-.{8}')
aws autoscaling create-launch-configuration --launch-configuration-name my-test-lc --image-id $ami --instance-type t2.micro
sudo rm output.txt
