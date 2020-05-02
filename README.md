# Gyhper

Streaming tool to integrate animated gifs submitted by your own community!

## Usage

You can use it in any web server environment. Requires PHP 5 at least.

Browser support is wide, but some animations and styles are only working on Chromium (or on Firefox with some flags enabled).

## Directories

### _viewer_

Tool to fetch and view GIFs from the database. Reserved for the streamer. Can be configured using arguments in the URL, and then used in OBS for example in a browser source.

- ``fade``: Duration in milliseconds between two gifs fading. Default 1000ms.

- ``duration``: Duration in milliseconds each gif will be shown. Default 10000ms

- ``limit``: Quantity of gifs to show. Last X gifs in the database will be shown. Default 10.

- ``notext``: Add this flag to hide the bottom text.

- ``font``: Change font used for text. Must be installed on your computer.

- ``fontsize``: Text size. Can be any CSS unit. Use ``em`` for constant size or ``vmax`` / ``vmin`` for font sizes that changes with the width and height of your browser (responsive)

- ``viewport``

  - ``fill`` will resize the gif without changing it's ratio to fill the screen
  
  - ``stretch`` will resize the gif by changing it's width & height independently to fill the screen
  
  - ``center``
  
    - You can add the ``background`` flag to specify a color for the "black bars", or put ``blur`` to use a blurred 
    version of the video. Default is 'transparent' (= can be used in OBS with transparency)
  
- ``inst``: which instance to send and receive gifs from. Default instance is gifs.


### _uploader_

Upload part. Allows your audience to look up gifs in the GIPHY API. Please change the API key if you want to use it for yourself.

### _api_

API which interfaces with the MySQL database. Replace with your credentials to your own database.

## Licensing

(c) defvs and AbstractAmcr - All Rights Reserved

You may not use or distribute this software without my permission, in any situation where you can make money out of it (streaming included if you do that commercially). You are allowed to view and fork the code as per Github's rules, and use it for testing purposes. But you cannot use it for your stream if you earn money out of that.

Please contact me for case-by-case licensing, I may allow limited licenses if you are a streamer who wants to use the software for yourself. I am more than welcome to help you.
