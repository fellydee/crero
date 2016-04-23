<?php
error_reporting(0);

/*** Known bugs
 * 
 * The support for non-ASCII artists, albums, tracks etc is still very partial
 * currently, non-ascii video-album and albums will work
 * non-ascii album blacklisting to prevent physical copies availability, too
 * non-ascii songbooks will work
 * all other non-ASCII datas of your media catalog may fail
 * 
 **** When refering to 'the audio' thereafter, it will means
 * Three time the same audio track, with the same basename, in .flac, .ogg and.mp3 
 * With the following vorbiscomment/id3 tags set
 * Artist
 * Album
 * Title
 * Year
 * 
 ** Please note that the software relies, for sorting feature
 * First, on the Year tag
 * Then, on the modification time of each file on your server
 * 
 * 
 * Note that configuration (.txt) files expect UNIX-style
 * ("\n") end-of-lines. 
 * Windows/Mac OS user please set this in your text editor
 * 
 * 
 * 
 *** Typical deployment is made of 
 * 
 ** -One Clewn API server to host and serve the "free download" audio with
 * -- 'the audio' uloaded in its ./audio subdir
 * -- the api/api.php file at the root of the install
 *			(not ./api.php ! ./api/api.php !)
 * ----please note that in case of DELETION of a free album or track you'll need to 
 * purge the cache by deleting the apicache.php file at the root of your Clewn
 * API server
 * 
 * (you'll have to edit the api.php file to 
 * modify './whatever/path/to/php-getid3'
 * to the path of your actual php-getid3 v 1.x installation(
 * 
 ** -One main backend CreRo with
 * --the d/artists.txt containing a list of the label' artists, one per line
 * -- the $labelhosting, $title, $description, $clewnapiurl 
 * and $clewnaudiurl options in ./config.php 
 * correctly set
 *
 * as well you'll have to edit ./api.php to reflect where php-getid3 is
 * installed
 * 
 * 
 * The "./z" subdir will be used to store the audio that is not available
 * for free download
 * Please note that it is incredibly easy to access it and STEAL your
 * music :)
 * 
 * The ./covers dir will contain cover artworks. 
 * They must be defined in a d/covers.txt file with the folling forma
 * Line 1 : album title
 * Line 2 : filename of the cover
 * Line 3 : album title
 * Line 4 : filename of the album defined at line 3
 * ...
 * 
 * Please note that covers requires to have GD installed. 
 * 
 * the "./songbook" subdir is to contain music sheets (for lyrics, 
 * chord, tablature)... If the basename of an audio file is foo, 
 * CreRo will look for a foo.txt file to be included on the site
 * 
 ***Advanced configuration
 * 
 ** What is the ./network subdir :
 * 
 * if you wish to use the chat feature, you'll have
 * to create two directories named "d" and "e" in the "network"
 * subdir of your install. Don't forget to include a link somewhere on 
 * your page to "./network" for people to find it. 
 * and to edit the "./network/site_variables.php" for site name 
 * description and so on for your chat subsite 
 *
 ** Video support
 * use video/api.php ; put it at the root of your video server
 * then there, put your videos in a <ROOT OF THE INSTALL>/audio subdir
 * same basename for each video file, in the folowing formats 
 * (none of them being mandatory) : avi, mpg, ogv, mp4, webm
 * basename.description.txt
 * basename.album.txt
 * basename.title.txt
 * basename.artist.txt
 * in the same dir, will be used to categorize your vids
 * you can set a downgraded version of any format, that will be
 * provided instead of the original for streaming purpose, by adding 
 * an extra <whatever-meaningful-digit>.basename.format file 
 *  
 ** How to sell physical items
 * 
 * Please look at the example "material_*.txt' files
 * in the d/ subdir. That's with theses files that you'll define
 * your physical copies product line
 * (remember Windows/Mac users, this is UNIX-style end of lines !)
 * 
 * please note that your paypal payment adress will be publicly available 
 * 
 * 
 * TODO : the script.js has several server url that are harcoded in it 
 * that you will have to manually replace to reflect your actual 
 * installation. It should move to a dynamic php file sooner or 
 * later. 
 * 
 * 
 * 
 */


$description = trim(file_get_contents('./d/description.txt'));
$title=trim(file_get_contents('./d/title.txt'));
$server=trim(file_get_contents('./d/server.txt'));;
//change this to your server's domain name
$sitename=trim(file_get_contents('./d/sitename.txt'));
$serverapi='http://'.$server.'/api.php';
//don't change this for typical install
$clewnapiurl=trim(file_get_contents('./d/clewnapiurl.txt'));
//you may change this to http://<your server>/whatever/path/to/free/audio/api.php
$clewnaudiourl=trim(file_get_contents('./d/clewnaudiourl.txt'));
//and this to http://<your server>/whatever/path/to/free/audio/audio/ ; otherwise you can upload your free audio to clewn and use it as free audio media server tier

$videoapiurl=trim(file_get_contents('./d/videoapiurl.txt'));
//same applies for video, with the difference that Clewn Video doesn't currently support public upload
$videourl=trim(file_get_contents('./d/videourl.txt'));

//whatever message you want to display if the HTTP GET message is set to this value, e.g. for advertising campaign or whatever

$message['ad20151127']='Welcome visitor ! We have\'nt got much money to spend on advertising. If you like what you hear, please be kind enough to share broadly';
//will be displayed for http://yourserver.com/?message=ad



$footerhtmlcode=trim(file_get_contents('./d/footerhtmlcode.txt'));

$activateaccountcreation=boolval(trim(file_get_contents('./d/activateaccountcreation.txt'))); 
//there's no true account management for now
//only a quick way to subscribe to label news
//without any kind of mailing list management
//just indicate your contact email adress in d/mailing-list-owner.txt (use an .htaccess if you don't want it to be web-accessible)
//and set this option to true
//then any email address entered in the "subscribe to label blah blah" field will cause a message to be sent to your address
//with the subscriber address in it
//and you can start spamming manually
//good luck

$activatechat=boolval(trim(file_get_contents('./d/activatechat.txt')));
//if set to any other value than false, including null, will enable the chat widget at the bottom of every page
//IMPORTANT : if you enable the chat feature, you'll have to create and *protect from outside access* 
//(with (as example with Apache) an .htaccess restriction file with directive set to deny access from all)
// two directories in <root of your install>/network/ : ./d/ and ./e/
//that's were sensitive data, sur as (if provided by the user) geolocation will be stored
//and you don't want the outside world to access them

$activatestats=boolval(trim(file_get_contents('./d/activatestats.txt')));
//if set to any other value than false, including null, will enable the chat widget at the bottom of every page
//IMPORTANT : if you enable the chat feature, you'll have to create and *protect from outside access* 
//(with (as example with Apache) an .htaccess restriction file with directive set to deny access from all)
// two directories in <root of your install>/admin/ : ./d/ and ./d/stats
//that's were sensitive data will be stored
//and you don't want the outside world to access them



$materialmenu=trim(file_get_contents('./d/materialmenu.txt'));


$materialreleasessalesagreement=trim(file_get_contents('./d/materialreleasessalesagreement.txt'));
//what will be displayed at the bottom of the shop page if the material release eshop is enabled 

?>
