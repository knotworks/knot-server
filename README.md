# Knot Server

[![Build Status](https://travis-ci.org/knotworks/knot-server.svg?branch=master)](https://travis-ci.org/knotworks/knot-server)

A decentralized server to build private social networks from.

## Background

My Family and I have used an app called [Path](https://path.com) to share private moments for a number of years. It's served us well, however with the [direction it's been going](http://blog.path.com/post/162970476867/explore-the-world-through-path) lately, and the constant worry of it going the way of the dinosaur unexpectedly, I wanted to build something similar as a contigency plan. This repository represents the server-side component that one would self-host to act as the API for their private network, and it's up to you to design and write a client that interfaces with it. Down the road I may release a sample client to get you started as well. Many of the currently supported features exist to mimic much of the functionality of Path, and that's generally the _Path_ (hehe) I'll take. As this project is fully open source, you are more than welcome to add, remove or modify any functionality you wish!

Thanks for checking out Knot!

## Features

### Token-based OAuth authentication

### Multiple post types

- Photo posts
- Text posts
- Location posts (coming soon...)

### Post meta attachments
- Tag friends in your posts
- Add a location to your posts

### Simple profile management
- Supports first name, last name, email, password, and avatar.
- Cover image support coming soon.

### Post comments
Optionally attach the commenter's location to the comment as well.

### Post Reactions
Simple, customizable reactions to a post without the need to comment.


### Friendship Management
- Send and receieve friend requests
- Accept or deny friend requests
- Remove existing friends

### Simple and customizable feed
 A ready-to-go (and easily changeable) feed of posts from the authenticated user and their friends

### Notifications
Receieve notifications when:

- You're added as a friend
- Someone accepts your friend request
- Someone comments on your post
- Someone replies to a comment thread you are a part of
- Someone adds a reaction to one of your posts

All notifications are currently stored in the database, but can be easily updated to go through other channels such as Slack as well.

### Cloud Upload Support
By default all image uploads are uploaded to a cloud provider of your choice. I recommend Amazon S3 or Backblaze B2. You can of course easily swap this out to host all images locally as well.

## Installation
Coming soon...




