# Knot Server

[![Build Status](https://travis-ci.org/knotworks/knot-server.svg?branch=master)](https://travis-ci.org/knotworks/knot-server)

A decentralized server to build private social networks from.

## Background

My Family and I used an app called [Path](https://path.com) to share private photos and moments for a number of years. It served us well, but unfortunately went the way of the dinosaur unexpectedly. I wanted to build something similar as a contingency plan, and Knot was the result. This repository represents the server-side component that one would self-host to act as the API for their private network, and it's up to you to design and write a client that interfaces with it. Down the road I may release a sample client to get you started as well. Many of the currently supported features exist to mimic much of the functionality of Path, and that's generally the _Path_ (hehe) I'll take. As this project is fully open source, you are more than welcome to add, remove or modify any functionality you wish!

Thanks for checking out Knot!

## Features

### Token-based OAuth authentication

Knot's authentication system is powered by a [Laravel Passport](https://laravel.com/docs/5.5/passport) password client. Users sign in with a simple email and password, and the server will send back an authentication token to use for all subsequent requests.

### Multiple post types

* Photo posts
* Text posts
* Location posts (coming soon...)

### Post meta attachments

* Tag friends in your posts
* Add a location to your posts

### Simple profile management

* Supports first name, last name, email, password, and avatar.
* Cover image support coming soon.

### Post comments

Optionally attach the commenter's location to the comment as well.

### Post Reactions

Simple, customizable reactions to a post without the need to comment.

### Friendship Management

* Send and receieve friend requests
* Accept or deny friend requests
* Remove existing friends

### Simple and customizable feed

A ready-to-go (and easily changeable) feed of posts from the authenticated user and their friends.

### Notifications

Receieve notifications when:

* You're added as a friend
* Someone accepts your friend request
* Someone comments on your post
* Someone replies to a comment thread you are a part of
* Someone adds a reaction to one of your posts

All notifications are currently stored in the database, but can be easily updated to go through other channels such as Slack as well.

### Cloud Upload Support

By default all image uploads are uploaded to a cloud provider of your choice. I personally recommend Amazon S3 as Knot has the best out-of-the-box support for it. You can of course easily swap this out to host all images locally as well.

## Installation

### Server

* Clone this repo!
* Download and install https://getcomposer.org/download/

    php composer.phar install

* Follow instructions @ https://laravel.com/docs/5.7/homestead to setup homestead
* Edit `Homestead.yaml`, map the knot-server folder, change the hostname of the server to knot-server.test
* In `/etc/hosts` map knot-server.test to the ip listed in Homestead.yaml

    vagrant up

* Login to server: `vagrant ssh`
* Setup database user + db

    mysql

    > CREATE USER 'forge'@'localhost' IDENTIFIED BY '';
    > GRANT ALL PRIVILEGES ON * . * TO 'forge'@'localhost';
    > FLUSH PRIVILEGES;
    > CREATE DATABASE forge;

* Run migrations and install dependencies

    php artisan migrate
      php artisan passport:install

* Turn on debugging in `config/app.php`
* Generate keys

    cp .env.example .env
    php artisan key:generate

* visit http://knot-server.test


### On the client side

* Clone https://github.com/knotworks/knot-client
* Install dependencies

    brew install yarn
    nvm install 11
    yarn install

* Setup a bugsnag account, and grab a key

    export BUGSNAG_KEY=<your key>
    export BASE_URL=http://knot-server.test/
    yarn run dev
