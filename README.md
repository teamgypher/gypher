# Gyhper

Streaming tool to integrate animated gifs submitted by your own community!

## Usage

You can use it in any web server environment. Requires PHP 5 at least.

Browser support is wide, but some animations and styles are only working on Chromium (or on Firefox with some flags enabled).

## Directories

### _viewer_

Tool to fetch and view GIFs from the database. Reserved for the streamer. Can be configured using arguments in the URL, and then used in OBS for example in a browser source.

- ``fade``: Duration in milliseconds between two gifs fading. Default 1000ms.

- ``duration``: Duration in milliseconds each gif will be shown. Default 10000

- ``limit``: Quantity of gifs to show. Last X gifs in the database will be shown. Default 10.

- ``notext``: Add this flag to hide the bottom text.

### _uploader_

Upload part. Allows your audience to look up gifs in the GIPHY API. Please change the API key if you want to use it for yourself.

### _api_

API which interfaces with the MySQL database. Replace with your credentials to your own database.

## Licensing

(c) defvs and AbstractAmcr - All Rights Reserved

You may not use or distribute this software without my permission. You are allowed to view and fork the code as per Github's rules. But you cannot use it for yourself.

Please contact me for case-by-case licensing, I may allow limited licenses if you are a streamer who wants to use the software for himself. I am more than welcome to help you.
