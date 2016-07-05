# AWS Web Based Security

This is a PHP based web interface to AWS Security Groups. This interface uses the AWS PHP SDK to connect to your security group and manipulate the firewall rules in order to allow a balance between security and convenience. A good application scenario for this interface could be for commonly used FTP or SSH access to a server or group of servers. 

General implementation of this system should be paired with SSL for delivery and HTTP authentication as an additional barrier to the interface. It is not recommended (for obvious reasons) to expose this interface to the public in any way whatsoever.

## How to implement this

Below find some simple steps / recommendations for implementing this system.

### Set up apache or nginx

The first thing you should do is set up an SSL virtual host on apache or nginx and implement HTTP authentication as a barrier for accessing the page. Different http authentication users will allow for better auditing and user management of the system. You could also implement integrated user authentication within PHP however that would generate more overhead and more of a need for robust testing.

### Install AWS' PHP SDK in your document root

This is already fully documented by AWS and can be accessed here : https://docs.aws.amazon.com/aws-sdk-php/v3/guide/getting-started/installation.html

### Copy this repository to your document root

Straightforward right? Once copied you might want to generate your IAM user credentials that the script will use to actually access your AWS account's security groups. It is also recommended that the security credentials you generate have as-narrowed-down as possible permissions. If you want to copy the policy we have , you can cut/paste what we have below. We are restricting access to be just with security groups.

```
{
    "Version": "2012-10-17",
    "Statement": [
        {
            "Sid": "Stmt1467303214000",
            "Effect": "Allow",
            "Action": [
                "ec2:AuthorizeSecurityGroupEgress",
                "ec2:AuthorizeSecurityGroupIngress",
                "ec2:CreateSecurityGroup",
                "ec2:DeleteSecurityGroup",
                "ec2:DescribeInstanceAttribute",
                "ec2:DescribeInstanceStatus",
                "ec2:DescribeInstances",
                "ec2:DescribeNetworkAcls",
                "ec2:DescribeSecurityGroups",
                "ec2:RevokeSecurityGroupEgress",
                "ec2:RevokeSecurityGroupIngress"
            ],
            "Resource": [
                "*"
            ]
        }
    ]
}
```

You could probably restrict the "resource" section to be just for one security group in particular if you wish to lock it down even further. This interface assumes you are using generally accepted best practices for storing your AWS security credentials (see http://docs.aws.amazon.com/aws-sdk-php/v2/guide/credentials.html). Storing your credentials in a file in your home directory (i.e. ~/.aws/credentials) is generally a better idea than hard coding them in the script itself.

### Modify index.php as you see fit

The script can be modified to manipulate more than one security group rule. The example used in the script is simply to modify/add FTP (port 21). 

## Who are you?

We [develop and design websites in Toronto](https://www.shift8web.ca "Toronto Web Design by Shift8")
