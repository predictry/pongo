#! /bin/bash

packer build EC2_PERSOAN_ACCOUNT  >image_id.txt
ami=$(tail -n 1 image_id.txt | grep -E -o 'ami-.{8}')
sudo rm image_id.txt

ami=ami-06183c54

if [ ! -z "$ami" ]; then


	#updating the template
	if aws cloudformation update-stack --stack-name update-pongo --template-body file://cloud.json --parameters  ParameterKey=Instanceid,ParameterValue=$ami;then
			
		echo "The process finished successfully"
	else
        	echo "Updating the template failed"
        return 1

        fi

else
echo "There is something wrong with packer"
return 1

fi

