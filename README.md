Eval Channel PHP
================

Sometimes when I write bash scripts things get messy and the
results are cumbersome.

There are a couple of reasons for that:
* There are no anonymous functions that can be passed around.
* Named functions cannot be local.
* Just the way how environment variables work.

So I sometimes switch to writing shell scripts in PHP where on
one side all of those things are not the case and where on the
other side I'm just more at home in general.

That of course comes with the downside of not being able to
manipulate environment variables of the shell the PHP script
was started, not being able to dynamically create aliases
and so on.

This project aims to deal with those issues.

The idea is: In addition to stdout and stderr, the PHP script
could maintain a third output stream where actual bash code
gets passed from PHP to the shell where the shell reads from
this stream and evaluates.

Basically something like `my-php-script.php | xargs eval`.
