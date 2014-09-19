#! /bin/bash

packer build EC2_PERSOAN_ACCOUNT  >image_id.txt
ami=$(tail -n 1 image_id.txt | grep -E -o 'ami-.{8}')
sudo rm image_id.txt

ami=ami-06183c54

if [ ! -z "$ami" ]; then

	aws autoscaling describe-tags >autoscaling.txt
	auto_scaling=$(grep -m 1 -E -o 'update-pongo[^"]+' autoscaling.txt)
	sudo rm autoscaling.txt

	if [ ! -z "$auto_scaling" ]; then

		#terminating the process
		if aws autoscaling  suspend-processes --auto-scaling-group-name $auto_scaling --scaling-processes Terminate ;then

			#updating the template
			if aws cloudformation update-stack --stack-name update-pongo --template-body file://cloud.json --parameters  ParameterKey=Instanceid,ParameterValue=$ami;then
			
			echo "The process finished successfully"
			else
               		echo "Updating the template failed"
                	return 1
                	fi

		else
		echo "Terminationg autoscaling failed"
                return 1	
		fi


	else
	echo "can't find the autoscaling name"
	return 1
	fi

else
echo "There is something wrong with packer"
return 1

fi

