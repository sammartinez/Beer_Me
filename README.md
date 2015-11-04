# Beer_Me

##### It's about time you buy your friend a beer, September 3rd, 2015

#### By _**Kelli Sommerdyke, Casey Heitz, Jason Bethel, Kyle Pratuch, Jordan Johansen, and Sam Martinez**_

## Description

Beer Me offers the ability to remotely buy a friend a drink. After siging up, a free and quick process, you can sign in using your email address. From your profile page you can buy a drink for a friend (don't worry we will notify them via email), check on the number of drink tokens you have (as well as their value), edit or change your profile information, and add your favorite bars.

  Do you want to be a participating bar? No problem! After siging up, you can login to view a profile suited to your needs. We offer the ability to add menu items of your choice to be redeemed with tokens. When you are ready to redeem a customer's token simply click on the "Unredeemed Tokens" link in your profile; from the new page you will see the value of the token as well as an option to the redeem the token. Your profile also has a link to update your business information at your convenience.

## Setup

If you're looking to test this app yourself (these directions assume you are working from a mac and are using MAMP):

1. Clone repository from GitHub.

2. Run ```composer install``` in top level of project folder.

3. In a new terminal tab, enter ```mysql.server start```.

4. Then enter ```mysql -uroot -proot``` (you now have MySql running)

5. Start an apache server (another new tab in terminal) with ```apachectl start```

5a. I also recommend using MAMP for mac, when opening MAMP, click on ```"Preferences > Web Server > Document Root > Beer_Me > web"```.
    The above commands are mainly pointing the server to the web folder on the project folder of Beer Me.

6. Open your browser to ```localhost:8888/phpmyadmin```

7. Import the the database files to the top level of your project folder using phpMyadmin. Do this by clicking the import tab in phpMyadmin and choosing one of the files and clicking "GO".

8. Start another terminal tab. Open a php server ```php -S localhost:8000```. This is so you can view your twig sites.

9. Direct your browser to ```localhost:8000``` .

10. It's about time time you buy your friend a damn beer.

## Tutorial on using Beer Me
* Below, I have created a Tutorial on setting up a couple users in Beer Me and being able to send tokens back and forth to one another. I have not taken the time to create a Tutorial for the business sign up. Stay tune for that.

1. Once you are setup with the application, go ahead and click on Sign Up.
2. Enter in your Name and email address and then click on Sign Up.
3. A new page should render thanking you for signing up and then you have the ability to sign in.
4. After clicking on Sign In from the rendered page, fill in your email address and click on Sign In.
5. Once you sign in, you have a few options available to you. Buy a Beer, View your Tokens, Edit your Profile, and choose from your favorite bars.
6. I've gone ahead and have added a few test bars into the database for you, they are as followed : ```Apex, Side Street, Binks, Target, and Test Bar ```.
7. Once you select a test bar, go ahead and add one of these bars to your favorite bars list.
8. At this point, we want to see tokens come into play with the application, to do this, repeat the steps from 1-7 and create a second user.
9. Once a second user is created, go ahead and type the original email you created when you first started the Tutorial into Buy A Beer dropdown field.
10. After searching for the original email, it'll render to a new page that has the user's favorite bars. Select from the dropdown a bar and click on "Select bar".
11. At this point, another dropdown will come up below that with different options to choose from that that specific bar offers. Select from the list and click on "Send Beer token".
12. If the beer token is sent successfully, then it will render a page that shows two options, either to go back and send another beer to the user or go back to the current user's login window.
13. At this point, you're able to click on the sign in in the navigation and sign in with the email address whom you just sent a beer token to.
14. Once in that user, click on My Tokens.
15. Select the token and view what the token is worth!
   And that is a Tutorial walking through using the user side of this app!
## Technologies Used

* HTML
* CSS
* PHP
* PHPUnit
* JavaScript
* JQuery
* Silex
* Twig

### Legal

Copyright (c) 2015 **_Kelli Sommerdyke, Casey Heitz, Jason Bethel, Kyle Pratuch, Jordan Johansen, and Sam Martinez_**

This software is licensed under the MIT license.
