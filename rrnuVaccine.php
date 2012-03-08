<?php
/**
 * Vaccine: Malware rr.nu
 * This simple script will read all file php recursivelly from directory and cleanup string defined by rr.nu
 * 
 * changelog: 
 * v0.2 - verification by Regex, based on idea: http://misc.wordherders.net/wp/wordpress-fix_php.txt
 * v0.1 - single string verification
 * 
 * @author Walker de Alencar <walkeralencar@gmail.com>
 * @link {https://github.com/walkeralencar/rrnuVaccine}
 */
class rrnuVaccine {

    private $directory;
    private $counter;
    private $log = '';
    private $pattern = '(\<\?php \/\*\*\/ eval\(base64_decode\("aWYoZnVuY3Rpb25fZXhpc3RzKCdvYl9zdGFydCcpJiYhaXNzZXQoJF9TRVJWRVJbJ21yX25vJ10pKX.*"\)\);\?\>)';

    private function __construct() {
        
    }

    /**
     * @return rrnuVacine
     */
    public static function create() {
        return new self();
    }

    /**
     * Define root directory to start the recursive search to Vacine all php files.
     * @param type $dir
     * @return rrnuVacine 
     */
    public function setDirectory($dir) {
        $this->directory = $dir;
        return $this;
    }

    private function getDirectory() {
        return $this->directory;
    }

    private function validate() {
        if (is_null($this->getDirectory())) {
            throw new exception('Define the root directory to Vacine!');
        }
    }

    private function startup() {
        $this->counter = array(
            'free' => 0,
            'infected' => 0,
            'disinfected' => 0,
            'total' => 0,
        );
    }

    private function vaccine($directory) {
        $currentDir = dir($directory);

        while (false !== ($entry = $currentDir->read())) {
            $file = $directory . DIRECTORY_SEPARATOR . $entry;

            if ($entry != "." && $entry != ".." && is_dir($file)) {
                $this->vaccine($file);
            } else if (pathinfo($entry, PATHINFO_EXTENSION) == 'php') {
                $fileContent = preg_replace($this->pattern, '', file_get_contents($file),-1,$detected);
                if($detected === 0){
                    $status = '<em style="color:darkblue">free</em>';
                    $this->counter['free']++;
                } else {
                    if (false === file_put_contents($file, $fileContent)) {
                        $status = '<em style="color:darkred">infected!</em>';
                        $this->counter['infected']++;
                    } else {
                        $status = '<em style="color:darkgreen">disinfected!</em>';
                        $this->counter['disinfected']++;
                    }
                }
                $this->counter['total']++;
                $this->log .= $file . "[" . $status . "]<br>\n";
            }
        }
        $currentDir->close();
        
    }

    public function execute() {
        $this->validate();
        $this->startup();
        $this->vaccine($this->getDirectory());
       
        $result = array();
        foreach($this->counter as $key => $value){
            $result[] = "<b>{$key}</b>({$value}) ";
        }

        return "<h2>".implode(' | ',$result)."</h2>\n". $this->log;
    }

}

echo '<div style="color:#333; font-family:Verdana; font-size:11px;">';
echo '<h1><a href="https://github.com/walkeralencar/rrnuVaccine">rr.nu Vaccine - v0.2 Beta</a></h1>';
echo '<h3>by <a href="mailto:walkeralencar@gmail.com">Walker de Alencar</a></h3><hr/>';
echo rrnuVaccine::create()
        ->setDirectory(realpath(getcwd()))
        ->execute();
echo '</div>';