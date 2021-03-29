# How to contribute

To contribute to this project, follow the steps below.

N.B. Before following these steps, you have to install git and composer on your local machine and create a Github account.
Procedure for making changes to the project

https://symfony.com/doc/current/best_practices.html

## Creation of a local fork of the project
Click on the "fork" button at the top right of the page. This will create a copy of this repository in your own Github. Notice that the name is different.

## Create a local copy
Clone your copy from GitHub to your local machine :

```
git clone https://github.com/YourGithubUsername/DAPS-P8-Upgrade-ToDo-Co.git
```

And install the project and its dependencies by referring to README.md

## Create a new branch
Navigate to the repository directory on your computer.
Create the new branch using a logical name corresponding to the changes or new features :

```
git checkout -b new-feature
```

## Add new tests related to modifications
To implement new tests, refer to the official Symfony documentation.
Run the tests with generation of a code coverage report to ensure that all the new code is running :

```
vendor/bin/phpunit --coverage-html testcoverage
```

## Validate the modifications
Commit your changes.
Clearly detail the changes made.

``` 
git add.
git commit -m 'commit message' 
```

Submit the changes to your forke repository
```
git push origin new-feature
```
## Create a Pull Request
Go to your forke repository, you will see that your new branch is listed at the top with a handy "Compare & pull request" button. Click on this button.
Be sure to provide a short title and explain why you created it, in the description box.

You must now submit the extract request to the original repository. To do this, press the "Create pull request" button and you are done.
The owner of the project will then receive a notification indicating that someone is suggesting a modification.

## Modifications
If you are asked to add or change anything, do not create a new checkout request. Make sure you are on the correct branch and make the changes.
```
git checkout new-feature
```