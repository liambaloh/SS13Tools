# SS13Tools
The various web tools for ss13.

There are only two things you should know about this repository...
* It was never meant to be public and thus was coded absolutely atrociously.
* It predates bootstrap :)

# Remake

You might want to also look at the following remake attempt for these tools: https://github.com/nfreader/newSS13tools
At the time of writing, not all tools have been recreated though.

# Included tools
- Ban overview page
- Connection lookup (aka "How much time have I wasted")
- Death heatmap image
- Do admins play
- Game stats
- Ingame poll results
- Examine poll (needs repair)
- Book club
- Player migration
- Privacy poll results (needs repair)
- Achievements generator
- Advice assistant
- Character image generator
- Moodlet generator
- Passport generator
- Userbar generator

# Licensing
- Code licensed under MIT license aka "Do whatever you want with it"
- SS13 art licensed under Creative Commons 3.0 BY-SA or whatever they're using right now. More info: https://github.com/tgstation/tgstation

# Installation

## Tools:
- Have a server running https://github.com/tgstation/tgstation code, with a database connection set up. Play a few games, run a few polls, ban a few players (just so you ahve data to show with these tools)
- Have a web server running some not-completely-obsolete version of PHP (5.4+ should work)
- Copy-paste these files to some folder on your web server (At minimum analytics.php, db.php, the php file for tool of your choice and all the directories)
- Open db.php and fill in the required information (database host, port, username, password and db name + website login username and password - these two are used for tools that require a sign-in like the book club)

## Image generators:
- Have a web server running PHP 5.4+
- Copy paste the entire folder for the image generator you want onto your web server
- You might need to add permissions (chmod) the 'w' and 'saves' folders so the php scripts are allowed to save generated images.
