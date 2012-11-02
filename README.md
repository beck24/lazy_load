lazy_load
=========

Implements lazy loaded images in elgg

Lazy loading of images is the opposite of pre-loading.  Images are held as a placeholder until
the page has fully rendered, then are filled in dynamically after the page has loaded.
Images in the viewport are loaded first, followed by images up to 200px below the viewport.

As the user scrolls down the page missing images are filled in on an as-needed basis.

This can result in slightly faster page load times, as well as saved bandwidth and server resources
as images aren't served if the user doesn't scroll down the page.


Instructions
============

Install plugin to mods/lazy_load
Enable plugin from the administration plugin page