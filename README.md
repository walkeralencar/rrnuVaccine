## Vaccine: Malware rr.nu ##

This simple script will read all file php recursivelly from current directory and cleanup string defined by one or more patterns:

    private $pattern = [
        '(\<\?php eval\(gzinflate\(base64_decode\(.*\)\)\);\?\>)',
        '(\<\?php eval\(base64_decode\(.*\)\);\?\>)'
    ];

## How to use: ##

Put the rrnuVaccine.php file in your base folder, it will scan every file in the current folder and every children folders, and run directly from shell: `sudo php rrnuVaccine.php`

When finished, it will show a report of all infected and cleaned php files.

If a malware is detected, it will silently try to remove the malware string. Be careful with this tool, make a backup first!


Emanuele "ToX" Toscano
