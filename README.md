<a href="https://codeclimate.com/github/08rose08/DAPS-P8-Upgrade-ToDo-Co/maintainability"><img src="https://api.codeclimate.com/v1/badges/7b885b3213f408219b26/maintainability" /></a>

<a href="https://openclassrooms.com/fr/paths/59-developpeur-dapplication-php-symfony">PHP/Symfony developer with Openclassrooms</a><br>

# DAPS-P8-Upgrade-ToDo-Co
## Context
You have just joined a startup whose core business is an application to manage its daily tasks. The company has just been set up, and the application had to be developed at full speed to help show potential investors that the concept is viable (we speak of Minimum Viable Product or MVP).

The choice of the previous developer was to use the PHP framework Symfony, a framework that you are starting to know well!

Good news ! ToDo & Co has finally succeeded in raising funds to allow the development of the company and especially the application.

Your role here is to improve the quality of the application. Quality is a concept that encompasses many subjects: we often talk about code quality, but there is also the quality perceived by the user of the application or the quality perceived by the company's employees, and and finally the quality that you perceive when you have to work on the project.

Thus, for this last specialization project, you are in the shoes of an experienced developer in charge of the following tasks:

    the implementation of new features;
    the correction of some anomalies;
    and the implementation of automated tests.

You are also asked to analyze the project using tools that allow you to have an overview of the quality of the code and the different areas of performance of the application.

You are not asked to correct the points raised by the code quality and performance audit. That said, if time allows, ToDo & Co will be happy to have you reduce the technical debt of this app.
## Skills assessed
* Read and transcribe the operation of a piece of code written by other developers
* Suggest a series of improvements
* Implement new features within an application already initiated by following a clear collaboration plan
* Implement unit and functional tests
* Produce a test execution report
* Analyze the code quality and performance of an application
* Establish a plan to reduce the technical debt of an application
* Provide corrective patches when testing suggests

## Let's go
* Configure the .env : `database`
* Run `composer install`
* Create your database : `php bin/console doctrine:database:create`
* Update the database : `php bin/console doctrine:schema:update --force`
* Load the fixtures if you want : `php bin/console doctrine:fixtures:load`
* Run the server : `symfony server:start`