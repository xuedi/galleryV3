## Why another PHP gallery?
This galley makes only sense to use if you use a root-server or a virtual server, and in combination with a file syncing tool like unison. 

This gallery generates everything out of the gallery directory, just a more fancy layout than to allow your webserver to index the directory directly ;-)

## Basic install

Upload the content to your website and create two folders, make cache writeable, that's it

    mkdir cache
    mkdir galleries
    chmod 777 cache

Open that location in your browser and open admin.php, now create a Menu & fileIndex and so on (basicly just rampageClick everything)

## Advanced install

* You might like to add some auth code to admin.php, even tough you cant break anything in there

* Fill out the absolute path in the config and add a cron job to create new thumbnails every 5 min or so

* Sync your local gallery with your online gallery folder, i recommend unison: <http://www.cis.upenn.edu/~bcpierce/unison/index.html>

* Have a web access rule with pass to protect your privacy


## Ingredients

There is a jquery and a lightbox in there (in this case a modified slimbox2: <http://www.digitalia.be/software/slimbox2>)

