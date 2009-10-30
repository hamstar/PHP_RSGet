# PHP_RSGet

PHP_RSGet is an API wrapper for the checkfiles subroutine in the rapidshare API.  It builds URL links to the actual file and returns a link directly to the file itself.  However you will need to be logged in and a premium user to download that file without the countdown timer.

## Usage

### Initialization

Init like this:
    include 'php_rsget.php';
    $rs = new PHP_RSGet;

### Getting a link

You may have a bunch of rapidshare links from RLSLOG or something, when you throw all those links into an array and into the main function it will return to you directly downloadable links (provided you have a premium account).

    // Build the array
    $urls = array(
        'http://rapidshare.com/files/298446242/the.daily.show.2009.10.26.pdtv.xvid-fqm.avi',
        'http://rapidshare.com/files/298532139/The.Colbert.Report.2009.10.26.Cornel.West.PDTV.XviD-FQM.rar'
    );

    // Run the main function passing urls to it
    $links = $rs->getLinks($urls);

    // Print the output
    print_r($links)

This would output the following:

    Array
    (
        [0] => stdClass Object
            (
                [link] => http://rs58tl.rapidshare.com/files/298446242/the.daily.show.2009.10.26.pdtv.xvid-fqm.avi
                [fid] => 298446242
                [fn] => the.daily.show.2009.10.26.pdtv.xvid-fqm.avi
                [size] => 183472450
                [server] => 58
                [status_code] => 1
                [status_msg] => File OK (Downloading possible without any logging)
                [shorthost] => tl
                [md5] => 0
            )

        [1] => stdClass Object
            (
                [link] => http://rs747tl2.rapidshare.com/files/298532139/The.Colbert.Report.2009.10.26.Cornel.West.PDTV.XviD-FQM.rar
                [fid] => 298532139
                [fn] => The.Colbert.Report.2009.10.26.Cornel.West.PDTV.XviD-FQM.rar
                [size] => 183413878
                [server] => 747
                [status_code] => 1
                [status_msg] => File OK (Downloading possible without any logging)
                [shorthost] => tl2
                [md5] => 0
            )

    )

At least at the time the files were still online...
