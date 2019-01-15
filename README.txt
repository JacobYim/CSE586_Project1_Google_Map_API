This the directory to store the Junghwan Yim CSE 586 Project1 files.

Directory :

    Version1 : directory for Version1 Web application which do not use the database

        parse1.html is the main html file to access Google and whether apis.

    version2 : directory for Version2 Web application which use the database

        parse2.php is the main server file to manage all system therefore, most of parse.html codes is contained to parse2.php file.
        check.php is the server file to access data base. It is determining whether the current accessing path with departure and destination, and if so, returns JSON file type of the data of the paths containing waypoints’ data, and if not, search data from API like the version 1. 
        Upload.php is uploading the new path and regard information with the path to database
        uploadpoint.php is uploading the new waypoints’ information to database. 