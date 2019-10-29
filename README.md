# PageFeed

A website where you can post your blogs, photos, and videos, and stay in touch
with your friends, all without sharing your data with advertisers or spooky
governments. Import your posts, photos, and videos from existing social networks
and take back control of your data.

# Features

- Homepage
- "Feed" page
- Posts
- Media (Photos and Videos)
- Comments
- Albums
- Privacy
- Sharing
- RSS+JSON Feeds

# Why the hell am I doing this?

The goal with this was to make something as simple as possible that could mimic,
to a certain extent, what people like about using social networks, which is (I think):

- They can see all the stuff their friends post in one place
- They can leave comments on that stuff
- They can share that stuff on their own profile.

No doubt people enjoy the free aspect of the social networks, but who knows.
I think there's at least some people out there willing to pay $5/month to setup
and run their own website, and maybe this will be useful for them. Also, I wanted
a way for people to be able to take their data from the existing social networks
and do something with it other than leave it in sitting in a zip file.

# Supported Data Import Sources

- Twitter
- Facebook
- Instagram
- More coming soon, maybe? 🤷‍

To import your data, you'll need to request a copy of your data from each social
network.

# Install

- Run `composer require ....`
- Configure the thing.
- Some more shit that I don't know yet.

# Setup

Fuck if I know.

# Importing data

Once you've got a copy of your data (and your site is setup), extract it and put
it in a folder that PageFeed can access. I suggest the `tmp` folder. The files
you extracted should only be _one level deep_. So, if you extract your twitter
data and it gives you a folder called "someusername-data-1231a", just put that
folder in the `tmp` folder (or wherever). If your data is in a folder _in a folder_
the importer will fail. After all that, run an importer!

## Data importers

All importers are run from the terminal (for now). To run a data importer:

- In a terminal `cd` into the directory where you installed PageFeed.
- Run `bin/cake importer /path/to/data`  where `importer` is the name of an
importer you want to run and `/path/to/data` is, duh, where you put the data for
the network you're trying to import data for.

You can optionally provide a third argument (an email address) to each importer
that will be used as the user to attribute posts, media, and whatever else to.
The email address provided must exist in the database, or the importer will fail.
If no user argument is provided, the first (oldest) user in the database will be used.

### Available Importers

- `import_facebook_data`
- `import_twitter_data`
- `import_instagram_data`

# Homepage

This is what people see when they come to your site, and it's just a list of your
posts in descending order, with some details about you, your friends, and some
photos and videos in sidebars.

# "Feed" page

View updates from your friends on your feed page. PageFeed will connect to your
friends' sites and pull in their posts so you can see them without leaving the
comfort of your own website.

# Posts

Post photos, videos, or blog posts. Posts consist of a name and an optional body
and photo and/or video uploads. The post body field also supports markdown via
[Parsedown](https://github.com/erusev/parsedown).

# Media (Photos and Videos)

Photos and videos are created by making a post. Upload as many photos and videos
as you want to an individual post. You can also configure privacy setting and
album settings when you're uploading media.

# Comments

Allow visitors to your site to leave comments on your posts and media. Comments
require approval before they will appear on to the public.

# Albums

Organize your media into albums for easy viewing.

# Privacy

Posts and media can be made private, so that they will only be listed on your site
to you when you are logged in. Additionally, comments can be disabled for individual
posts and media.

# Sharing

Share things from your feed, or from other PageFeed websites, on your own website
kind of like you share things on regular social networks... Hopefully.

# RSS+JSON Feeds

RSS and JSON feeds of your homepage, media, and albums are available. Additionally,
PageFeed uses RSS and JSON feeds to populate your feed.

# Software/Tech Used

- [Cake PHP](https://www.cakephp.org)
- [MySQL](https://www.mysql.com)
- [FontAwesome Free](https://www.fontawesome.com)
- [Bulma](https://bulma.io)
- [ffmpeg](https://ffmpeg.org/)
- [PHP-FFMpeg](https://github.com/PHP-FFMpeg/PHP-FFMpeg)
- [ImageMagick](https://imagemagick.org/index.php)

# Technical Details

This is nothing fancy. No front end frameworks, no build tools, hell, not even any
CSS preprocessors, and vanilla-as-possible JavaScript. I wanted to make this easy
to pick up and hack, so I tried to keep it as simple as possible. If you are
familiar with [CakePHP](https://www.cakephp.org), then you should be able to get
around the project pretty easily.

# How the "feed" works

It's just RSS. The backend pulls all your feeds, sorts them, and caches them so
you can paginate through them. It will need **a lot** of work, no doubt. Right now,
the only "optimization" is that feeds are cached for like, 5 minutes or something
dumb.

## Frontend files

CSS files live in `webroot/css`. From there, individual files should live in folders
that correspond to the controller, and the CSS filenames should correspond to the
controller action... _Should_.

JS files live in `webroot/js` and, like CSS files, the naming convention should
match up with controller and action names.

JS libraries live in `webroot/js/lib` - no npm or anything yet. Just download
stuff and copy what you need into there. This will probably (most definitely)
change later.

Outside of all that, it should be pretty straightforward to figure out the
logic behind the structure for everything in the `webroot` directory that's
_not_ part of what ships with Cake.

# What else...

- Video thumbnails (with ffmpeg and [php-ffmpeg](https://github.com/PHP-FFMpeg/PHP-FFMpeg))
- Image thumbnails with [ImageMagick](https://imagemagick.org/index.php)
-

# What this doesn't do

- No spam prevention for comments
- No login throttling to prevent someone from trying to brute force your admin account
- No video transcoding. What you upload is what gets sent down the wire.
- No image resizing. Like video, what you upload is what people get.

# TODO

- [ ] Password reset
- [ ] Partial GET for RSS feeds
- [ ] Sharing
- [ ] Editing media details on add/edit post
- [ ] Settings
- [ ] Audio uploads
- [ ] Themes
