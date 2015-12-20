<?php
/**
 * Vaccine: Malware rr.nu
 * This simple script will read all file php recursivelly from directory and cleanup strings like eval(base64_decode) etc.
 *
 * changelog:
 * v0.3 - multiple search patterns (not only rr.nu malware), output optimized for shell scripting use
 * v0.2 - verification by Regex, based on idea: http://misc.wordherders.net/wp/wordpress-fix_php.txt
 * v0.1 - single string verification
 *
 * @author Walker de Alencar <walkeralencar@gmail.com> (0.1, 0.2 - original idea)
 * @link {https://github.com/walkeralencar/rrnuVaccine}
 *
 * @author Emanuele "ToX" Toscano <toss82@gmail.com> (0.3 - more and user-definable search patterns)
 */
namespace rrnuVaccine;

class RrnuVaccine {
    private $directory;
    private $counter;
    private $log = '';
    private $pattern = [
        '(\<\?php eval\(gzinflate\(base64_decode\(.*\)\)\);\?\>)',
        '(\<\?php eval\(base64_decode\(.*\)\);\?\>)'
    ];

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
                foreach ($this->pattern as $pattern) {
                    $fileContent = preg_replace($pattern, '', file_get_contents($file),-1,$detected);
                    if($detected === 0){
                        $status = 'free';
                        $this->counter['free']++;
                        //$this->log .= $file . " [" . $status . "]\n";
                    } else {
                        if (false === file_put_contents($file, $fileContent)) {
                            $status = '########### STILL INFECTED! ###########';
                            $this->counter['infected']++;
                            $this->log .= $file . " [" . $status . "]\n";
                        } else {
                            $status = 'disinfected!';
                            $this->counter['disinfected']++;
                            $this->log .= $file . " [" . $status . "]\n";
                        }
                    }
                }
                $this->counter['total']++;
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
            $result[] = "{$key}: ({$value}) ";
        }
        return implode(' | ',$result)."\n". $this->log;
    }
}

echo RrnuVaccine::create()
        ->setDirectory(realpath(getcwd()))
        ->execute();
