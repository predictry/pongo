#! /bin/bash


packer build EC2_MAIN_SERVER.json  >image_id.txt
ami=$(tail -n 1 image_id.txt | grep -E -o 'ami-.{8}')
sudo rm image_id.txt


if [ ! -z "$ami" ]; then
	
	#The below command will return  the name of the most updated launch-configuration
	current_launch_configuration=$(aws cloudformation describe-stack-resources --stack-name "update-pongo" | grep -E -o 'update-pongo-PongoLaunchConfiguration-.+"' | grep -E -o '.+[^\"]')


	#The below command will return the id of the most resent pongo-image
	old_ami_id=$(aws autoscaling describe-launch-configurations --launch-configuration-names $current_launch_configuration | grep -E -o 'ami-.+[^\"]' | grep -E -$



	#updating the template
	 aws cloudformation update-stack --stack-name update-pongo --template-body file://cloud.json --parameters  ParameterKey=Instanceid,ParameterValue=$ami;then
			

	counter=1
	while :;
  	do
		update_status=$(aws cloudformation describe-stacks --stack-name "update-pongo" | grep -E -o '"StackStatus": "(.+)"')

		if [[ $update_status == '"StackStatus": "UPDATE_COMPLETE"' ]]; then
			echo 'Stack successfully updated'

			#The below command will delete  the old image after the updating process finished
			aws ec2 deregister-image --image-id $old_ami_id


			#The below command will get the ID of the snapshop from the old image
			snapshot_id=$(aws ec2 describe-snapshots | grep -E -o -A 10 "$old_ami_id.*" | grep -E -o 'snap-\S{8}')

			#The bellow command will delete the the old snapshop
			aws ec2 delete-snapshot --snapshot-id $snapshot_id


	break
					else
						echo 'Not Updates Yet'

					 	counter=$(($counter + 1))
						echo $counter

					if [ $counter -eq 30 ]; then
						echo 'There is something wrong with the stack updating, please check out the stack events';
					break;
					fi

		sleep 10;
		fi
	done


unset $current_launch_configuration
unset $old_ami_id
unset $snapshot_id


else
echo "There is something wrong with packer"
return 1

fi

